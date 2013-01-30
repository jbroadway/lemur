<?php

namespace lemur\API;

use Restful;

class Learner extends Restful {
	/**
	 * Add a learner to a course.
	 */
	public function post_add () {
		if (! User::require_admin ()) {
			return $this->error (__ ('Admin access required.'));
		}

		if (! isset ($_POST['course'])) {
			return $this->error (__ ('Missing parameter: course'));
		}

		if (! isset ($_POST['user'])) {
			return $this->error (__ ('Missing parameter: user'));
		}

		$res = lemur\Learner::add_to_course ($_POST['course'], $_POST['user']);
		if (! $res) {
			error_log (DB::error ());
			return $this->error (__ ('Failed to add learner.'));
		}
		return true;
	}

	/**
	 * Remove a learner from a course.
	 */
	public function post_remove () {
		if (! User::require_admin ()) {
			return $this->error (__ ('Admin access required.'));
		}

		if (! isset ($_POST['course'])) {
			return $this->error (__ ('Missing parameter: course'));
		}

		if (! isset ($_POST['user'])) {
			return $this->error (__ ('Missing parameter: user'));
		}

		$res = lemur\Learner::remove_from_course ($_POST['course'], $_POST['user']);
		if (! $res) {
			error_log (DB::error ());
			return $this->error (__ ('Failed to remove learner.'));
		}
		return true;
	}
}

?>