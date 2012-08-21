<?php

namespace lemur;

/**
 * Custom form validation rules go here.
 */
class Rules {
	/**
	 * Verifies the price is set if availability = paid.
	 */
	public static function price_is_set ($price) {
		if ($_POST['availability'] != 4) {
			return true;
		}
		if (trim ($price) === '') {
			return false;
		}
		if (! is_numeric ($price)) {
			return false;
		}
		return true;
	}
}

?>