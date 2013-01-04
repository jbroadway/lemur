<?php

$this->require_admin ();

$page->layout = 'admin';

$c = new lemur\Course ($_GET['id']);
if ($c->error) {
	echo View::render ('lemur/admin/error', $c);
	return;
}

$page->title = __ ('Course Content') . ': ' . $c->title;

$page->add_style ('/apps/lemur/css/admin.css');
$page->add_script ('/apps/lemur/js/admin.js');

echo View::render ('lemur/course/manage', array (
	'course' => $c->id,
	'course_title' => $c->title,
	'pages' => $c->pages ()
));

?>