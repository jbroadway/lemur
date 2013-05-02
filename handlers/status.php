<?php

/**
 * Display the status of a learner within a course.
 */

echo lemur\Data::learner_status ($data['course'], User::val ('id')) . '%';

?>