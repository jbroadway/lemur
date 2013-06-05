<?php

/**
 * Display the status of a learner within a course.
 */

echo $tpl->render (
	'lemur/status',
	array (
		'status' => lemur\Data::learner_status ($data['course'], User::val ('id'))
	)
);

?>