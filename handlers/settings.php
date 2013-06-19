<?php

$this->require_acl ('admin', 'lemur');

require_once ('apps/admin/lib/Functions.php');

$page->layout = 'admin';
$page->title = __ ('Courses - Settings');

$form = new Form ('post', $this);

$form->data = array (
	'title' => $appconf['Lemur']['title'],
	'public_name' => $appconf['Lemur']['public_name'],
	'layout' => $appconf['Lemur']['layout'],
	'course_layout' => $appconf['Lemur']['course_layout'],
	'comments' => $appconf['Lemur']['comments'],
	'layouts' => admin_get_layouts ()
);

echo $form->handle (function ($form) {
	if (! Ini::write (
		array (
			'Lemur' => array (
				'title' => $_POST['title'],
				'public_name' => $_POST['public_name'],
				'layout' => $_POST['layout'],
				'course_layout' => $_POST['course_layout'],
				'comments' => ($_POST['comments'] === 'yes') ? true : false
			)
		),
		'conf/app.lemur.' . ELEFANT_ENV . '.php'
	)) {
		printf ('<p>%s</p>', __ ('Unable to save changes. Check your folder permissions and try again.'));
		return;
	}

	$form->controller->add_notification (__ ('Settings saved.'));
	$form->controller->redirect ('/lemur/admin');
});

?>