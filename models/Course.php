<?php

namespace lemur;

class Course extends \Model {
	public $table = 'lemur_course';

	/**
	 * Get all courses grouped by category, optionally limited by owner.
	 */
	public static function categories ($owner = false) {
		if ($owner) {
			return \DB::fetch ('select
					lemur_course.*, lemur_category.title as category_title
				from
					lemur_course, lemur_category
				where
					lemur_course.category = lemur_category.id and
					lemur_course.owner = ?
				order by
					lemur_category.sorting asc,
					lemur_course.sorting asc',
				$owner
			);
		}
		return \DB::fetch ('select
				lemur_course.*, lemur_category.title as category_title
			from
				lemur_course, lemur_category
			where
				lemur_course.category = lemur_category.id
			order by
				lemur_category.sorting asc,
				lemur_course.sorting asc'
		);
	}
}

?>