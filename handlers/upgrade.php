<?php

$this->require_admin ();

$page->layout = 'admin';

if ($this->installed ('lemur', $appconf['Admin']['version']) === true) {
	$page->title = 'Already up-to-date';
	echo '<p><a href="/">Home</a></p>';
	return;
}

$page->title = 'Upgrading app: Lemur Learning';

echo '<p>Done.</p>';

$this->mark_installed ('lemur', $appconf['Admin']['version']);

?>