<?php

/**
 * Email the instructor of a course.
 */

$this->require_login ();

if (! $data['instructor']) {
	echo $this->error (404, __ ('Page not found'), __ ('The page you requested could not be found.'));
	return;
}

$page->title = $data['title'];

$form = new Form ('post', $this);

$form->data = $data;

echo $form->handle (function ($form) use ($data, $page) {
	$u = new User ($data['instructor']);
	try {
		Mailer::send (array (
			'to' => array ($u->email, $u->name),
			'reply_to' => array (User::val ('email'), User::val ('name')),
			'subject' => '[' . __ ('Learner Contact Form') . '] ' . $data['title'],
			'text' => $_POST['message']
		));
	} catch (Exception $e) {
		@error_log ('Email failed (lemur/course/contact)');
		$form->data['error'] = __ ('Failed to send email. Please try again later.');
		return false;
	}
	echo View::render (
		'lemur/course/contact_sent',
		$form->data
	);
});

?>