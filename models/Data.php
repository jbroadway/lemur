<?php

namespace lemur;

use DB;
use Model;
use User;

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

	public static function for_learner ($course, $user) {
		return DB::fetch (
			'select * from lemur_data
			where course = ? and user = ?',
			$course,
			$user
		);
	}

	public static function for_items ($items, $user = false) {
		$user = $user ? $user : User::val ('id');
		return DB::fetch (
			'select * from lemur_data
			where item in(' . join (',', $items) . ')
			and user = ?',
			$user
		);
	}

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

	public static function learner_status ($course, $user = false) {
		$user = $user ? $user : User::val ('id');
		return DB::shift (
			'select (sum(status) * 100) / count() from lemur_data
			where course = ? and user = ?',
			$course,
			$user
		);
	}
}

?>