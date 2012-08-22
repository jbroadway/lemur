<?php

$this->require_admin ();

if (! isset ($_GET['id']) || ! is_numeric ($_GET['id'])) {
	$this->redirect ('/lemur/admin');
}

if (! isset ($_GET['category']) || empty ($_GET['category'])) {
	$this->redirect ('/lemur/admin');
}

$cat = new lemur\Category ($_GET['id']);
$cat->title = $_GET['category'];

if (! $cat->put ()) {
	$this->add_notification ('An error occurred.');
} else {
	$this->add_notification ('Category updated.');
}

$this->redirect ('/lemur/admin');

?>