<?php

/**
 * Unlock the page or course for editing by others,
 * and redirect to the appropriate screen.
 */

$this->require_acl ('admin', 'lemur');

if (isset ($_GET['page'])) {
	// Unlock a page and go to pages list
	$lock = new Lock ('lemur_page', $_GET['id'] . '/' . $_GET['page']);
	$lock->remove ();
	$this->redirect ('/lemur/course/manage?id=' . $_GET['id']);
} else {
	// Unlock a course and go to courses list
	$lock = new Lock ('lemur_course', $_GET['id']);
	$lock->remove ();
	$this->redirect ('/lemur/admin');
}

?>