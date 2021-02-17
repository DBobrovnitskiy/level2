<?php

header('Access-Control-Allow-Origin: http://test2.local');
header('Access-Control-Allow-Credentials: true');

/**
 * disconnects the session
 */
session_start();
$_COOKIE = array();
$_SESSION = array();

echo '{"ok":true}';