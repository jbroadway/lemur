<?php

$this->require_acl ('admin', 'lemur');

$page->layout = false;

$c = new lemur\Course ($_GET['id']);
if ($c->error) {
	echo View::render ('lemur/admin/error', $c);
	return;
}

$p = new lemur\Page ($_GET['page']);
if ($p->error) {
	echo View::render ('lemur/admin/error', $p);
	return;
}

$page->add_script ('/apps/lemur/js/preview.js');

printf ('<h1>%s</h1>', $p->title);

$items = $p->items ();

$quiz = false;
foreach ($items as $item) {
	// combine inputs for quiz
	if ($item->type == lemur\Item::QUIZ) {
		$quiz = true;
	} elseif (lemur\Item::is_input ($item->type)) {
		$item->quiz = $quiz;
	} elseif ($quiz) {
		echo View::render ('lemur/item/end_quiz', array ('answered' => false));
	}

	// split options for choice fields
	if (in_array ((int) $item->type, array (lemur\Item::DROP_DOWN, lemur\Item::RADIO, lemur\Item::CHECKBOXES))) {
		$item->content = explode ("\n", trim ($item->content));
	}

	echo View::render (
		'lemur/item/' . $item->type,
		$item
	);
}

// close quiz if still open
if ($quiz) {
	echo View::render ('lemur/item/end_quiz', array ('answered' => false));
}

?>