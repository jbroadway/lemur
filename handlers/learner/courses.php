<?php

$this->require_login ();

$courses = lemur\Learner::courses ();

echo View::render (
	'lemur/learner/courses',
	array (
		'courses' => $courses
	)
);

?>