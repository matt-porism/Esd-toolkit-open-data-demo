<?php
require_once(dirname(__FILE__)."/../classes/sparqlReader.class.php");
require_once(dirname(__FILE__)."/../classes/database.class.php");
require_once(dirname(__FILE__)."/../classes/authority.class.php");

class getAuthorityData
{	
	public function __construct()
	{		
		$config = parse_ini_file(dirname(__FILE__)."/../config.ini", true);
		$dataEndPoint = $config["sparql_end_points"]["data_gov_uk"];
		$dataSparqlReader = new sparqlReader($dataEndPoint);
		$osEndPoint = $config["sparql_end_points"]["os"];
		$osSparqlReader = new sparqlReader($osEndPoint);
		
		$database = new database();
		
		$database->clearAuthorityData();
		
		$authorityTypes = self::readAuthorityTypes($dataSparqlReader, $database);
		
		$geographyTypes = self::readGeographyTypes($dataSparqlReader, $osSparqlReader, $database);
		
		$authorities = self::readAuthorities($dataSparqlReader, $database);

		$geographies = self::readGeographies($dataSparqlReader, $database);

		self::readNeighbouringGeographies($osSparqlReader, $database);

		self::readGeographicHierarchies($osSparqlReader, $database);
	}
	
	
	public static $uris_dataGeo_LocalAuthority = "http://statistics.data.gov.uk/def/administrative-geography/LocalAuthority"; // class
	public static $uris_dataGeo_localAuthority = "http://statistics.data.gov.uk/def/administrative-geography/localAuthority"; // predicate
	public static $uris_dataGeo_coverage = "http://statistics.data.gov.uk/def/administrative-geography/coverage";
	public static $uris_skos_prefLabel = "http://www.w3.org/2004/02/skos/core#prefLabel";
	public static $uris_skos_notation = "http://www.w3.org/2004/02/skos/core#notation";
	public static $uris_osSpatial_containedBy = "http://data.ordnancesurvey.co.uk/ontology/spatialrelations/containedBy";
	public static $uris_rdf_label = "http://www.w3.org/2000/01/rdf-schema#label";
	public static $uris_rdf_comment = "http://www.w3.org/2000/01/rdf-schema#comment";
	public static $uris_os_County = "http://data.ordnancesurvey.co.uk/ontology/admingeo/County";
	
	
	public static $sparql_selectAuthorityTypes = '
prefix skos: <http://www.w3.org/2004/02/skos/core#>
prefix geo: <http://statistics.data.gov.uk/def/administrative-geography/>

select ?label 
where { geo:LocalAuthority skos:prefLabel ?label }
';

	public static $sparql_selectGeographyTypes = '
prefix geo: <http://statistics.data.gov.uk/def/administrative-geography/>
		
select distinct ?areaType
where { 
	?area geo:localAuthority ?auth .
	?area a ?areaType .
	filter regex(str(?areaType), "data.ordnancesurvey.co.uk") .
}
';
	
	public static $sparql_selectAuthorities = '
prefix skos: <http://www.w3.org/2004/02/skos/core#>
prefix rdf: <http://www.w3.org/2000/01/rdf-schema#>
prefix geo: <http://statistics.data.gov.uk/def/administrative-geography/>

select distinct ?uri ?label ?code
where { 
	?uri a geo:LocalAuthority .
	{ 
		{ ?uri skos:prefLabel ?label . } 
		union 
		{ 
			?uri rdf:label ?label . 
			optional { ?uri skos:prefLabel ?prefLabel } .
			filter (!bound(?prefLabel)) . 
		} 
	} .
	?uri skos:notation ?code .
}
';

	// this query reduces the number of areas from 478 to 407
	// this is because of the line binding ?geographyTypeUri
	// some areas do not have a type according to ordnance survey
	// i "think" this is because they are former counties or districts in former counties
	// and have been replaced by unitaries
	// i have made the choice: 
	//	- data.gov.uk own authorities
	//	- os own areas	
	// this query also attempts to take into account an error with half of the notation urls (prefix err)
	// and uses the rdf label if the pref label is not avaliable (there can be more then one rdf label,
	// but for those without a pref label there seems to be only one).
	public static $sparql_selectGeographies = '
prefix rdf: <http://www.w3.org/2000/01/rdf-schema#>
prefix skos: <http://www.w3.org/2004/02/skos/core#>
prefix owl: <http://www.w3.org/2002/07/owl#>
prefix geo: <http://statistics.data.gov.uk/def/administrative-geography/>
prefix os: <http://data.ordnancesurvey.co.uk/ontology/admingeo/>
prefix oss: <http://data.ordnancesurvey.co.uk/ontology/spatialrelations/>
prefix err: <http://statistics.data.gov.uk/def/administrative-geography//>

select distinct ?uri ?osUri ?label ?code ?geographyTypeUri ?authorityUri ?containedByUri
where { 
	?uri geo:localAuthority ?auth .
	?uri owl:sameAs ?osUri .
	filter (regex(str(?osUri), "data.ordnancesurvey.co.uk")) .
	{ 
		{ ?uri skos:prefLabel ?label . } 
		union 
		{ 
			?uri rdf:label ?label . 
			optional { ?uri skos:prefLabel ?prefLabel } .
			filter (!bound(?prefLabel)) . 
		} 
	} .
	{ 
		{ 
			?uri skos:notation ?code .
			filter (datatype(?code) = geo:StandardCode) .
		} 
		union 
		{
			?uri skos:notation ?code .
			filter (datatype(?code) = err:StandardCode) .
			optional { 
				?uri skos:notation ?standardCode .
				filter (datatype(?standardCode) = geo:StandardCode) .
			} .			
			filter (!bound(?standardCode)) .
		} 
	} .
	?uri a ?geographyTypeUri .
	filter (regex(str(?geographyTypeUri), "data.ordnancesurvey.co.uk") && !sameTerm(?geographyTypeUri, os:Borough)) .
	?uri geo:localAuthority ?authorityUri .
	optional {
		?uri oss:containedBy ?containedByUri .
		?containedByUri a os:County .
	}
}
';
	
	public static $sparql_selectNeighbouringGeographies = '
prefix sr: <http://data.ordnancesurvey.co.uk/ontology/spatialrelations/>

select ?uri ?neighbourUri
where {
	{
		{ ?uri a <http://data.ordnancesurvey.co.uk/ontology/admingeo/LondonBorough> }
		union
		{ ?uri a <http://data.ordnancesurvey.co.uk/ontology/admingeo/Borough> }
		union
		{ ?uri a <http://data.ordnancesurvey.co.uk/ontology/admingeo/MetropolitanDistrict> }
		union
		{ ?uri a <http://data.ordnancesurvey.co.uk/ontology/admingeo/UnitaryAuthority> }
		union
		{ ?uri a <http://data.ordnancesurvey.co.uk/ontology/admingeo/County> }
		union
		{ ?uri a <http://data.ordnancesurvey.co.uk/ontology/admingeo/District> }
	}
	?uri sr:touches ?neighbourUri .
}
';


	public static $sparql_selectGeographicHierarchies = '
prefix os: <http://data.ordnancesurvey.co.uk/ontology/admingeo/>

select ?parentUri ?childUri
where {
	?parentUri a os:County .
	?parentUri os:district ?childUri .
}
';
	
	
	private static function readAuthorityTypes(sparqlReader $sparqlReader, database $database) {
		$authorityTypeJson = $sparqlReader->query(self::$sparql_selectAuthorityTypes);	
		
		$authorityType = new authorityType();
		$authorityType->uri = self::$uris_dataGeo_LocalAuthority;
		$authorityType->label = $authorityTypeJson->results->bindings[0]->label->value;
		
		$authorityTypes = array();
		$authorityTypes[] = $authorityType;
		
		foreach ($authorityTypes as $authorityType) {
			$database->insertAuthorityType($authorityType);
		}
		
		echo("finished reading authority types\n");
		
		return $authorityTypes;
	}
	
	
	private static function readGeographyTypes(sparqlReader $dataSparqlReader, sparqlReader $osSparqlReader, database $database) {
		$sparql = self::$sparql_selectGeographyTypes;
		
		$dataJson = $dataSparqlReader->query($sparql);	
		
		$geographyTypes = array();

		foreach ($dataJson->results->bindings as $binding) {
			$geographyType = new geographyType();
			$geographyType->uri = $binding->areaType->value;
			
			$geographyTypes[] = $geographyType;
			
			// get the label
			$sparql = 'select ?label where { <'.$geographyType->uri.'> <'.self::$uris_rdf_label.'> ?label }';
			$osJson = $osSparqlReader->query($sparql);
			if (empty($osJson->results->bindings)) {
				// one result has no label, but seems to have the label in the "comment"
				$sparql = 'select ?label where { <'.$geographyType->uri.'> <'.self::$uris_rdf_comment.'> ?label }';
				$osJson = $osSparqlReader->query($sparql);
				if (empty($osJson->results->bindings)) {
					continue;
				}
			}
			$geographyType->label = $osJson->results->bindings[0]->label->value;
		}
		
		foreach ($geographyTypes as $geographyType) {
			$database->insertGeographyType($geographyType);
		}
		
		echo("finished reading geography types\n");
		
		return $geographyTypes;
	}
	
	
	private function readAuthorities(sparqlReader $sparqlReader, database $database) {
		$authorities = array();
		$json = $sparqlReader->query(self::$sparql_selectAuthorities);

		foreach ($json->results->bindings as $binding) {
			$authority = new authority();
			$authority->uri = $binding->uri->value;
			$authority->label = $binding->label->value;
			$authority->code = $binding->code->value;
			$authority->authorityTypeUri = self::$uris_dataGeo_LocalAuthority;
			
			$authorities[] = $authority;
		}
		
		foreach ($authorities as $authority) {
			$database->insertAuthority($authority);
		}
		
		echo("finished reading authorities\n");
		
		return $authorities;
	}
	
	
	public static function sortCountiesFirst(geography $a, geography $b) {
		if ($a->geographyTypeUri == self::$uris_os_County) {
			return -1;
		}
		return 1;
	}
	
	
	private function readGeographies(sparqlReader $sparqlReader, database $database) {
		$geographies = array();
		$json = $sparqlReader->query(self::$sparql_selectGeographies);

		foreach ($json->results->bindings as $binding) {
			$geography = new geography();
			$geography->uri = $binding->osUri->value;
			$geography->label = $binding->label->value;
			$geography->code = $binding->code->value;
			$geography->geographyTypeUri = $binding->geographyTypeUri->value;
			$geography->authorityUri = $binding->authorityUri->value;
			if (isset($binding->containedByUri)) {
				$geography->containedByUri = $binding->containedByUri->value;
			}
			
			$geographies[] = $geography;
		}
		
		$thisClassName = get_class($this);
		usort($geographies, array($thisClassName, "sortCountiesFirst"));

		foreach ($geographies as $geography) {
			$database->insertGeography($geography);
		}
		
		echo("finished reading geographies\n");
			
		return $geographies;
	}
	
	
	private function readNeighbouringGeographies(sparqlReader $sparqlReader, database $database) {
		$json = $sparqlReader->query(self::$sparql_selectNeighbouringGeographies);
		
		$neighbours = array();
		
		foreach ($json->results->bindings as $binding) {
			$neighbour = new neighbour();
			$neighbour->geographyUri = $binding->uri->value;
			$neighbour->neighbourUri = $binding->neighbourUri->value;
			
			$neighbours[] = $neighbour;
		}
		
		foreach ($neighbours as $neighbour) {
			$database->insertGeography_geographicNeighbour($neighbour);
		}
		
		echo("finished reading neighbours\n");
		
		return $neighbours;
	}


	private function readGeographicHierarchies(sparqlReader $sparqlReader, database $database) {
		$json = $sparqlReader->query(self::$sparql_selectGeographicHierarchies);
		
		$hierarchies = array();
		
		foreach ($json->results->bindings as $binding) {
			$hierarchy = new geographyHierarchy();
			$hierarchy->parentUri = $binding->parentUri->value;
			$hierarchy->childUri = $binding->childUri->value;
			
			$hierarchies[] = $hierarchy;
		}
		
		foreach ($hierarchies as $hierarchy) {
			$database->insertGeography_geographicHierarchy($hierarchy);
		}
		
		echo("finished reading geographic hierarchies\n");
		
		return $hierarchies;
	}
}


new getAuthorityData();


?>