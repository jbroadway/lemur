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
	$page->add_script ('/apps/lemur/css/items.css');

	$pages = $course->pages ();

	if (! isset ($this->params[2])) {
		foreach ($pages as $id => $title) {
			$this->redirect ($_SERVER['REQUEST_URI'] . '/' . $id . '/' . URLify::filter ($title));
		}

		// show the table of contents
		/*echo View::render (
			'lemur/course/toc',
			array (
				'id' => $course->id,
				'title' => $course->title,
				'pages' => $pages,
			)
		);*/
	}

	if ($this->params[2] === 'contact') {
		// contact the instructor
		$course = $course->orig ();
		$course->pages = $pages;
		echo $this->run ('lemur/course/contact', $course);
		return;
	} elseif ($this->params[2] === 'glossary') {
		// show the glossary
		echo View::render (
			'lemur/course/glossary',
			array (
				'course' => $course->id,
				'course_title' => $course->title,
				'pages' => $pages,
				'glossary' => $course->glossary (),
				'has_glossary' => $course->has_glossary,
				'instructor' => $course->instructor,
				'comments_id' => 'lemur-course-' . $course->id . '-glossary'
			)
		);
		return;
	}

	$pid = $this->params[2];
	if (! isset ($pages[$pid])) {
		echo $this->error (404, __ ('Page not found'), __ ('The page you requested could not be found.'));
		return;
	}

	// build the page body
	$page_body = '';
	$p = new lemur\Page ($pid);
	$items = $p->items ();

	// build a list of input item ids to fetch answers
	$item_ids = array ();
	foreach ($items as $k => $item) {
		if (in_array ((int) $item->type, lemur\Item::$input_types)) {
			$item_ids[] = $item->id;
		}
	}

	// fetch answers for input items
	$answers = lemur\Data::for_items ($item_ids);
	$answers = is_array ($answers) ? $answers : array ();
	foreach ($answers as $answer) {
		foreach ($items as $k => $item) {
			if ($item->id === $answer->item) {
				$items[$k]->answered = (int) $answer->status;
				$items[$k]->answer = $answer->answer;
				$items[$k]->correct = (int) $answer->correct;
				break;
			}
		}
	}

	foreach ($items as $item) {
		if (in_array ((int) $item->type, array (12, 13, 14))) {
			$item->content = explode ("\n", trim ($item->content));
		}
		$page_body .= View::render (
			'lemur/item/' . $item->type,
			$item
		);
	}

	$page->add_script ('/apps/lemur/js/api.js');
	$page->add_script ('/apps/lemur/js/course.js');

	// show a page
	echo View::render (
		'lemur/course/page',
		array (
			'course' => $course->id,
			'pages' => $pages,
			'id' => $pid,
			'title' => $p->title,
			'course_title' => $course->title,
			'page_body' => $page_body,
			'comments_id' => 'lemur-course-' . $course->id . '-' . $pid,
			'has_glossary' => $course->has_glossary,
			'instructor' => $course->instructor
		)
	);

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