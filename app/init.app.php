<?php

error_reporting(E_ALL);
ini_set("display_errors", 1);
session_start();
require_once("app/error.app.php");

if (file_exists("config/cfg.ini")) {
	$db_infos = parse_ini_file("config/cfg.ini");
	if (!isset($db_infos['db_name']) ||
		!isset($db_infos['db_host']) ||
		!isset($db_infos['db_username']) ||
		!isset($db_infos['db_passwd']) ||
		!isset($db_infos['db_port'])) {
		header("Location: error.php");
	}
} else {
	header("Location: config/setup.php");
}

include ("config/database.php");

try {
	$db = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8", PDO::ERRMODE_EXCEPTION));
} catch (Exception $e) {
        header("Location: config/setup.php?error=" . urlencode($e));
}

date_default_timezone_set('Europe/Paris');

$mapsAPI = "AIzaSyDL228EdYTuQ0mvE9V5LwrQwWj4D9rzIG4";

spl_autoload_register(function($class) {
    require_once 'app/class/'. $class .'.class.php';
});


if (isset($_SESSION['connected']) && $_SESSION['connected'] === true) {
	if (isset($_SESSION['userid'])) {
		$manager = new MemberManager($db);
		$return = $manager->getFromId($_SESSION['userid']);
		if (!is_object($return)) {
			$error = $return;
			session_destroy();
			header("Location: index.php");
			$_SESSION['connected'] = false;
		} else {
			$_SESSION['connected'] = true;
			$currentUser = $return;
			$currentUser->setLastLogin(time());
			$manager->update($currentUser);
			$currentUser->recheckPopularity($db);
			$currentUser->checkLocation();
		}
	} else {
		$_SESSION['connected'] = false;
	}
} else {
	$_SESSION['connected'] = false;
}

function isUserLogged() {
	return $_SESSION['connected'];
}

require_once("app/action.app.php");