<?php

if (! User::is_valid ()) {
	return;
}

$courses = lemur\Learner::courses ();

echo View::render (
	'lemur/learner/sidebar',
	array (
		'courses' => $courses
	)
);

?>