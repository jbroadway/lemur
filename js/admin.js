var lemur = (function ($) {
	var self = {};

	/**
	 * Prefix to API URL.
	 */
	self.prefix = '/lemur/api/';

	/**
	 * Prompt for and save a new category.
	 */
	self.add_category = function (msg) {
		var category = prompt (msg, '');
		if (category && category.length > 0) {
			window.location.href = '/lemur/category/add?category=' + encodeURIComponent (category);
		}
		return false;
	};
	
	/**
	 * Prompt for and update a category name.
	 */
	self.edit_category = function (e) {
		var el = $(e.target),
			id = el.data ('id'),
			title = el.data ('title');

		var category = prompt ('Category name:', title);
		if (category && category.length > 0) {
			// update ui immediately
			el.data ('title', category).html (category);

			$.post (self.prefix + 'category/update', {id: id, title: category}, function (res) {
				if (res.success) {
					$.add_notification (res.data);
				} else {
					$.add_notification (res.error);

					// rollback on failure
					el.data ('title', title).html (title);
				}
			});
		}
		return false;
	};
	
	/**
	 * Hover change for category titles.
	 */
	self.on_category = function (e) {
		$(e.target).parent ().addClass ('hovering');
	};
	
	/**
	 * Hover off for category titles.
	 */
	self.off_category = function (e) {
		$(e.target).parent ().removeClass ('hovering');
	};

	/**
	 * Prompt for and save a new page.
	 */
	self.add_page = function (course, msg) {
		var page = prompt (msg, '');
		if (page && page.length > 0) {
			window.location.href = '/lemur/page/add?course=' + course + '&page=' + encodeURIComponent (page);
		}
		return false;
	};
	
	/**
	 * Prompt for and update a page name.
	 */
	self.edit_page = function (e) {
		var el = $(e.target),
			id = el.data ('id'),
			title = el.data ('title'),
			tel = $('#page-title-' + id);

		var page = prompt ('Page name:', title);
		if (page && page.length > 0) {
			// update ui immediately
			el.data ('title', page);
			tel.html (page);

			$.post (self.prefix + 'page/update', {id: id, title: page}, function (res) {
				if (res.success) {
					$.add_notification (res.data);
				} else {
					$.add_notification (res.error);

					// rollback on failure
					el.data ('title', title);
					tel.html (title);
				}
			});
		}
		return false;
	};
	
	/**
	 * Hover change for page titles.
	 */
	self.on_page = function (e) {
		$(e.target).parent ().addClass ('hovering');
	};
	
	/**
	 * Hover off for page titles.
	 */
	self.off_page = function (e) {
		$(e.target).parent ().removeClass ('hovering');
	};

	/**
	 * Update an element with a message that will fade out.
	 * Usage: lemur.notice ('#saving', 'Saving...');
	 */
	self.notice = function (id, msg) {
		$(id).stop (true).html (msg).show ().delay (1500).fadeOut (300);
	};

	return self;
})(jQuery);

$(function () {
	/**
	 * Initializations go here.
	 */
	$('.category-title')
		.hover (lemur.on_category, lemur.off_category)
		.click (lemur.edit_category);

	$('.page-rename')
		.click (lemur.edit_page);
});
