<?php

$this->require_acl ('admin', 'lemur');

$page->layout = 'admin';

$c = new lemur\Course ($_GET['id']);
if ($c->error) {
	echo View::render ('lemur/admin/error', $c);
	return;
}

$page->title = $c->title . ' - ' . __ ('Pages');

$page->add_style ('/apps/lemur/css/admin.css');
$page->add_script ('/apps/lemur/js/admin.js');
$page->add_script ('/js/jquery-ui/jquery-ui.min.js');

echo View::render ('lemur/course/manage', array (
	'course' => $c->id,
	'course_title' => $c->title,
	'pages' => $c->pages ()
));

?>