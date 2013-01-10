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

			$i->title = $item['title'];
			$i->sorting = $item['sorting'];
			$i->content = $item['content'];

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

		$i = new \lemur\Item (array (
			'title' => $_POST['title'],
			'page' => $_POST['page'],
			'sorting' => $_POST['sorting'],
			'type' => $_POST['type'],
			'content' => $_POST['content']
		));

		if (! $i->put ()) {
			return $this->error ($i->error);
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
		if ($i->error) {
			return $this->error ($i->error);
		}

		if (! $i->remove ()) {
			return $this->error ($i->error);
		}

		return true;
	}
}

?>