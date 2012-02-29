<?php
require_once(dirname(__FILE__)."/../classes/sparqlReader.class.php");
require_once(dirname(__FILE__)."/../classes/database.class.php");
require_once(dirname(__FILE__)."/../classes/concept.class.php");


class getConceptData
{
	public function __construct()
	{
		$config = parse_ini_file(dirname(__FILE__)."/../config.ini", true);
		
		$database = new database();
		
		$database->clearConceptData();
		
		$endPoint = $config["sparql_end_points"]["esd"];
		$reader = new sparqlReader($endPoint);
		
		$navigations = self::readNavigations($reader, $database);
		
		$services = self::readServices($reader, $database);
		
		$interactions = self::readInteractions($reader, $database);
		
		$hierarchy = self::readNavigationHierarchy($reader, $database);
		
		$mappings = self::readNavigationServiceMappings($reader, $database);
	}
	
	
	static function readNavigations(sparqlReader $reader, database $database) {
		$schemeUri = self::getLatestLgnlUri();
		
		$navigations = self::readConcepts($reader, $schemeUri, 'navigation');
		
		foreach ($navigations as $navigation) {
			$database->insertNavigation($navigation);
		}
		
		echo("finished reading navigation items\n");
		
		return $navigations;
	}
	
	
	static function readServices(sparqlReader $reader, database $database) {
		$schemeUri = self::getLatestLgslUri();
		
		$services = self::readConcepts($reader, $schemeUri, 'service');
		
		foreach ($services as $service) {
			$database->insertService($service);
		}
		
		echo("finished reading services\n");
		
		return $services;
	}
	
	
	static function readInteractions(sparqlReader $reader, database $database) {
		$schemeUri = self::getLatestLgilUri();
		
		$interactions = self::readConcepts($reader, $schemeUri, 'interaction');
		
		foreach ($interactions as $interaction) {
			$database->insertInteraction($interaction);
		}
		
		echo("finished reading interactions\n");
		
		return $interactions;
	}
	
	
	static function readConcepts(sparqlReader $reader, $schemeUri, $conceptClassName) {
		$sparql = str_replace("{scheme-uri}", $schemeUri, self::$sparql_selectConcepts);
		
		$result = $reader->query($sparql);
		
		$concepts = array();
		
		foreach ($result->results->bindings as $binding) {
			$concept = new $conceptClassName();
			$concept->uri = $binding->uri->value;
			$concept->label = $binding->label->value;
			$concept->identifier = $binding->identifier->value;
			
			$concepts[] = $concept;
		}
		
		return $concepts;
	}
	
	
	static function readNavigationHierarchy(sparqlReader $reader, database $database) {
		$latestLgnl = self::getLatestLgnlUri();
		
		$sparql = str_replace("{scheme-uri}", $latestLgnl, self::$sparql_selectHierarchy);
		
		$result = $reader->query($sparql);
		
		$hierarchies = array();
		
		foreach ($result->results->bindings as $binding) {
			$hierarchy = new hierarchy();
			$hierarchy->narrowerUri = $binding->narrowerUri->value;
			$hierarchy->broaderUri = $binding->broaderUri->value;
			$hierarchies[] = $hierarchy;
		}
		
		foreach ($hierarchies as $hierarchy) {
			$database->insertNavigation_navigation($hierarchy);
		}
		
		echo("finished reading navigation hierarchy\n");
		
		return $hierarchies;
	}
	
	
	static function readNavigationServiceMappings(sparqlReader $reader, database $database) {
		$latestLgnl = self::getLatestLgnlUri();
		$latestLgsl = self::getLatestLgslUri();
		
		$sparql = str_replace("{from-scheme-uri}", $latestLgnl, self::$sparql_selectMappings);
		$sparql = str_replace("{to-scheme-uri}", $latestLgsl, $sparql);
		
		$result = $reader->query($sparql);
		
		$mappings = array();
		
		foreach ($result->results->bindings as $binding) {
			$mapping = new mapping();
			$mapping->fromUri = $binding->fromUri->value;
			$mapping->toUri = $binding->toUri->value;
			$mappings[] = $mapping;
		}
		
		foreach ($mappings as $mapping) {
			$database->insertNavigation_service($mapping);
		}
		
		echo("finished reading navigation service mappings\n");
		
		return $mappings;
	}
	
	
	static function getLatestLgnlUri() {
		// todo
		return "http://id.esd.org.uk/LocalGovernmentNavigationList/3.13";
	}
	
	
	static function getLatestLgslUri() {
		// todo
		return "http://id.esd.org.uk/LocalGovernmentServiceList/3.14";
	}
	
	
	static function getLatestLgilUri() {
		// todo
		return "http://id.esd.org.uk/InteractionList/1.01";
	}
	
		
	public static $sparql_selectConcepts = '
prefix skos: <http://www.w3.org/2004/02/skos/core#>
prefix dc: <http://purl.org/dc/terms/>

select distinct ?uri ?label ?identifier ?definition
where {
	?uri skos:inScheme <{scheme-uri}> .
	?uri skos:prefLabel ?label .
	?uri dc:identifier ?identifier .
	optional { ?uri skos:definition ?definition } .
}
';

	public static $sparql_selectHierarchy = '
prefix skos: <http://www.w3.org/2004/02/skos/core#>

select distinct ?narrowerUri ?broaderUri
where {
	?narrowerUri skos:inScheme <{scheme-uri}> .
	?narrowerUri skos:broader ?broaderUri .
	?broaderUri skos:inScheme <{scheme-uri}> .
}
';

	public static $sparql_selectMappings = '
prefix skos: <http://www.w3.org/2004/02/skos/core#>

select distinct ?fromUri ?toUri
where {
	?fromUri skos:inScheme <{from-scheme-uri}> .
	?toUri <http://def.esd.org.uk/isPartOfServiceGroup> ?fromUri .
	?toUri skos:inScheme <{to-scheme-uri}> .
}
';
}

new getConceptData();

?>