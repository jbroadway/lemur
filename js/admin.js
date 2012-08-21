var lemur = {
	add_category: function () {
		var category = prompt ('Category name:', '');
		if (category.length > 0) {
			window.location.href = '/lemur/category/add?category=' + encodeURIComponent (category);
		}
		return false;
	},
	
	edit_category: function (el) {
		var id = $(el.target).data ('id');
		console.log ('click: ' + id);
	},
	
	on_category: function (el) {
		var id = $(el).data ('id');
		console.log ('on: ' + id);
	},
	
	off_category: function (el) {
		var id = $(el).data ('id');
		console.log ('off: ' + id);
	}
};

$(function () {
	$('.category')
		.hover (lemur.on_category, lemur.off_category)
		.click (lemur.edit_category);
});
