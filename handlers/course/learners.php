<?php

$this->require_admin ();

$page->layout = 'admin';

$c = new lemur\Course ($_GET['id']);
if ($c->error) {
	echo View::render ('lemur/admin/error', $c);
	return;
}

$learners = $c->learners ();

$page->title = $c->title . ' - ' . __ ('Learners') . ' (' . count ($learners) . ')';

$page->add_style ('/apps/lemur/css/admin.css');
$page->add_script ('/apps/lemur/js/admin.js');
$page->add_script ('/js/jquery-ui/jquery-ui.min.js');

echo View::render ('lemur/course/learners', array (
	'course' => $c->id,
	'course_title' => $c->title,
	'learners' => $learners
));

?>