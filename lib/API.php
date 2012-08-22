<?php

namespace lemur;

use DB, Restful, User;

class API extends Restful {
	/**
	 * Update the order of categories.
	 */
	public function post_category_order () {
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
}

?>