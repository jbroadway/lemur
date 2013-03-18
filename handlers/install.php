<?php

$page->layout = 'admin';

if (! User::require_admin ()) {
	header ('Location: /admin');
	exit;
}

$cur = $this->installed ('lemur', $appconf['Admin']['version']);

if ($cur === true) {
	$page->title = 'Already installed';
	echo '<p><a href="/">Home</a></p>';
	return;
} elseif ($cur !== false) {
	header ('Location: /' . $appconf['Admin']['upgrade']);
	exit;
}

$page->title = 'Installing app: Lemur Learning';

if (ELEFANT_VERSION < '1.1.0') {
	$driver = conf ('Database', 'driver');
} else {
	$conn = conf ('Database', 'master');
	$driver = $conn['driver'];
}

$error = false;
$sqldata = sql_split (file_get_contents ('apps/lemur/conf/install_' . $driver . '.sql'));
foreach ($sqldata as $sql) {
	if (! DB::execute ($sql)) {
		$error = DB::error ();
		echo '<p class="notice">Error: ' . DB::error () . '</p>';
		break;
	}
}

if ($error) {
	echo '<p class="notice">Error: ' . $error . '</p>';
	echo '<p>Install failed.</p>';
	return;
}

echo '<p><a href="/lemur/admin">Done.</a></p>';

$this->mark_installed ('lemur', $appconf['Admin']['version']);

?>