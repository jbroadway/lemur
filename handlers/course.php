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

// Was this course published yet?
if ((int) $course->status < 2) {
	echo $this->error (404, __ ('Course not found'), __ ('The course you requested could not be found.'));
	return;
}

if ((int) $course->availability === 2 || (User::is_valid () && lemur\Learner::in_course ($cid))) {
	// free or already registered, show the course
	$page->title = $course->title;
	$page->layout = $appconf['Lemur']['layout'];
	$page->add_script ('/apps/lemur/css/default.css');

	$pages = $course->pages ();

	if (isset ($this->params[2])) {
		$pid = $this->params[2];
		if (! isset ($pages[$pid])) {
			echo $this->error (404, __ ('Page not found'), __ ('The page you requested could not be found.'));
			return;
		}

		// build the page body
		$page_body = '';
		$p = new lemur\Page ($pid);
		$items = $p->items ();

		foreach ($items as $item) {
			if (in_array ((int) $item->type, array (12, 13, 14))) {
				$item->content = explode ("\n", trim ($item->content));
			}
			$page_body .= View::render (
				'lemur/item/' . $item->type,
				$item
			);
		}

		// show a page
		echo View::render (
			'lemur/course/page',
			array (
				'course' => $course->id,
				'pages' => $pages,
				'id' => $pid,
				'title' => $p->title,
				'page_body' => $page_body
			)
		);
	} else {
		// show the table of contents
		echo View::render (
			'lemur/course/toc',
			array (
				'id' => $course->id,
				'title' => $course->title,
				'pages' => $pages,
			)
		);
	}

	return;
}

switch ((int) $course->availability) {
	case 1:
		// private
		echo $this->error (404, __ ('Course not found'), __ ('The course you requested could not be found.'));
		return;

	case 3:
		// free w/ registration, show summary and login/join link
		$page->title = $course->title;
		$page->layout = $appconf['Lemur']['layout'];
		$page->add_script ('/apps/lemur/css/default.css');
		echo View::render ('lemur/course/summary', $course);

		if (User::is_valid ()) {
			// show join link
		} else {
			// show login form
		}
		return;

	case 4:
		// paid, show summary and login/purchase link
		$page->title = $course->title;
		$page->layout = $appconf['Lemur']['layout'];
		$page->add_script ('/apps/lemur/css/default.css');
		echo View::render ('lemur/course/summary', $course);

		if (User::is_valid ()) {
			// show purchase link
		} else {
			// show login form
		}
		return;
}

?>