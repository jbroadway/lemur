<?php

if (! $this->internal) die;

if (strpos ($data['identifier'], 'lemur-course-') !== 0) {
	return;
}

$tail = str_replace ('lemur-course-', '', $data['identifier']);
list ($id, $pg) = explode ('-', $tail);

$course = new lemur\Course ((int) $id);
if ($course->error || ! $course->instructor) {
	return;
}

$instructor = new User ($course->instructor);
if (! $instructor) {
	return;
}

try {
	Mailer::send (array (
		'to' => array ($instructor->email, $instructor->name),
		'subject' => 'New comment in "' . $course->title . '"',
		'text' => 'The following comment has been posted to your course:'
			. "\n\n"
			. $data['name'] . ': ' . Template::sanitize ($data['comment'])
			. "\n\n"
			. 'Click here to reply to this comment:'
			. "\n\n"
			. sprintf (
				'http://%s/course/%d/%s/%d',
				$_SERVER['HTTP_HOST'],
				$course->id,
				URLify::filter ($course->title),
				$pg
			)
	));
} catch (Exception $e) {
	// email failed
}

?>