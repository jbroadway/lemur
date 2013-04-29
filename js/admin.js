var lemur = (function ($) {
	var self = {};

	/**
	 * Prefix to API URL.
	 */
	self.prefix = '/lemur/api/';

	/**
	 * List of templates to render.
	 */
	self.tpl = {};

	/**
	 * Current course.
	 */
	self.course = 0;

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
	 * Set the user list on first loading the learners page.
	 */
	self.set_learners = function (learners) {
		self.learners = learners;
		self.redraw_learners ();
	};

	/**
	 * Redraw the learner list when a user has been added.
	 */
	self.redraw_learners = function (sort) {
		var list = $('#learner-list').html ('');

		if (sort) {
			// new user added, sort them first.
			self.learners.sort (function (a, b) {
				if (typeof b === 'boolean') {
					return -1;
				}

				var a_name = a.name.toLowerCase (),
					b_name = b.name.toLowerCase ();
				
				if (a_name < b_name) {
					return -1;
				} else if (a_name > b_name) {
					return 1;
				}
				return 0;
			});
		}

		for (var i = 0; i < self.learners.length; i++) {
			self.learners[i].course = self.course;
			list.append (self.tpl.learner (self.learners[i]));
			$('#progress-' + self.learners[i].id).css ({width: self.learners[i].progress + '%'});
			if (parseInt (self.learners[i].progress) === 100) {
				$('#status-' + self.learners[i].id).html ($.i18n ('Complete'));
			}
		}

		$('#learner-count').text (self.learners.length);
	};

	/**
	 * Fetch a list of existing users for the userchooser.
	 */
	self.chosen_users = function () {
		var chosen_users = [];
		for (var i = 0; i < self.learners.length; i++) {
			chosen_users.push (parseInt (self.learners[i].id));
		}
		return chosen_users;
	};

	/**
	 * Add a learner prompt.
	 */
	self.add_learner = function (e) {
		var course = $(e.target).data ('course');

		$.userchooser ({
			chosen: self.chosen_users (),
			callback: function (user, name, email) {
				$.post (self.prefix + 'learner/add', {course: course, user: user}, function (res) {
					if (res.success) {
						$.add_notification ($.i18n ('Learner added.'));
						self.learners.push ({
							id: user,
							name: name,
							email: email,
							progress: 0
						});
						self.redraw_learners (true);
					} else {
						$.add_notification (res.error);
					}
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

		if (! confirm ($.i18n ('Are you sure you want to remove this learner from the course?'))) {
			return false;
		}

		$.post (self.prefix + 'learner/remove', {course: course, user: id}, function (res) {
			if (res.success) {
				$.add_notification ($.i18n ('Learner removed.'));
				$('#learner-' + id).remove ();
				for (var i = 0; i < self.learners.length; i++) {
					if (self.learners[i].id == id) {
						self.learners.splice (i, 1);
						$('#learner-count').text (self.learners.length);
						break;
					}
				}
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

	$('#learners')
		.on ('click', '.remove-learner', lemur.remove_learner);
});
