<?php
require_once(dirname(__FILE__)."/authority.class.php");
require_once(dirname(__FILE__)."/concept.class.php");

class database
{
	private $username = null;
	private $password = null;
	private $database = null;
	private $server = null;
	
	
	public function __construct() {
		$config = parse_ini_file(dirname(__FILE__)."/../config.ini", true);
		$this->username = $config["database_connection"]["username"];
		$this->password = $config["database_connection"]["password"];
		$this->database = $config["database_connection"]["database"];
		$this->server = $config["database_connection"]["server"];
	}
	
	
	
	public function call($storedProcedure, $fetch=true) {
		$db_handle = mysql_connect($this->server, $this->username, $this->password);
		$db_found = mysql_select_db($this->database, $db_handle);

		$rows = array();
		
		if ($db_found) {
			$sql = "call ".$storedProcedure;
			$result = mysql_query($sql);
			
			if ($fetch) {
				while ($row = mysql_fetch_object($result)) {
					$rows[] = $row;
				}

				mysql_close($db_handle);
			}
			
			if (!$result) {
				$this->logError($sql, "call failed");
			}			
		}
		else {
			mysql_close($db_handle);
			throw new Exception("Problem connecting to the database");
		}
		
		return $rows;
	}
	
	
	public function logError($sql, $message) {
		$db_handle = mysql_connect($this->server, $this->username, $this->password);
		$db_found = mysql_select_db($this->database, $db_handle);

		$rows = array();
		
		if ($db_found) {
			$sql = "call insertError ('".mysql_escape_string($sql)."', '".mysql_escape_string($message)."')";
			$result = mysql_query($sql);
			mysql_close($db_handle);
		}
		else {
			mysql_close($db_handle);
			throw new Exception("Problem connecting to the database");
		}
		
		return $rows;
	}
	
	
	public function clearAuthorityData() {
		$this->call("clearAuthorityData", false); 
	}
	
	
	public function insertAuthorityType(authorityType $authorityType) {		
		$sql = "insertAuthorityType ('".mysql_escape_string($authorityType->label)."', '".mysql_escape_string($authorityType->uri)."')";
		$this->call($sql, false); 
	}
	
	
	public function insertGeographyType(geographyType $geographyType) {		
		$sql = "insertGeographyType ('".mysql_escape_string($geographyType->label)."', '".mysql_escape_string($geographyType->uri)."')";
		$this->call($sql, false); 
	}
	
	
	public function insertAuthority(authority $authority) {
		$sql = "insertAuthority ('".mysql_escape_string($authority->label)."', '".mysql_escape_string($authority->uri)."', '".mysql_escape_string($authority->code)."', '".mysql_escape_string($authority->authorityTypeUri)."')";
		$this->call($sql, false); 
	}
	
	
	public function insertGeography(geography $geography) {
		$sql = "insertGeography ('".mysql_escape_string($geography->label)."', '".mysql_escape_string($geography->uri)."', '".mysql_escape_string($geography->code)."', '".mysql_escape_string($geography->geographyTypeUri)."', '".mysql_escape_string($geography->authorityUri)."', '".mysql_escape_string($geography->containedByUri)."')";
		$this->call($sql, false); 
	}
	
	
	public function insertGeography_geographicNeighbour(neighbour $neighbour) {
		$sql = "insertGeography_geographicNeighbour ('".mysql_escape_string($neighbour->geographyUri)."', '".mysql_escape_string($neighbour->neighbourUri)."')";
		$this->call($sql, false);
	}


	public function insertGeography_geographicHierarchy(geographyHierarchy $hierarchy) {
		$sql = "insertGeography_geographicHierarchy ('".mysql_escape_string($hierarchy->parentUri)."', '".mysql_escape_string($hierarchy->childUri)."')";
		
		print_r($sql);
		print_r("\n");

		$this->call($sql, false);
	}	
	
	
	
	public function clearConceptData() {
		$this->call("clearConceptData", false); 
	}
	
	
	private function getInsertConceptSql($sprocName, concept $concept) {
		return $sprocName." ('".mysql_escape_string($concept->label)."', '".mysql_escape_string($concept->uri)."', '".mysql_escape_string($concept->identifier)."', '".mysql_escape_string($concept->definition)."')";
	}
	
	
	public function insertNavigation(navigation $navigation) {
		$sql = $this->getInsertConceptSql("insertNavigation", $navigation);
		$this->call($sql, false); 
	}
	
	
	public function insertNavigation_navigation(hierarchy $hierarchy) {
		$sql = "insertNavigation_navigation ('".mysql_escape_string($hierarchy->narrowerUri)."', '".mysql_escape_string($hierarchy->broaderUri)."')";
		$this->call($sql, false); 
	}
	
	
	public function insertNavigation_service(mapping $mapping) {
		$sql = "insertNavigation_service ('".mysql_escape_string($mapping->fromUri)."', '".mysql_escape_string($mapping->toUri)."')";
		$this->call($sql, false); 
	}
	
	
	public function insertService(service $service) {
		$sql = $this->getInsertConceptSql("insertService", $service);
		$this->call($sql, false); 
	}
	
	
	public function insertInteraction(interaction $interaction) {
		$sql = $this->getInsertConceptSql("insertInteraction", $interaction);
		$this->call($sql, false); 
	}
	
	
	public function clearLinkData() {
		$this->call("clearLinkData", false); 
	}
	
	
	public function insertNavigation_link(navigationLink $navigationLink) {
		$sql = "insertNavigation_link ('".
			mysql_escape_string($navigationLink->navigationUri)."', '".
			mysql_escape_string($navigationLink->label)."', '".
			mysql_escape_string($navigationLink->url)."')";
		
		$this->call($sql, false);
	}
	
	
	public function insertService_interaction_link(serviceInteractionLink $serviceInteractionLink) {
		$sql = "insertService_interaction_link ('".
			mysql_escape_string($serviceInteractionLink->authorityUri)."', '".
			mysql_escape_string($serviceInteractionLink->serviceUri)."', '".
			mysql_escape_string($serviceInteractionLink->interactionUri)."', '".
			mysql_escape_string($serviceInteractionLink->label)."', '".
			mysql_escape_string($serviceInteractionLink->url).
		"')";
		
		$this->call($sql, false);
	}
	
	
	
	
	
	public function selectItems($sproc, $itemClassName)
	{
		$rows = $this->call($sproc, true); 		
		$items = array();
		
		foreach ($rows as $row) {
			$item = new $itemClassName();
			$item->buildFromRow($row);
			
			$items[] = $item;
		}
		
		return $items;
	}
	
	
	public function selectItem($sproc, $itemClassName, $uri) 
	{
		if (empty($uri)) {
			return null;
		}
		$sql = $sproc."('".mysql_escape_string($uri)."')";
		$rows = $this->call($sql, true);
		if (empty($rows)) {
			return null;
		}
		$row = $rows[0];
		
		$item = new $itemClassName();
		$item->buildFromRow($row);
		
		return $item;
	}
	
	
		
	public function selectAuthorities() {
		return $this->selectItems("selectAuthorities", "authority"); 
	}
	
	
	public function selectAuthority($uri) {
		return $this->selectItem("selectAuthority", "authority", $uri);
	}
	
	
	public function selectGeographicParentAuthorities($authorityUri) {
		$sql = "selectGeographicParentAuthorities ('".mysql_escape_string($authorityUri)."')";
		return $this->selectItems($sql, "authority");
	}
	
	
	public function selectGeographicChildAuthorities($authorityUri) {
		$sql = "selectGeographicChildAuthorities ('".mysql_escape_string($authorityUri)."')";
		return $this->selectItems($sql, "authority");
	}
	
	
	public function selectGeographicNeighbouringAuthorities($authorityUri) {
		$sql = "selectGeographicNeighbouringAuthorities ('".mysql_escape_string($authorityUri)."')";
		return $this->selectItems($sql, "authority");
	}
	
	
	
	public function selectHierarchicalNavigation()
	{
		$navigations = $this->selectItems("selectNavigations", "navigation");
		$hierarchies = $this->selectItems("selectNavigation_navigations", "hierarchy");

		$navigationUris = array(); // keep for use later
		$keyedByUri = array();
		foreach ($navigations as $navigation) {
			$navigationUris[] = $navigation->uri;
			$keyedByUri[$navigation->uri] = $navigation;
		}
		ksort($keyedByUri);
		
		$narrowerUris = array(); // keep a list of narrower uris to work out which are the roots later - top concept of would have been better
		
		// set the narrowers
		foreach ($hierarchies as $hierarchy) {
			$narrowerUris[] = $hierarchy->narrowerUri;
			$broader = $keyedByUri[$hierarchy->broaderUri];
			$narrower = $keyedByUri[$hierarchy->narrowerUri];
			$broader->narrowers[] = $narrower;
		}
		
		// get the top items
		$tree = array();
		foreach ($navigationUris as $navigationUri) {
			if (!in_array($navigationUri, $narrowerUris, true)) {
				$tree[] = $keyedByUri[$navigationUri];
			}
		}
		
		return $tree;
	}
	
	
	public function selectNavigation($uri) {
		return $this->selectItem("selectNavigation", "navigation", $uri);
	}
	
	
	public function selectNavigation_links($uri) {
		$sql = "selectNavigation_links('".mysql_escape_string($uri)."')";
		return $this->selectItems($sql, "navigationLink");
	}
	
	
	public function selectServiceByNavigation($navigationUri) {
		return $this->selectItem("selectServiceByNavigation", "service", $navigationUri);
	}
	
	
	public function selectService_interaction_links($authorityUri, $serviceUri) {
		$sql = "selectService_interaction_links('".mysql_escape_string($authorityUri)."', '".mysql_escape_string($serviceUri)."')";
		return $this->selectItems($sql, "serviceInteractionLink");
	}
}

?>