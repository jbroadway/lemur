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
	 * The temp image for image items.
	 */
	self.default_img_src = '/apps/lemur/pix/default.png';
	
	/**
	 * List of translatable strings.
	 */
	self.str = {
		delete_confirm: 'Are you sure you want to delete this item?',
		untitled: 'Untitled'
	};

	/**
	 * Type names for display.
	 */
	self.type_names = {
		1: 'Text',
		2: 'Image',
		3: 'Video',
		4: 'HTML code',
		5: 'Pre-formatted text',
		6: 'SCORM module',
		7: 'File download',
		8: 'Accordion',
		9: 'Definition',
		10: 'Single-line answer',
		11: 'Paragraph answer',
		12: 'Drop down',
		13: 'Checkboxes',
		14: 'Multiple choice'
	};

	/**
	 * Options for the wysiwyg editor.
	 */
	self.redactor_options = {
		autoresize: false,
		buttons: [
			'html', '|', 'formatting', '|', 'bold', 'italic', 'deleted', '|',
			'unorderedlist', 'orderedlist', 'outdent', 'indent', '|',
			'table', 'link', '|', 'fontcolor', 'backcolor', '|',
			'alignment', 'horizontalrule'
		]
	};
	
	/**
	 * Whether the items are currently being updated. Prevents
	 * calling update_items() incorrectly.
	 */
	self.updating_items = false;
	
	/**
	 * Manages CodeMirror instances by item ID.
	 */
	self.codemirror_instances = [];
	
	/**
	 * A list of SCORM modules available to the SCORM item type.
	 */
	self.scorm_modules = [];
	
	/**
	 * Initialize the data and settings.
	 */
	self.init = function (options) {
		self.items = self.make_items_observable (options.items);
		self.str = options.str;
		self.type_names = options.type_names;
		self.course = options.course;
		self.page = options.page;
		self.scorm_modules = options.scorm_modules;

		ko.bindingHandlers.sortable.afterMove = self.sortable_update;

		ko.applyBindings (self);
		self.initialized = true;

		// register event handlers
		$('.main').on ('blur', '.redactor_editor', self.wysiwyg_update);

		self.show_full ();
	};

	/**
	 * Initialize or reinitialize plugins (wysiwyg editor, syntax highlighter, etc.).
	 */
	self.initialize_plugins = function () {
		$('.wysiwyg').redactor (self.redactor_options);

		$('.html').each (function () {
			var id = $(this).data ('id');
			if (id in self.codemirror_instances) {
				self.codemirror_instances[id].toTextArea ();
			}
			self.codemirror_instances[id] = CodeMirror.fromTextArea (this, {
				mode: 'text/html',
				lineNumbers: true,
				indentWithTabs: true
			});
			self.codemirror_instances[id].on ('blur', self.codemirror_update);
		});

		$('.pre').each (function () {
			var id = $(this).data ('id');
			if (id in self.codemirror_instances) {
				self.codemirror_instances[id].toTextArea ();
			}
			self.codemirror_instances[id] = CodeMirror.fromTextArea (this, {
				mode: 'text/plain',
				lineNumbers: true,
				indentWithTabs: true
			});
			self.codemirror_instances[id].on ('blur', self.codemirror_update);
		});
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

		/**
		 * The name of this item, or Untitled.
		 */
		i.sortable_title = ko.computed (function () {
			if (this.title () === '') {
				return self.str.untitled;
			}
			return this.title ();
		}, i);

		/**
		 * Which template to call for this item type.
		 */
		i.template = ko.computed (function () {
			return self.template_name (this);
		}, i);

		/**
		 * Get the display name for an item type.
		 * Accepts an item object or a type number.
		 */
		i.type_name = ko.computed (function () {
			return self.type_names[this.type];
		}, i);

		/**
		 * The image to display, or a temp image.
		 */
		i.img_src = ko.computed (function () {
			if (this.content () === '') {
				return self.default_img_src;
			}
			return this.content ();
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
	 * Determine the template ID for an item type.
	 * Accepts an item object or a type number.
	 */	
	self.template_name = function (item) {
		if (item && item.type) {
			return 'tpl-item-' + item.type;
		} else if (item) {
			return 'tpl-item=' + item;
		}
	};

	/**
	 * Delete an item from the list.
	 */
	self.delete_item = function () {
		var item = this;

		if (confirm (self.str.delete_confirm)) {
			self.updating_items = true;
			self.show_saving ();

			$.post (self.prefix + 'item/delete', {item: item.id}, function (res) {
				self.updating_items = false;
				self.done_saving ();

				if (! res.success) {
					$.add_notification (res.error);
				} else {
					if (self.codemirror_instances[item.id]) {
						self.codemirror_instances.splice (item.id, 1);
					}
					self.items.remove (item);
				}
			});
		}

		return false;
	};

	/**
	 * Save the updated items to the server.
	 */
	self.update_items = function () {
		if (! self.initialized) {
			return false;
		}
		
		if (self.updating_items) {
			return false;
		}

		self.updating_items = true;
		self.show_saving ();

		var items = ko.toJS (self.items);
		for (var i = 0; i < items.length; i++) {
			items[i].sorting = i + 1;
		}

		$.post (self.prefix + 'item/update_all', {items: items}, function (res) {
			self.updating_items = false;
			self.done_saving ();

			if (! res.success) {
				$.add_notification (res.error);
			}
		});

		return false;
	};

	/**
	 * Update items after re-initializing editor from
	 * sorting items..
	 */
	self.sortable_update = function () {
		self.update_items ();
	};

	/**
	 * Update items after wysiwyg editor loses focus,
	 * then call editor.update_items().
	 */
	self.wysiwyg_update = function () {
		$('.wysiwyg').each (function () {
			var id = $(this).data ('id'),
				html = $(this).getCode ();

			for (var i in self.items ()) {
				if (self.items ()[i].id == id) {
					self.items ()[i].content (html);
					break;
				}
			}
		});

		self.update_items ();
	};

	/**
	 * Update items after codemirror editor loses focus,
	 * then call editor.update_items().
	 */
	self.codemirror_update = function (cm) {
		var textarea = cm.getTextArea (),
			id = $(textarea).data ('id'),
			html = cm.getValue ();
		
		for (var i in self.items ()) {
			if (self.items ()[i].id == id) {
				self.items ()[i].content (html);
				break;
			}
		}

		self.update_items ();
	};

	/**
	 * Callback for the file browser.
	 */
	self.filemanager_image = function () {
		var item = this;
		$.wysiwyg.fileManager.setAjaxHandler ('//' + window.location.host + '/filemanager/embed');
		$.wysiwyg.fileManager.init (function (file) {
			item.content (file);
			self.update_items ();
		});
	};

	/**
	 * Callback for the file browser.
	 */
	self.filemanager_video = function () {
		var item = this;
		$.wysiwyg.fileManager.setAjaxHandler ('//' + window.location.host + '/filemanager/embed');
		$.wysiwyg.fileManager.init (function (file) {
			item.content (file);
			self.update_items ();
		});
	};

	/**
	 * Callback for the file browser.
	 */
	self.filemanager_file = function () {
		var item = this;
		$.wysiwyg.fileManager.setAjaxHandler ('//' + window.location.host + '/filemanager/embed');
		$.wysiwyg.fileManager.init (function (file) {
			item.content (file);
			self.update_items ();
		});
	};

	/**
	 * Get the next sorting value.
	 */
	self.next = function () {
		var sorting = 0;
		for (var i in self.items ()) {
			var current = parseInt (self.items ()[i].sorting ());
			if (sorting < current) {
				sorting = current;
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
			self.initialize_plugins ();
			self.focus_last_item ();
		});
		return false;
	};

	/**
	 * Add an item by type.
	 */
	self.add_text_field			= function () { return self.create_blank_item (1); };
	self.add_image_field		= function () { return self.create_blank_item (2); };
	self.add_video_field		= function () { return self.create_blank_item (3); };
	self.add_html_field			= function () { return self.create_blank_item (4); };
	self.add_pre_field			= function () { return self.create_blank_item (5); };
	self.add_scorm_field		= function () { return self.create_blank_item (6); };
	self.add_file_field			= function () { return self.create_blank_item (7); };
	//self.add_accordion_field	= function () { return self.create_blank_item (8); };
	self.add_definition_field	= function () { return self.create_blank_item (9); };
	self.add_input_field		= function () { return self.create_blank_item (10); };
	self.add_para_field			= function () { return self.create_blank_item (11); };

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

		// init/re-init plugins
		self.initialize_plugins ();
		return false;
	};
	
	return self;
})(jQuery);
