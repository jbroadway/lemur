<?php

namespace lemur;

use DB;
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

	public static function remove_user ($course, $user) {
		return DB::execute (
			'delete from lemur_data
			where course = ?
			and user = ?',
			$course,
			$user
		);
	}

	public static function for_course ($course) {
		return DB::pairs (
			'select user, (sum(status) * 100) / count() as progress
			from lemur_data where course = ? group by user',
			$course
		);
	}
}

?>