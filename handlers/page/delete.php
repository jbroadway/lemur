<?php

$this->require_acl ('admin', 'lemur');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	$this->redirect ('/lemur/admin');
}

if (! isset ($_POST['id']) || ! is_numeric ($_POST['id'])) {
	$this->redirect ('/lemur/admin');
}

if (! isset ($_POST['course']) || ! is_numeric ($_POST['course'])) {
	$this->redirect ('/lemur/admin');
}

$pg = new lemur\Page ($_POST['id']);

if ($pg->error) {
	$this->redirect ('/lemur/admin');
}

if ($pg->course !== $_POST['course']) {
	$this->redirect ('/lemur/admin');
}

if (! $pg->remove ()) {
	$this->add_notification ('An error occurred.');
} else {
	$this->add_notification ('Page deleted.');
}

$this->redirect ('/lemur/course/manage?id=' . $_POST['course']);

?>