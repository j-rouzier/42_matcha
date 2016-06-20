<?php

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
	$db = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
} catch (Exception $e) {
        die('Il y a eu une erreur. Détails :  ' . $e->getMessage());
}

require_once("app/class/Member.class.php");
require_once("app/class/MemberManager.php");

session_start();

require_once("app/action.app.php");