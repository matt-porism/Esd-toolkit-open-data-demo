<?php
require_once(dirname(__FILE__)."/classes/database.class.php");
require_once(dirname(__FILE__)."/classes/authority.class.php");
require_once(dirname(__FILE__)."/classes/concept.class.php");

class serviceSection {
	
	public function __construct() {
		$navigationUri = $_GET["navigationUri"];
		
		if (empty($navigationUri)) {
			return;
		}
		
		self::echoService($navigationUri);
	}
	
	
}

new serviceSection();