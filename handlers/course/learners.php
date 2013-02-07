<?php

$this->require_admin ();

$page->layout = 'admin';

$c = new lemur\Course ($_GET['id']);
if ($c->error) {
	echo View::render ('lemur/admin/error', $c);
	return;
}

$learners = $c->learners ();

$progress = lemur\Data::for_course ($c->id);
foreach ($learners as $k => $learner) {
	$learners[$k]->progress = isset ($progress[$learner->id])
		? ceil ($progress[$learner->id])
		: 0;
}

$page->title = $c->title . ' - ' . __ ('Learners') . ' (<span id="learner-count">' . count ($learners) . '</span>)';

$page->add_style ('/apps/lemur/css/admin.css');
$page->add_script ('/apps/lemur/js/handlebars.js');
$page->add_script ('/apps/lemur/js/admin.js');
$page->add_script (I18n::export (
	'Learner removed.',
	'Learner added.',
	'Are you sure you want to remove this learner from the course?'
));

echo View::render ('lemur/course/learners', array (
	'course' => $c->id,
	'course_title' => $c->title,
	'learners' => $learners
));

?>