<?php

namespace lemur;

use Model;

/**
 * Learner input data management belongs here.
 *
 * Fields:
 *
 * - id
 * - course
 * - user
 * - item
 * - status
 * - correct
 * - ts
 * - answer
 * - feedback
 */
class Data extends Model {
	public $table = 'lemur_data';
}

?>