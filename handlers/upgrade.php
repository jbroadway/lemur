<?php

$page->layout = 'admin';

if (! User::require_admin ()) {
	header ('Location: /admin');
	exit;
}

if ($this->installed ('lemur', $appconf['Admin']['version']) === true) {
	$page->title = 'Already up-to-date';
	echo '<p><a href="/">Home</a></p>';
	return;
}

$page->title = 'Upgrading app: Lemur Learning';

echo '<p>Done.</p>';

$this->mark_installed ('lemur', $appconf['Admin']['version']);

?>