<?php

/**
 * Duplicate a course.
 */

$this->require_acl ('admin', 'lemur');

$page->layout = 'admin';

// fetch the course
$c = new lemur\Course ($_GET['id']);
if ($c->error) {
	echo View::render ('lemur/admin/error', $c);
	return;
}

DB::beginTransaction ();

$data = $c->orig ();
unset ($data->id);
$data->title .= ' (copy)';
$data->status = 1;
$data->sorting = $c->next ('sorting');
$c2 = new lemur\Course ($data);

if (! $c2->put ()) {
	DB::rollback ();
	echo View::render ('lemur/admin/error', $c2);
	return;
}

$pages = lemur\Page::query ()
	->where ('course', $c->id)
	->fetch_orig ();

foreach ($pages as $page) {
	unset ($page->id);
	$page->course = $c2->id;
	$p = new lemur\Page ($page);

	if (! $p->put ()) {
		DB::rollback ();
		echo View::render ('lemur/admin/error', $p);
		return;
	}

	$items = lemur\Item::query ()
		->where ('page', $page->id)
		->fetch_orig ();

	foreach ($items as $item) {
		unset ($item->id);
		$item->page = $p->id;
		$item->course = $c2->id;
		$i = new lemur\Item ($item);
		
		if (! $i->put ()) {
			DB::rollback ();
			echo View::render ('lemur/admin/error', $i);
			return;
		}
	}
}

DB::commit ();

$this->add_notification (__ ('Course duplicated.'));
$this->redirect ('/lemur/admin');

?>