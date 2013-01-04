var editor = (function ($) {
	var self = {};

	/**
	 * Prefix to API URL.
	 */
	self.prefix = '/lemur/api/';
	
	/**
	 * Is the editor initialized.
	 */
	self.initialized = false;
	
	/**
	 * The ID of the course being edited.
	 */
	self.course = null;
	
	/**
	 * The ID of the page being edited.
	 */
	self.page = null;
	
	/**
	 * The list of items.
	 */
	self.items = null;
	
	/**
	 * List of translatable strings.
	 */
	self.str = {
		delete_confirm: 'Are you sure you want to delete this item?'
	};

	/**
	 * Type names for display.
	 */
	self.type_names = {
		1: 'Text',
		2: 'Image'
	};

	/**
	 * Options for the wysiwyg editor.
	 */
	self.redactor_options = {
		autoresize: false
	};
	
	/**
	 * Whether the items are currently being updated. Prevents
	 * calling update_items() incorrectly.
	 */
	self.updating_items = false;
	
	/**
	 * Initialize the data and settings.
	 */
	self.init = function (options) {
		self.items = self.make_items_observable (options.items);
		self.str = options.str;
		self.type_names = options.type_names;
		self.course = options.course;
		self.page = options.page;

		ko.bindingHandlers.sortableList = {
			init: function (element, valueAccessor) {
				var list = valueAccessor ();
				$(element).sortable ({
					update: function (event, ui) {
						// get the item data
						var item = ui.item[0];
						console.log (ui.item);

						// figure out its new position
						var position = ko.utils.arrayIndexOf (ui.item.parent ().children (), ui.item[0]);
						
						// start updating the items
						self.updating_items = true;

						// remove the item and add it back in the right spot
						if (position >= 0) {
							list.remove (item);
							list.splice (position, 0, item);
						}
						
						// done updating items
						self.updating_items = false;
						
						// save the changes
						self.update_items ();
					}
				});
			}
		};

		ko.applyBindings (self);
		self.initialized = true;
		self.show_full ();
	};

	/**
	 * Make an observable item from a regular one.
	 */
	self.make_item_observable = function (item) {
		var i = {
			id: item.id,
			title: ko.observable (item.title),
			page: item.page,
			sorting: ko.observable (item.sorting),
			type: item.type,
			content: ko.observable (item.content)
		};

		i.template = ko.computed (function () {
			return self.template_name (this);
		}, i);

		i.item_id = ko.computed (function () {
			return 'item-' + this.id;
		}, i);

		type_name = ko.computed (function () {
			return self.type_name (this);
		}, i);

		return i;
	};

	/**
	 * Turns the items into an observableArray whose properties are also observable.
	 */
	self.make_items_observable = function (data) {
		var items = ko.observableArray ();
		for (var i = 0; i < data.length; i++) {
			items.push (self.make_item_observable (data[i]));
		};
		return items;
	};

	/**
	 * Get an item from the list by its ID.
	 */
	self.get_item = function (id) {
		console.log (id);
		console.log (self.items);
		console.log (editor.items);
		for (var i = 0; i < self.items.length; i++) {
			console.log (self.items[i]);
			if (self.items[i].id == id) {
				return self.items[i];
			}
		}
		return false;
	};

	/**
	 * Determine the template ID for an item type.
	 */	
	self.template_name = function (item) {
		if (item && item.type) {
			return 'tpl-item-' + item.type;
		} else if (item) {
			return 'tpl-item=' + item;
		}
	};

	self.type_name = function (item) {
		if (item && item.type) {
			return self.type_names[item.type];
		} else if (item) {
			return self.type_names[item];
		}
	};

	/**
	 * Delete an item from the list.
	 */
	self.delete_item = function (item) {
		if (confirm (self.str.delete_confirm)) {
			self.items.remove (item);
			return self.update_items ();
		}
		return false;
	};

	/**
	 * Save the updated items to the server.
	 */
	self.update_items = function () {
		if (! self.initialized) {
			return;
		}
		
		if (self.updating_items) {
			return;
		}

		self.updating_items = true;
		self.show_saving ();
		$.post (self.prefix + 'item/update_all', {items: ko.toJSON (self.items)}, function (res) {
			self.updating_items = false;
			self.done_saving ();
			if (! res.success) {
				$.add_notification (res.error);
			}
		});
	};

	/**
	 * Get the next sorting value.
	 */
	self.next = function () {
		var sorting = 0;
		for (var i = 0; i < self.items.length; i++) {
			if (sorting < self.items[i].sorting ()) {
				sorting = self.items[i].sorting ();
			}
		}
		return sorting + 1;
	}

	/**
	 * Add an item on the server so it has an ID value.
	 */
	self.create_blank_item = function (type) {
		var item = {
			title: '',
			page: self.page,
			sorting: self.next (),
			type: type,
			content: ''
		};
		$.post (self.prefix + 'item/create', item, function (res) {
			if (! res.success) {
				$.add_notification (res.error);
				return;
			}
			self.items.push (self.make_item_observable (res.data));
			if (res.data.type == 1) {
				$('.wysiwyg').redactor (self.redactor_options);
			}
			self.focus_last_item ();
		});
		return false;
	};

	/**
	 * Methods for adding fields to the list.
	 */
	self.add_text_field = function () {
		return self.create_blank_item (1);
	};

	/**
	 * Set the current drag item.
	 */
	self.select_item = function (item) {
		self.selected_item (item);
	};

	/**
	 * The current drag item.
	 */
	self.selected_item = ko.observable ();

	/**
	 * Put the last item into focus.
	 */
	self.focus_last_item = function () {
		var last_item = $('#item-list .item:last');

		$('html, body').animate ({
			scrollTop: last_item.offset ().top
		}, 500);

		last_item.find ('input:first').focus ();
	};

	/**
	 * Disable "Done Editing" link and show "Saving..." message.
	 */
	self.show_saving = function () {
		$('#saving').fadeIn ('slow');
		$('#done-editing').addClass ('disabled').on ('click', function (e) {
			e.preventDefault ();
			if ($(this).hasClass ('disabled')) {
				return false;
			}
			window.location.href = $(this).attr ('href');
		});
	};

	/**
	 * Re-enable "Done Editing" link and hide "Saving..." message.
	 */
	self.done_saving = function () {
		$('#done-editing').removeClass ('disabled');
		$('#saving').fadeOut ('slow');
	};

	/**
	 * Show the list tab.
	 */
	self.show_list = function () {
		$('#toggle-full').removeClass ('active');
		$('#toggle-list').addClass ('active');
		$('#item-list-full').hide ();
		$('#item-list-list').show ();
		return false;
	};

	/**
	 * Show the list tab.
	 */
	self.show_full = function () {
		$('#toggle-full').addClass ('active');
		$('#toggle-list').removeClass ('active');
		$('#item-list-full').show ();
		$('#item-list-list').hide ();
		return false;
	};
	
	return self;
})(jQuery);
