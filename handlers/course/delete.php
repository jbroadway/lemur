<?php

$this->require_acl ('admin', 'lemur');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	$this->redirect ('/lemur/admin');
}

$page->layout = 'admin';
info ($_POST);

// fetch the course
$c = new lemur\Course ($_POST['id']);
if ($c->error) {
	echo View::render ('lemur/admin/error', $c);
	return;
}

if (! $c->delete ()) {
	echo View::render ('lemur/admin/error', $c);
	return;
}

$this->add_notification (__ ('Course deleted.'));
$this->redirect ('/lemur/admin');

?>