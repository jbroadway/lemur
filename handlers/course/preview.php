<?php

$this->require_admin ();

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

printf ('<h1>%s</h1>', $p->title);

$items = $p->items ();

foreach ($items as $item) {
	echo View::render (
		'lemur/item/' . $item->type,
		$item
	);
}

?>