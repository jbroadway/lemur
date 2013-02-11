<?php

namespace lemur;

use DB;
use Model;

class Course extends Model {
	public $table = 'lemur_course';

	/**
	 * Delete a course and all its associated data.
	 */
	public function delete () {
		DB::beginTransaction ();
		DB::execute ('delete from lemur_data where course = ?', $this->id);
		DB::execute ('delete from lemur_learner where course = ?', $this->id);
		DB::execute ('delete from lemur_item where course = ?', $this->id);
		DB::execute ('delete from lemur_page where course = ?', $this->id);
		DB::execute ('delete from lemur_course where id = ?', $this->id);
		$this->error = DB::error ();
		if ($this->error) {
			DB::rollback ();
			return false;
		}
		return DB::commit ();
	}

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
			$sql .= ' and lemur_course.status = 2 and lemur_course.availability > 1';
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

	/**
	 * Fetches a list of learners in a course. Returns their user
	 * ID, name, and email.
	 */
	public function learners () {
		return DB::fetch (
			'select
				#prefix#user.id as id,
				#prefix#user.name as name,
				#prefix#user.email as email
			from
				#prefix#user, lemur_learner
			where
				lemur_learner.course = ? and
				lemur_learner.user = #prefix#user.id
			order by
				#prefix#user.name asc',
			$this->id
		);
	}

	/**
	 * Fetch all words and their definitions for this course.
	 */
	public function glossary () {
		return DB::pairs (
			'select title, content
			from lemur_item
			where course = ? and type = ?
			order by title asc',
			$this->id,
			Item::DEFINITION
		);
	}
}

?>