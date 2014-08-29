<?php
session_start();
require('../config.php');
$url = "https://authserver.mojang.com/authenticate";

$data = array();
  $data['agent'] = array("name" => "Minecraft", "version" => 1);
  $data['username'] = $_POST['email'];
  $data['password'] = $_POST['password'];

$options = array(
  'http' => array(
    'header' => "Content-type: application/json",
    'method' => 'POST',
    'content' => json_encode($data),
  ),
);
$context = stream_context_create($options);
$result = file_get_contents($url, false, $context);

if ($result === false) {
	header('Location: ../' . INDEX_FILENAME . '?login=incorrect');
} else {
	$userData = json_decode($result, true);
	$tfinfo = json_decode(file_get_contents(TFINFO_JSON), true);
	//$userData['selectedProfile']['name']
	$developers = $tfinfo['developers'];
	$seniors = $tfinfo['senioradmins'];
	$telnets = $tfinfo['telnetadmins'];
	$supers = $tfinfo['superadmins'];

	
	if (array_search($userData['selectedProfile']['name'], $developers)) {
		$rank = 'developer';
	} elseif (array_search($userData['selectedProfile']['name'], $seniors)) {
		$rank = 'seniors';
	} elseif (array_search($userData['selectedProfile']['name'], $telnets)) {
		$rank = 'telnets';
	}  elseif (array_search($userData['selectedProfile']['name'], $supers)) {
		$rank = 'supers';
	} else {
		$rank = 'player';
	}
	
	if ($rank != 'player') {
		$_SESSION['loggedIn'] = true;
		header('Location: ../' . INDEX_FILENAME);
		die();
	} else {
		header('Location: ../' . INDEX_FILENAME . '?login=notAdmin');
		die();
	}
		
}

?>