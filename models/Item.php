<?php

namespace lemur;

class Item extends \Model {
	public $table = 'lemur_item';

	const TEXT       = 1;
	const IMAGE      = 2;
	const VIDEO      = 3;
	const HTML       = 4;
	const FORMATTED  = 5;
	const SCORM      = 6;
	const FILE       = 7;
	const ACCORDION  = 8;
	const DEFINITION = 9;
	const TEXT_INPUT = 10;
	const PARA_INPUT = 11;
	const DROP_DOWN  = 12;
	const RADIO      = 13;
	const CHECKBOXES = 14;
	const AUDIO      = 15;
	const SECTION    = 16;
	const QUIZ       = 17;
	const UPLOAD     = 18;

	public static $input_types = array (10, 11, 12, 13, 14, 18);

	public static $all_types = array (1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18);

	public static function get_inputs ($course) {
		return self::query ()
			->where ('course', $course)
			->where ('type in(' . join (', ', self::$input_types) . ')')
			->fetch_orig ();
	}

	public static function total_inputs ($course) {
		global $cache;

		$total = $cache->get ('lemur:' . $course . ':inputs');

		if ($total === false || $total === null) {
			$total = self::query ()
				->where ('course', $course)
				->where ('type in(' . join (', ', self::$input_types) . ')')
				->count ();

			$cache->set ('lemur:' . $course . ':inputs', $total);
		}

		return $total;
	}

	public static function is_input ($type) {
		return in_array ((int) $type, self::$input_types);
	}
}

?>