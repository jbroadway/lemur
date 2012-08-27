<?php

$this->require_admin ();

if (! isset ($_GET['id']) || ! is_numeric ($_GET['id'])) {
	$this->redirect ('/lemur/admin');
}

$cat = new lemur\Category ($_GET['id']);

if (! $cat->remove ()) {
	$this->add_notification ('An error occurred.');
} else {
	$this->add_notification ('Category deleted.');
}

$this->redirect ('/lemur/admin');

?>