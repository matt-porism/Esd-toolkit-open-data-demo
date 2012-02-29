<?php
require_once(dirname(__FILE__)."/../classes/database.class.php");

$database = new database();

$rows = array();
$type = isset($_GET["type"]) ? $_GET["type"] : "navigation-links";

if ($type === "service-links") {
	$authorityUri = isset($_GET["authorityUri"]) ? $_GET["authorityUri"] : "navigation-links";
	if (!empty($authorityUri)) {
		$rows = $database->call("selectNavigationUrisWithServiceLink ('".mysql_escape_string($authorityUri)."')");
	}
}
else {
	$rows = $database->call("selectNavigationUrisWithNavigationLink");
}

$uris = array();

foreach ($rows as $rows) {
	$uris[] = $rows->uri;
}

$obj = new stdClass();
$obj->uris = $uris;

//header('Content-Type: application/json; charset=utf8');
echo json_encode($obj);

?>