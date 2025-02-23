<?php
	error_reporting(E_ALL | E_STRICT);
    ini_set('display_errors', 1);
    ini_set('html_errors', 1);
	
	define('PROJECT_ROOT', './');
	
	if (!session_id() && !session_start()) {
		die('Failed to start session');
	}
	
	$unblockedPages = ['login', 'register'];
	
	// Get the current Screen from the browser
	// NOTE: .htaccess rewrites the screen from /screen to ?screen=screen
	// This makes it available in $_REQUEST
	$screen = @$_REQUEST['screen'];
	
	if (!@$_SESSION['uid'] && !in_array($screen, $unblockedPages)) {
		header("Location: ".PROJECT_ROOT."login", true, 303);
		exit;
	}
	
	require_once('./db.php');
	
	echo <<< HeadHTML
	<!DOCTYPE html>
	<html lang="en">
		<head>
  		<meta charset="utf-8">
  		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  		
  		<title>CP476 Project</title>
		</head>
HeadHTML;
	
	if (!in_array($screen, $unblockedPages)) {
		echo "<!--logout--><a style='margin-right: 10px;' href='".PROJECT_ROOT."logout'><button>Logout</button></a>";
	}
	
	switch ($screen) {
		case 'register':
			require_once('register.php');
			break;
		case 'login':
			require_once('login.php');
			break;
		case 'home':
			require_once('home.php');
			break;
		case 'logout':
			unset($_SESSION);
			setcookie(session_name(), session_id(), 1);
			session_destroy();
			session_write_close();
			/* clean browswer cache information */
			header("Cache-Control: no-cache, must-revalidate");
			header("Expires: Mon,  Jun 2016 05:00:00 GMT");
			header("Content-Type: application/xml; charset=utf-8");
			header("Location: ".PROJECT_ROOT."login");
			exit;
		case 'cart':
			require_once('cart.php');
			break;
		default:
			header("Location: ".PROJECT_ROOT."home", true, 303);
			exit;
	}
	echo "</body></html>";
?>