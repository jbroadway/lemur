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

	public static $input_types = array (10, 11, 12, 13, 14);

	public static $all_types = array (1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15);

	public static function get_inputs ($course) {
		return self::query ()
			->where ('course', $course)
			->where ('type in(' . join (', ', self::$input_types) . ')')
			->fetch_orig ();
	}
}

?>