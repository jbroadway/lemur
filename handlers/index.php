<?php

$page->title = __ ($appconf['Lemur']['public_name']);
$page->layout = $appconf['Lemur']['layout'];
$page->add_script ('/apps/lemur/css/default.css');

// fetch sorted categories
$categories = lemur\Category::sorted ();

// fetch and sort courses by category (published, any owner)
$courses = lemur\Course::categories (false, true);
if (count ($courses) === 0) {
	printf ('<p>%s</p>', __ ('No courses available at this time.'));
	return;
}

foreach (array_keys ($categories) as $k) {
	$categories[$k] = (object) array (
		'id' => $k,
		'title' => $categories[$k],
		'courses' => array (),
		'course_count' => 0
	);
}
foreach (array_keys ($courses) as $k) {
	$categories[$courses[$k]->category]->courses[] = $courses[$k];
	$categories[$courses[$k]->category]->course_count++;
}

echo $tpl->render (
	'lemur/index',
	array (
		'categories' => $categories
	)
);

?>