var lemur = (function ($) {
	var self = {};

	/**
	 * Prefix to API URL.
	 */
	self.prefix = '/lemur/api/';

	/**
	 * List of users for userchooser that are already in a course.
	 */
	self.chosen_users = [];

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
	 * Set the chosen user list.
	 */
	self.set_chosen_users = function (learners) {
		self.chosen_users = [];
		for (var i = 0; i < learners.length; i++) {
			self.chosen_users.push (parseInt (learners[i].id));
		}
	};

	/**
	 * Add a learner prompt.
	 */
	self.add_learner = function (e) {
		var course = $(e.target).data ('course');

		$.userchooser ({
			chosen: self.chosen_users,
			callback: function (user, name, email) {
				$.post (self.prefix + 'learner/add', {course: course, user: user}, function (res) {
				});
			}
		});
		return false;
	};

	/**
	 * Remove a learner.
	 */
	self.remove_learner = function (e) {
		var course = $(e.target).data ('course'),
			id = $(e.target).data ('id');

		if (! confirm ('Are you sure you want to remove this learner?')) {
			return false;
		}

		console.log ('removing learner: ' + course + ' - ' + id);

		$.post (self.prefix + 'learner/remove', {course: course, user: id}, function (res) {
			console.log (res);
			if (res.success) {
				$.add_notification ('Learner removed.');
				$('#learner-' + id).remove ();
			} else {
				$.add_notification (res.error);
			}
		});
		return false;
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

	$('.add-learner')
		.click (lemur.add_learner);

	$('.remove-learner')
		.click (lemur.remove_learner);
});
