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

	/**
	 * Fetch a list of payment handlers.
	 */
	public static function payment_handlers () {
		$files = glob ('apps/*/conf/payments.php');
		$files = is_array ($files) ? $files : array ();
		$providers = array ();
		foreach ($files as $file) {
			$ini = parse_ini_file ($file);
			if (! is_array ($ini)) {
				continue;
			}
			$providers = array_merge ($providers, $ini);
		}
		asort ($providers);
		return $providers;
	}
}

?>