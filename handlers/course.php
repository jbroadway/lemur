<?php

if (count ($this->params) === 0) {
	echo $this->error (500, __ ('No course specified'), __ ('The link you requested is invalid.'));
	return;
}

$cid = $this->params[0];
$course = new lemur\Course ($cid);
if ($course->error) {
	echo $this->error (404, __ ('Course not found'), __ ('The course you requested could not be found.'));
	return;
}

$pages = $course->pages ();

if (isset ($this->params[2])) {
	$pid = $this->params[2];
	if (! isset ($pages[$pid])) {
		echo $this->error (404, __ ('Page not found'), __ ('The page you requested could not be found.'));
		return;
	}
}

$page->title = $course->title;

echo View::render (
	'lemur/course',
	array (
		'id' => $course->id,
		'title' => $course->title,
		'pages' => $pages,
		'page_id' => isset ($pid) ? $pid : 0,
		'page_title' => isset ($pid) ? $pages[$pid] : ''
	)
);

?>