<?php

$page->layout = 'admin';

$this->require_admin ();

$page->title = __ ('Add Course');

$form = new Form ('post', $this);

$instructor = $_POST['instructor']
	? new User ($_POST['instructor'])
	: (object) array ('name' => __ ('None'));

$form->data = array (
	'categories' => lemur\Category::sorted (),
	'category' => $_GET['category'],
	'instructor_name' => $instructor->name
);

echo $form->handle (function ($form) {
	unset ($_POST['_token_']);

	$c = new lemur\Course ();
	$_POST['created'] = gmdate ('Y-m-d H:i:s');
	$_POST['owner'] = 0;
	$_POST['sorting'] = $c->next ('sorting');
	$_POST['status'] = 1;
	$_POST['has_glossary'] = 0;

	$c = new lemur\Course ($_POST);
	if (! $c->put ()) {
		echo View::render ('lemur/admin/error', $c);
		return;
	}
	$form->controller->add_notification (__ ('Course added.'));
	$form->controller->redirect ('/lemur/course/manage?id=' . $c->id);
});

?>