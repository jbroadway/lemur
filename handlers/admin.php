<?php

$page->layout = 'admin';

$this->require_acl ('admin', 'lemur');

$page->title = __ ('Courses');

// fetch sorted categories
$categories = lemur\Category::sorted ();

// fetch and sort courses by category
$courses = lemur\Course::categories ();
foreach (array_keys ($categories) as $k) {
	$categories[$k] = (object) array (
		'id' => $k,
		'title' => $categories[$k],
		'courses' => array ()
	);
}
foreach (array_keys ($courses) as $k) {
	$categories[$courses[$k]->category]->courses[] = $courses[$k];
}

$page->add_style ('/apps/lemur/css/admin.css');
$page->add_script ('/apps/lemur/js/admin.js');

echo $tpl->render ('lemur/admin', array (
	'categories' => $categories
));

?>