<?php
session_start();
beginning:
require_once('config.php');

function isLoggedIn() {
	if (isset($_SESSION['loggedIn'])) {
		return true;
	} else {
		return false;
	}
}
function contains($needle, $haystack) {
	return strpos($haystack, $needle) !== false;
}

function curlUrl($url) {
	$ch = curl_init();
	$timeout = 2;
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
	$data = curl_exec($ch);
	curl_close($ch);
	return $data;
}

function searchForIP($ip, $array) {
   foreach ($array as $key => $val) {
       if ($val['ip'] === $ip) {
           return $key;
       }
   }
   return null;
}

if (isLoggedIn()) {

	if (!isset($_POST['kbytes'])) {
		die('Not all POST requests sent');
	}
	$length = $_POST['kbytes'] * 1024;
	$log = curlUrl(HTTPD_URL . "?password=" . HTTPD_PASSWORD . "&range=tail=" . $length);

	if ($log == "Incorrect password.") {
		die('Incorrect password entered in PHP script for TotalFreedomMod HTTPD');
	}

	file_put_contents('savedlogs.yml', $log);

	$logs = file('savedlogs.yml');
	$logs = array_slice($logs, count($logs)-($length+1), $length);

	for ($i = 0; $i < count($logs); ++$i) {
		$type = 'chat';
		if (contains('/gtfo', $logs[$i]) OR contains('/ban', $logs[$i]) OR contains('/glist', $logs[$i]) OR contains('/tempban', $logs[$i]) OR contains('/kick', $logs[$i]) OR contains('/kicknoob', $logs[$i]) OR contains('Banning', $logs[$i]) OR contains('has been a VERY naughty, naughty boy', $logs[$i]) OR contains(' been a naughty, naughty boy', $logs[$i]) OR contains(' - Temporarily banned ', $logs[$i])) {
			if ($_POST['filterKickBans'] == "false") {
				break;
			}
			$type = 'kickBan';
		} elseif (contains('WorldEdit', $logs[$i])) {
			if ($_POST['worldedit'] == "false") {
				break;
			}
			$type = 'worldEdit';
		} elseif (contains('[TotalFreedomMod] [ADMIN]', $logs[$i])) {
			if ($_POST['chat'] == "false") {
				break;
			}
			$type= 'adminChat';
		} elseif (contains('[PLAYER_COMMAND]', $logs[$i]) OR contains('[PREPROCESS_COMMAND]', $logs[$i]) OR contains('issued server command:', $logs[$i])) {
			if ($_POST['commands'] == "false") {
				break;
			}
			$type = 'commands';
		} elseif (contains('Server thread/ERROR]', $logs[$i])) {
			if ($_POST['errors'] == "false") {
				break;
			}
			$type = 'errors';
		} elseif (contains(' [JOIN] ', $logs[$i]) OR contains(' [EXIT] ', $logs[$i]) OR contains('logged in with entity', $logs[$i]) OR contains('lost connection:', $logs[$i]) OR contains('Disconnecting ',$logs[$i])) {
			if ($_POST['loginlogout'] == "false") {
				break;
			}
			$type = 'loginLogout';
		}
			if ($_POST['chat'] == "false" AND $type == 'chat') {
				break;
			}
		echo "<span class='" . $type . "'>" . htmlspecialchars($logs[$i]) . "</span>";
	}
	//unlink('savedlogs.yml');
} else {
	$users = json_decode(file_get_contents(USERS_JSON/*'users.json'*/), true);
	$check = searchForIP($_SERVER['REMOTE_ADDR'], $users);
	
	if ($check) {
		$_SESSION['loggedIn'] = true;
		goto beginning;
		die();
	}
	if (isset($_POST['password']) AND $_POST['password'] == BYPASS_PASSWORD) {
		$_SESSION['loggedIn'] = true;
		header('Location: ' . INDEX_FILENAME);
	} elseif (isset($_POST['password']) AND $_POST['password'] != BYPASS_PASSWORD) {
		header('Location: ' . INDEX_FILENAME);
	}
	echo "You are not authorized! Use <i>/logs</i> on the server to register.<br /><br />";
	
	if (isset($_GET['login'])) {
		if ($_GET['login'] == "notAdmin") {
			echo "You are not an admin on TotalFreedom";
		}
		if ($_GET['login'] == "incorrect") {
			echo "Incorrect username or password";
		}
	}
	
	echo "Alternatively you can login using Your Minecraft Email and Password";
	
	?>
	
	<form class="form-horizontal" method="POST" action="<?php echo MC_AUTH_SCRIPT_LOCATION; ?>" role="form">
	  <div class="form-group">
		<label for="email" class="col-sm-2 control-label">Email</label>
		<div class="col-sm-10">
		  <input type="email" class="form-control" name="email" id="email" placeholder="Minecraft Email">
		</div>
	  </div>
	  <div class="form-group">
		<label for="password" class="col-sm-2 control-label">Password</label>
		<div class="col-sm-10">
		  <input type="password" class="form-control" name="password" id="password" placeholder="Minecraft Password">
		</div>
	  </div>
	  <div class="form-group">
		<div class="col-sm-offset-2 col-sm-10">
		  <button type="submit" class="btn btn-default">Log In</button>
		</div>
	  </div>
	</form>
	
	<?php

	
	
}
?>