<?php

namespace lemur;

/**
 * General helper methods.
 */
class Lemur {
	/**
	 * Call this from Elefant's bootstrap.php file so that
	 * links to `/course/` map to `/lemur/course/` and
	 * links to `/courses` map to `/lemur/courses`.
	 *
	 * Usage:
	 *
	 *     lemur\Lemur::bootstrap ();
	 */
	public static function bootstrap () {
		if (preg_match ('|^/courses?/?|', $_SERVER['REQUEST_URI'])) {
			$_SERVER['REQUEST_URI'] = '/lemur' . $_SERVER['REQUEST_URI'];
		}
	}
}

?>