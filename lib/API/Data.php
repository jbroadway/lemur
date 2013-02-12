<?php

namespace lemur\API;

use DB;
use Restful;
use User;

class Data extends Restful {
	/**
	 * Handle an answer submission for a learner.
	 * Returns whether the answer was correct or not,
	 * or undetermined if it could not automatically
	 * tell.
	 */
	public function post_submit ($id) {
		if (! User::is_valid ()) {
			return $this->error (__ ('Must be logged in first.'));
		}
		
		$data = \lemur\Data::query ()
			->where ('user', User::val ('id'))
			->where ('item', $id)
			->single ();

		if ($data->error) {
			return $this->error (__ ('Question not found.'));
		}
		
		if ($data->status == 1) {
			return $this->error (__ ('Question already answered.'));
		}

		$item = new \lemur\Item ($data->item);
		
		$data->answer = trim ($_POST['answer']);
		$data->status = 1;
		$data->ts = gmdate ('Y-m-d H:i:s');

		if (! empty ($item->answer)) {
			if ($data->answer == $item->answer) {
				$data->correct = 1;
				$correct = 'yes';
			} else {
				$data->correct = -1;
				$correct = 'no';
			}
		} else {
			$data->correct = 0;
			$correct = 'undetermined';
		}
		$data->put ();

		if ($data->error) {
			error_log ('Error saving answer: ' . $data->error);
			return $this->error (__ ('An unknown error occurred.'));
		}
		
		return array ('correct' => $correct);
	}
}

?>