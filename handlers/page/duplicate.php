<?php

/**
 * Duplicate a page.
 */

$this->require_acl ('admin', 'lemur');

$page->layout = 'admin';

// fetch the page
$p = new lemur\Page ($_GET['page']);
if ($p->error) {
	echo View::render ('lemur/admin/error', $p);
	return;
}

if ($p->course != $_GET['id']) {
	echo View::render (
		'lemur/admin/error',
		array ('error' => __ ('Page and course ID do not match.'))
	);
}

DB::beginTransaction ();

$data = $p->orig ();
unset ($data->id);
$data->title .= ' (copy)';
$data->sorting = $p->next ('sorting');
$p2 = new lemur\Page ($data);

if (! $p2->put ()) {
	DB::rollback ();
	echo View::render ('lemur/admin/error', $p2);
	return;
}

$old_page_id = $p->id;
$new_page_id = $p2->id;
$course_id = $p->course;
unset ($p);
unset ($p2);
unset ($data);

$items = lemur\Item::query ()
	->where ('page', $old_page_id)
	->fetch_orig ();

for ($k = 0; $k < count ($items); $k++) {
	$id = $items[$k]->id;
	$data = $items[$k];
	unset ($data->id);
	$data->page = $new_page_id;
	$data->course = $course_id;
	$i = new lemur\Item ($data);
	
	if (! $i->put ()) {
		DB::rollback ();
		echo View::render ('lemur/admin/error', $i);
		return;
	}

	unset ($i);
	unset ($data);
	$items[$k] = null;
}

unset ($items);

DB::commit ();

$this->add_notification (__ ('Page duplicated.'));
$this->redirect ('/lemur/course/manage?id=' . $_GET['id']);

?>