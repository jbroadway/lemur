<?php

$this->require_admin ();

if (! isset ($_GET['category']) || empty ($_GET['category'])) {
	$this->redirect ('/lemur/admin');
}

$cat = new lemur\Category;
$cat->owner = 0;
$cat->title = $_GET['category'];
$cat->sorting = $cat->next ('sorting');

if (! $cat->put ()) {
	$this->add_notification ('An error occurred');
} else {
	$this->add_notification ('Category added');
}

$this->redirect ('/lemur/admin');

?>