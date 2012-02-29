<?php
require_once(dirname(__FILE__)."/../classes/csvReader.class.php");
require_once(dirname(__FILE__)."/../classes/database.class.php");
require_once(dirname(__FILE__)."/../classes/concept.class.php");


class getLinkData
{
	public function __construct()
	{
		$database = new database();
		
		$database->clearLinkData();
		
		self::readNavigationLinks($database);
		self::readServiceLinks($database);
	}
	
	
	
	static function readNavigationLinks(database $database) {
		$config = parse_ini_file(dirname(__FILE__)."/../config.ini", true);
		
		$navigation_links_path = $config["links"]["navigation_links"];
		
		$reader = new csvReader();
		$csv = $reader->read($navigation_links_path);
		
		$navigationLinks = array();
		
		foreach ($csv as $row) {
			$navigationLink = new navigationLink();
			$navigationLink->url = $row["Link URL"];
			$navigationLink->label = $row["Link title"];
			// replace service uris with navigation uris (not sure if i should be doing this)
			$navigationUri = str_replace("http://id.esd.org.uk/service/", "http://id.esd.org.uk/serviceGroup/", $row["ESD link"]);
			$navigationLink->navigationUri = $navigationUri;
			
			$navigationLinks[] = $navigationLink;
		}
		
		foreach ($navigationLinks as $navigationLink) {
			$database->insertNavigation_link($navigationLink);
		}
		
		echo("finished reading navigation links\n");
		
		return $navigationLinks;
	}
	
	
	
	static function readServiceLinks(database $database) {
		$config = parse_ini_file(dirname(__FILE__)."/../config.ini", true);		
		
		$authorities = $database->selectAuthorities();
		$extraParameters = new stdClass();
		$extraParameters->authorities = $authorities;
		$extraParameters->database = $database;
		
		$service_links_path = $config["links"]["service_links"];
				
		$reader = new csvReader();
		$csv = $reader->read_callback($service_links_path, array("getLinkData", "saveServiceLink"), $extraParameters);
		
		echo("finished reading service links in $time\n");
	}
	
	
	static function saveServiceLink($csvRow, stdClass $parameters) {
		$authorities = $parameters->authorities;
		$database = $parameters->database;
		
		$snac = $csvRow["SNAC"];
		$label = $csvRow["Service Name"];
		$lgsl = $csvRow["LGSL"];
		$lgil = $csvRow["LGIL"];
		$url = $csvRow["Service URL"];
		
		$authorityUri = self::getAuthorityUriBySnac($authorities, $snac);
		
		if (empty($authorityUri)) {
			$database->logError("", "authority not found for snac code: ".$snac);
			return;
		}
		
		$serviceLink = new serviceInteractionLink();
		$serviceLink->label = $label;
		$serviceLink->url = $url;
		$serviceLink->authorityUri = $authorityUri;
		$serviceLink->serviceUri = "http://id.esd.org.uk/service/".$lgsl;
		$serviceLink->interactionUri = "http://id.esd.org.uk/interaction/".$lgil;
		
		$database->insertService_interaction_link($serviceLink);
	}
	
	
	static function getAuthorityUriBySnac($authorities, $snac) {
		foreach ($authorities as $authority) {
			if ($authority->code === $snac) {
				return $authority->uri;
			}
		}
		
		return null;
	}
}

new getLinkData();

?>
