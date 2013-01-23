<?php

$this->require_admin ();

$page->layout = 'admin';

$c = new lemur\Course ($_GET['id']);
if ($c->error) {
	echo View::render ('lemur/admin/error', $c);
	return;
}

$page->title = $c->title . ' - ' . __ ('Sort Pages');

$page->add_style ('/apps/lemur/css/admin.css');
$page->add_script ('/apps/lemur/js/admin.js');
$page->add_script ('/apps/lemur/js/jquery.drag-drop.plugin.min.js');

echo View::render ('lemur/course/sort', array (
	'course' => $c->id,
	'pages' => $c->pages ()
));

?>