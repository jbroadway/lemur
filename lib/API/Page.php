<?php

namespace lemur\API;

use DB, Restful, User;

class Page extends Restful {
	/**
	 * Update the order of pages for a course.
	 */
	public function post_order () {
		if (! User::require_admin ()) {
			return $this->error (__ ('Admin access required.'));
		}

		if (! is_array ($_POST['order'])) {
			return $this->error (__ ('Invalid field: order'));
		}

		DB::execute ('begin');
		foreach ($_POST['order'] as $n => $id) {
			if (! DB::execute (
				'update lemur_page set sorting = ? where id = ? and course = ?',
				$n + 1,
				$id,
				$_POST['course']
			)) {
				DB::execute ('rollback');
				return $this->error (DB::error ());
			}
		}
		DB::execute ('commit');

		return true;
	}

	/**
	 * Update the name of a page.
	 */
	public function post_update () {
		if (! User::require_admin ()) {
			return $this->error (__ ('Admin access required.'));
		}

		if (! isset ($_POST['id']) || ! is_numeric ($_POST['id'])) {
			return $this->error (__ ('Invalid field: id'));
		}

		if (! isset ($_POST['title']) || empty ($_POST['title'])) {
			return $this->error (__ ('Invalid field: title'));
		}

		$pg = new \lemur\Page ($_POST['id']);
		$pg->title = $_POST['title'];
		
		if (! $pg->put ()) {
			return $this->error (__ ('An error occurred.'));
		}

		return __ ('Category updated.');
	}
}

?>