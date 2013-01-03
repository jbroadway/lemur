<?php

$this->require_admin ();

$page->layout = 'admin';

$c = new lemur\Course ($_GET['id']);
if ($c->error) {
	echo View::render ('lemur/admin/error', $c);
	return;
}

$p = new lemur\Page ($_GET['page']);
if ($p->error) {
	echo View::render ('lemur/admin/error', $p);
	return;
}

$page->title = __ ('Editing Page') . ': ' . $p->title;

echo View::render (
	'lemur/course/content',
	array (
		'course' => $c->id,
		'page' => $p->id
	)
);

?>