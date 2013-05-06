<?php

/**
 * Post-install script for Composer.
 */

if (! $this->cli) {
	die ('Must be run from the command line.');
}

$page->layout = false;

// add bootstrap
if (file_exists ('bootstrap.php')) {
	$data = file_get_contents ('bootstrap.php');
	if (strpos ($data, '?>') === false) {
		$data .= "\n\n// Initialize the LMS\nlemur\\Lemur::bootstrap ();";
	} else {
		$data = str_replace ('?>', "// Initialize the LMS\nlemur\\Lemur::bootstrap ();\n\n?>", $data);
	}
	file_put_contents ('bootstrap.php', $data);
} else {
	copy ('apps/lemur/sample_bootstrap.php', 'bootstrap.php');
}

// add layout
exec ('cp -R apps/lemur/theme layouts/lemur');

?>