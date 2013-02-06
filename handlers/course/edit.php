<?php

$page->layout = 'admin';

$this->require_admin ();

$c = new lemur\Course ($_GET['id']);
if ($c->error) {
	echo View::render ('lemur/admin/error', $c);
	return;
}

$page->title = $c->title . ' - ' . __ ('Settings');

$form = new Form ('post', $this);

$instructor = $c->instructor
	? new User ($c->instructor)
	: (object) array ('name' => __ ('None'));

$form->data = $c->orig ();
$form->data->categories = lemur\Category::sorted ();
$form->data->instructor_name = $instructor->name;

echo $form->handle (function ($form) {
	unset ($_POST['_token_']);

	$c = new lemur\Course ($_GET['id']);
	$c->title = $_POST['title'];
	$c->thumb = $_POST['thumb'];
	$c->summary = $_POST['summary'];
	$c->category = $_POST['category'];
	$c->availability = $_POST['availability'];
	$c->price = $_POST['price'];
	$c->status = $_POST['status'];
	$c->instructor = $_POST['instructor'];

	if (! $c->put ()) {
		echo View::render ('lemur/admin/error', $c);
		return;
	}
	$form->controller->add_notification (__ ('Course settings saved.'));
	$form->controller->redirect ('/lemur/admin');
});

?>