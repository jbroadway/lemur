<?php

namespace lemur;

use DB;
use Model;

class Course extends Model {
	public $table = 'lemur_course';

	/**
	 * Get all courses grouped by category, optionally limited by owner.
	 */
	public static function categories ($owner = false, $published = false) {
		$sql = 'select
					lemur_course.*, lemur_category.title as category_title
				from
					lemur_course, lemur_category
				where
					lemur_course.category = lemur_category.id';

		if ($owner) {
			$sql .= ' and lemur_course.owner = ?';
		}

		if ($published) {
			$sql .= ' and lemur_course.status = 2';
		}

		$sql .= ' order by
					lemur_category.sorting asc,
					lemur_course.sorting asc';

		if ($owner) {
			return DB::fetch ($sql, $owner);
		}
		return DB::fetch ($sql);
	}

	/**
	 * Get an associative array of pages for the current course.
	 */
	public function pages () {
		return DB::pairs (
			'select id, title from lemur_page where course = ? order by sorting asc',
			$this->id
		);
	}
}

?>