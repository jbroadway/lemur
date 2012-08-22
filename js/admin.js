var lemur = {
	/**
	 * Prompt for and save a new category.
	 */
	add_category: function () {
		var category = prompt ('Category name:', '');
		if (category && category.length > 0) {
			window.location.href = '/lemur/category/add?category=' + encodeURIComponent (category);
		}
		return false;
	},
	
	/**
	 * Prompt for and update a category name.
	 */
	edit_category: function (e) {
		var el = $(e.target),
			id = el.data ('id'),
			title = el.data ('title');

		var category = prompt ('Category name:', title);
		if (category && category.length > 0) {
			window.location.href = '/lemur/category/edit?id=' + id + '&category=' + encodeURIComponent (category);
		}
		return false;
	},
	
	/**
	 * Hover change for category titles.
	 */
	on_category: function (e) {
		$(e.target).parent ().addClass ('hovering');
	},
	
	/**
	 * Hover off for category titles.
	 */
	off_category: function (e) {
		$(e.target).parent ().removeClass ('hovering');
	}
};

$(function () {
	/**
	 * Initializations go here.
	 */
	$('.category-title')
		.hover (lemur.on_category, lemur.off_category)
		.click (lemur.edit_category);
});