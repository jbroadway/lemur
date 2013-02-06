<?php

namespace lemur\API;

use DB, Restful, User;

class Item extends Restful {
	/**
	 * Update all items for a page.
	 */
	public function post_update_all () {
		if (! User::require_admin ()) {
			return $this->error (__ ('Admin access required.'));
		}

		foreach ($_POST['items'] as $item) {
			$i = new \lemur\Item ($item['id']);
			if ($i->error) {
				return $this->error ($i->error);
			}

			$i->title = isset ($item['title']) ? $item['title'] : '';
			$i->sorting = $item['sorting'];
			$i->content = $item['content'];
			$i->answer = isset ($item['answer']) ? $item['answer'] : '';

			if (! $i->put ()) {
				return $this->error ($i->error);
			}
		}
		return true;
	}

	/**
	 * Create a new item.
	 */
	public function post_create () {
		if (! User::require_admin ()) {
			return $this->error (__ ('Admin access required.'));
		}

		$_POST['title'] = isset ($_POST['title']) ? $_POST['title'] : '';
		$_POST['answer'] = isset ($_POST['answer']) ? $_POST['answer'] : '';

		$p = new \lemur\Page ($_POST['page']);

		$i = new \lemur\Item (array (
			'title' => $_POST['title'],
			'page' => $_POST['page'],
			'sorting' => $_POST['sorting'],
			'type' => $_POST['type'],
			'content' => $_POST['content'],
			'answer' => $_POST['answer'],
			'course' => $p->course
		));

		if (! $i->put ()) {
			return $this->error ($i->error);
		}

		// Enable glossary for definitions
		if ($_POST['type'] == \lemur\Item::DEFINITION) {
			$c = new \lemur\Course ($p->course);
			$c->has_glossary = 1;
			$c->put ();
		}

		return $i->orig ();
	}

	/**
	 * Delete an item.
	 */
	public function post_delete () {
		if (! User::require_admin ()) {
			return $this->error (__ ('Admin access required.'));
		}
		
		$i = new \lemur\Item ($_POST['item']);
		$course = $i->course;

		if ($i->error) {
			return $this->error ($i->error);
		}

		if (! $i->remove ()) {
			return $this->error ($i->error);
		}

		// Disable glossary if last definition
		if (! DB::shift (
			'select count(*) from lemur_item where course = ? and type = ?',
			$course,
			\lemur\Item::DEFINITION
		)) {
			$c = new \lemur\Course ($course);
			$c->has_glossary = 0;
			$c->put ();
		}

		return true;
	}
}

?>