<?php

namespace lemur;

/**
 * Custom view filters.
 */
class Filter {
	/**
	 * Filter status number to text.
	 */
	public static function status ($status) {
		return ($status == 1) ? __ ('Draft') : __ ('Published');
	}

	/**
	 * Filter availability number to text.
	 */
	public static function availability ($availability) {
		switch ($availability) {
			case 1:
				return __ ('Private');
			case 2:
				return __ ('Public - Free');
			case 3:
				return __ ('Public - Free w/ registration');
			case 4:
				return __ ('Public - Paid');
		}
	}

	/**
	 * Filter pricing float to money.
	 */
	public static function money ($price) {
		return money_format ('$%i', $price);
	}
}

?>