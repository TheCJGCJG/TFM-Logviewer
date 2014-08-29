<?php
require('config.php');

$mode = $_GET['mode'];
$password = $_GET['password'];
$name = $_GET['name'];
$ip = $_GET['ip'];

if ($password != UPDATE_PASSWORD) {
	die('incorrect password');
}

$users = json_decode(file_get_contents('users.json'), true);

switch($mode) {
	case "update":
		$users[$name]['ip'] = $ip;
		break;
	case "delete":
		unset($users[$name]);
		break;
	default:
		die('Invalid Mode');
		break;
}

file_put_contents('users.json', json_encode($users));


?>