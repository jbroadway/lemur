<?php

$this->require_admin ();

$page->layout = 'admin';
$page->title = __ ('Sort Categories');
$page->add_script ('/apps/lemur/js/jquery.drag-drop.plugin.min.js');

echo $tpl->render (
	'lemur/category/sort',
	array (
		'categories' => lemur\Category::sorted ()
	)
);

?>