<?php
require_once(dirname(__FILE__)."/classes/database.class.php");
require_once(dirname(__FILE__)."/classes/authority.class.php");
require_once(dirname(__FILE__)."/classes/concept.class.php");

$database = new database();
$navigationHierarchy = $database->selectHierarchicalNavigation();

print("hello");

?>