<?php

namespace lemur\API;

use DB, Restful, User;

class Category extends Restful {
	/**
	 * Update the order of categories.
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
				'update lemur_category set sorting = ? where id = ?',
				$n + 1,
				$id
			)) {
				DB::execute ('rollback');
				return $this->error (DB::error ());
			}
		}
		DB::execute ('commit');

		return true;
	}

	/**
	 * Update the name of a category.
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

		$cat = new \lemur\Category ($_POST['id']);
		$cat->title = $_POST['title'];
		
		if (! $cat->put ()) {
			return $this->error (__ ('An error occurred.'));
		}

		return __ ('Category updated.');
	}
}

?>