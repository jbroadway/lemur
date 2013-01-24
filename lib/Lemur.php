<?php

namespace lemur;

/**
 * General helper methods.
 */
class Lemur {
	/**
	 * Call this from Elefant's bootstrap.php file so that
	 * links to `/course/` map to `/lemur/course/`.
	 *
	 * Usage:
	 *
	 *     lemur\Lemur::bootstrap ();
	 */
	public static function bootstrap () {
		if (strpos ($_SERVER['REQUEST_URI'], '/course/') === 0) {
			$_SERVER['REQUEST_URI'] = '/lemur' . $_SERVER['REQUEST_URI'];
		}
	}
}

?>