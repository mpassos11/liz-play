<?php

include_once __DIR__ . '/common.php';
include_once __DIR__ . '/rotas.php';

carregarEnv();

session_start();

$dirs = [
	COMMON_PATH . 'src/classes' . DIRECTORY_SEPARATOR,
	COMMON_PATH . 'src/models' . DIRECTORY_SEPARATOR
];

foreach ($dirs as $dir) {
	$files = glob($dir . '*.class.php');
	foreach ($files as $file) {
		require_once $file;
	}
}

/**
 * @var IPTV $IPTV
 */
$GLOBALS['IPTV'] = new IPTV();