<?php

$this->require_admin ();

$page->layout = 'admin';

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

try {
	$scorm_modules = scorm\Util::get_modules ();
} catch (Exception $e) {
	$scorm_modules = array ();
}

$page->title = __ ('Editing Page') . ': ' . $p->title;

$this->run ('filemanager/util/browser');

$page->add_style ('/apps/admin/js/redactor/redactor.css');
$page->add_style ('/apps/lemur/js/codemirror/lib/codemirror.css');
$page->add_style ('/apps/lemur/css/admin.css');
$page->add_script ('/apps/lemur/js/waypoints.min.js');
$page->add_script ('/apps/lemur/js/jquery-ui.min.js');
$page->add_script ('/apps/lemur/js/knockout-2.2.0.min.js');
$page->add_script ('/apps/lemur/js/knockout-sortable.min.js');
$page->add_script ('/apps/admin/js/redactor/redactor.min.js');
$page->add_script ('/apps/lemur/js/codemirror/lib/codemirror.js');
$page->add_script ('/apps/lemur/js/codemirror/mode/xml/xml.js');
$page->add_script ('/apps/lemur/js/codemirror/mode/javascript/javascript.js');
$page->add_script ('/apps/lemur/js/codemirror/mode/css/css.js');
$page->add_script ('/apps/lemur/js/codemirror/mode/htmlmixed/htmlmixed.js');
$page->add_script ('/apps/lemur/js/admin.js');
$page->add_script ('/apps/lemur/js/editor.js');

echo View::render (
	'lemur/course/content',
	array (
		'course' => $c->id,
		'page' => $p->id,
		'items' => $p->items (),
		'scorm_modules' => $scorm_modules
	)
);

?>