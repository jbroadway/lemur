<?php

namespace lemur\API;

use DB;
use Mailer;
use Restful;
use User;
use Validator;
use View;

class Data extends Restful {
	/**
	 * Handle an answer submission for a learner.
	 * Returns whether the answer was correct or not,
	 * or undetermined if it could not automatically
	 * tell.
	 */
	public function post_submit ($id) {
		if (! User::require_login ()) {
			return $this->error (__ ('Unauthorized.'));
		}

		// handle multiple answers at once
		if (isset ($_POST['quiz'])) {
			if (! isset ($_POST['answers']) || ! is_array ($_POST['answers'])) {
				return $this->error (__ ('Invalid request data.'));
			}

			$quiz = $id;
			$out = array ();

			foreach ($_POST['answers'] as $id => $answer) {
				$item = new \lemur\Item ($id);
				if ($item->error) {
					return $this->error (__ ('Question not found.'));
				}

				$data = \lemur\Data::query ()
					->where ('user', User::val ('id'))
					->where ('item', $id)
					->single ();

				if (! $data or $data->error) {
					$data = new \lemur\Data (array (
						'course' => $item->course,
						'user' => User::val ('id'),
						'item' => $id,
						'status' => 0,
						'correct' => 0,
						'ts' => gmdate ('Y-m-d H:i:s'),
						'answer' => '',
						'feedback' => ''
					));
				}

				if ($data->status == 1) {
					return $this->error (__ ('Question already answered.'));
				}

				$data->answer = trim ($answer);
				$data->status = 1;
				$data->ts = gmdate ('Y-m-d H:i:s');

				if ($item->answer !== '') {
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

				$out[] = array (
					'item' => $id,
					'correct' => $correct,
					'answer' => $data->answer
				);
			}

			return array ('correct' => $out);
		}

		$item = new \lemur\Item ($id);
		if ($item->error) {
			return $this->error (__ ('Question not found.'));
		}

		$data = \lemur\Data::query ()
			->where ('user', User::val ('id'))
			->where ('item', $id)
			->single ();

		if (! $data or $data->error) {
			$data = new \lemur\Data (array (
				'course' => $item->course,
				'user' => User::val ('id'),
				'item' => $id,
				'status' => 0,
				'correct' => 0,
				'ts' => gmdate ('Y-m-d H:i:s'),
				'answer' => '',
				'feedback' => ''
			));
		}

		if ($data->status == 1) {
			return $this->error (__ ('Question already answered.'));
		}

		$data->answer = trim ($_POST['answer']);
		$data->status = 1;
		$data->ts = gmdate ('Y-m-d H:i:s');

		if ($item->answer !== '') {
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

		if ($correct === 'no') {
			return array (
				'correct' => $correct,
				'answer' => $item->answer
			);
		}
		return array ('correct' => $correct);
	}

	/**
	 * Save feedback on learner data and send an email notification
	 * with the feedback to the learner.
	 */
	public function post_feedback () {
		if (! $this->require_acl ('admin', 'lemur')) {
			return $this->error (__ ('Unauthorized.'));
		}
		
		$failed = Validator::validate_list (
			$_POST,
			array (
				'course' => array ('type' => 'numeric'),
				'user' => array ('type' => 'numeric'),
				'input' => array ('type' => 'numeric'),
				'feedback' => array ('not empty' => 1)
			)
		);
		if (count ($failed) > 0) {
			return $this->error (__ ('Missing or invalid parameters') . ': ' . join (', ', $failed));
		}

		$data = new \lemur\Data ($_POST['input']);
		if ($data->error) {
			return $this->error (__ ('Question not found.'));
		}

		if ($data->course != $_POST['course'] || $data->user != $_POST['user']) {
			return $this->error (__ ('Incorrect request data.'));
		}
		
		if ($data->feedback !== '') {
			return $this->error (__ ('Feedback already given.'));
		}
		
		$data->feedback = $_POST['feedback'];
		if (! $data->put ()) {
			error_log ('Error saving feedback: ' . $data->error);
			return $this->error (__ ('An unknown error occurred.'));
		}

		try {
			$u = new User ($data->user);

			Mailer::send (array (
				'to' => array ($u->email, $u->name),
				'subject' => 'Instructor feedback received',
				'text' => View::render ('lemur/email/feedback', array (
					'user' => $u,
					'course' => new \lemur\Course ($data->course),
					'answer' => $data
				))
			));
		} catch (Exception $e) {
			error_log ('Mail error: ' . $e->getMessage ());
		}

		return true;
	}
}

?>