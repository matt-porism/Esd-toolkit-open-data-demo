<?php

class sparqlReader
{
	private $endPointUrl = null;
	
	
	public function __construct($endPointUrl) {
		$this->endPointUrl = $endPointUrl;
	}
	
	
	
	public function query($sparql) {
		// consider using curl
		
		$fullUrl = $this->endPointUrl.urlencode($sparql);
		$resultText = file_get_contents($fullUrl);
		
		$result = json_decode($resultText);
		
		if ($result === null) {
			// try parsing as xml
			try {
				$result = self::parseXml($resultText);
			}
			catch (Exception $e) {
				echo("error parsing text as xml from: ".$fullUrl."\n");
				echo("the text was: ".$resultText."\n");
				throw($e);
			}
		}

		return $result;
	}
	
	
	private static function parseXml($xml) {
		
		$xmlObj = new SimpleXMLElement($xml);

		// make it look like the talis json object (because I have already written the stuff to handle that).
		
		$result = new stdClass();
		
		$head = new stdClass();
		$result->head = $head;
		
		$vars = array();
		$head->vars = &$vars;
		
		foreach ($xmlObj->head->variable as $xVariable) {
			$vars[] = (string)$xVariable["name"];
		}
		
		$results = new stdClass();		
		$result->results = $results;
		
		$bindings = array();
		$results->bindings = &$bindings;
		
		foreach ($xmlObj->results->result as $xResult) {
			$bindingItem = new stdClass();
			$bindings[] = $bindingItem;
			
			foreach ($xResult->binding as $xBinding) {				
				$name = (string)$xBinding["name"];
				
				$binding = new stdClass();
				$bindingItem->{$name} = $binding;
				
				$uri = $xBinding->uri;				
				$literal = $xBinding->literal;
				
				if (empty($uri) && empty($literal)) {
					throw new Exception("unknown type ".sprintf($xBinding));
				}
				$value = (string)$uri;
				$type = 'uri';
				if (empty($uri)) {
					$value = (string)$literal;
					$type = 'literal';
				}

				$datatype = (string)$literal["datatype"];
				if (!empty($datatype)) {
					$binding->{"datatype"} = $datatype;
				}
				
				$binding->type = $type;
				
				$lang = (string)$literal["lang"];
				if (!empty($lang)) {
					$binding->{"xml:lang"} = $lang;
				}
				
				$binding->value = $value;
			}
		}
		
		return $result;
	}
}
	
?>