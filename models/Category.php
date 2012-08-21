<?php

namespace lemur;

class Category extends \Model {
	public $table = 'lemur_category';

	/**
	 * Get a sorted list of categories, optionally limited by owner.
	 */
	public static function sorted ($owner = false) {
		if ($owner) {
			return self::query ()
				->where ('owner', $owner)
				->order ('sorting', 'asc')
				->fetch_assoc ('id', 'title');
		}
		return self::query (array ('id', 'title'))
			->order ('sorting', 'asc')
			->fetch_assoc ('id', 'title');
	}
}

?>