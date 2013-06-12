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

if (User::is_valid ()) {
	$is_instructor = ($course->instructor == User::val ('id'));
	$is_learner = (! $instructor) ? lemur\Learner::in_course ($cid) : false;
} else {
	$is_instructor = false;
	$is_learner = false;
}

if (((int) $course->availability === 2 && $_SERVER['REQUEST_METHOD'] === 'GET') || $is_instructor || $is_learner) {
	// free or already registered, show the course
	$page->title = $course->title;
	$page->layout = $appconf['Lemur']['layout'];
	$page->add_script ('/apps/lemur/css/default.css');
	$page->add_script ('/apps/lemur/css/items.css');

	$pages = $course->pages ();

	if (! isset ($this->params[2])) {
		foreach ($pages as $id => $title) {
			$this->redirect (preg_replace ('|^/lemur|', '', $_SERVER['REQUEST_URI']) . '/' . $id . '/' . URLify::filter ($title));
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
		$course->is_instructor = $is_instructor;
		$course->is_learner = $is_learner;
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
				'is_instructor' => $is_instructor,
				'is_learner' => $is_learner,
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
	$sections = array ();

	// build a list of input item ids to fetch answers
	$item_ids = array ();
	foreach ($items as $k => $item) {
		if (in_array ((int) $item->type, lemur\Item::$input_types)) {
			$item_ids[] = $item->id;
		} elseif ($item->type == 3) { // video
			$page->add_style ('/apps/lemur/css/video-js.min.css');
			$page->add_script ('/apps/lemur/js/video.min.js');
		} elseif ($item->type == 15) { // audio
			$page->add_style ('<style>.audiojs audio{display:none;}</style>');
			$page->add_script ('/apps/lemur/js/audiojs/audio.min.js');
			$page->add_script ('<script>audiojs.events.ready(function(){audiojs.createAll();});</script>');
		} elseif ($item->type == 16) { // section
			$sections['section-' . URLify::filter ($item->title)] = $item->title;
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
		if ($course->instructor == User::val ('id')) {
			$item->instructor = true;
		}
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
			'instructor' => $course->instructor,
			'is_instructor' => $is_instructor,
			'is_learner' => $is_learner,
			'has_sections' => count ($sections),
			'sections' => $sections
		)
	);

	return;
}

switch ((int) $course->availability) {
	case 1:
		// private, show login or 404
		
		// show login form
		if (! User::is_valid ()) {
			$this->redirect ('/user/login?redirect=' . urlencode ($_SERVER['REQUEST_URI']));
		}

		// still no access, show 404
		echo $this->error (404, __ ('Course not found'), __ ('The course you requested could not be found.'));
		return;

	case 2:
	case 3:
		// free or free w/ registration, show summary and login/join link
		
		// show login form
		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			if (! User::is_valid ()) {
				$this->redirect ('/user/login?redirect=' . urlencode ($_SERVER['REQUEST_URI']));
			}

			// add learner
			if ($_POST['subscribe'] == 1) {
				$res = lemur\Learner::add_to_course ($course->id, User::val ('id'));
				if (! $res) {
					error_log (DB::error ());
					echo $this->error (404, __ ('An error occurred'), __ ('There was an error in the course registration. Please try again later.'));
					return;
				}

				// reload to show course
				$this->redirect ($_SERVER['REQUEST_URI']);
				return;
			}
		}
		
		// show summary
		$page->title = $course->title;
		$page->layout = $appconf['Lemur']['layout'];
		$page->add_script ('/apps/lemur/css/default.css');
		echo View::render ('lemur/course/summary', $course);
		return;

	case 4:
		// paid, show summary and login/purchase link
		$page->title = $course->title;
		$page->layout = $appconf['Lemur']['layout'];
		$page->add_script ('/apps/lemur/css/default.css');
		echo View::render ('lemur/course/summary', $course);

		// show login form
		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			if (! User::is_valid ()) {
				$this->redirect ('/user/login?redirect=' . urlencode ($_SERVER['REQUEST_URI']));
			}

			// TODO: show pay wall
		}

		return;
}

?>