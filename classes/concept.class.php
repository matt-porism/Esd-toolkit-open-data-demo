<?php

class concept
{
	public $label;
	public $uri;
	public $identifier;
	public $definition;
	public $narrowers = array();
	
	public function buildFromRow($row) {
		$this->uri = $row->uri;
		$this->label = $row->label;
		$this->identifier = $row->identifier;
		if (isset($row->definition)) {
			$this->definition = $row->definition;
		}
	}
}

class service extends concept
{
}

class navigation extends concept
{
}

class interaction extends concept
{
}

class hierarchy
{
	public $narrowerUri;
	public $broaderUri;
	
	public function buildFromRow($row) {
		$this->broaderUri = $row->broaderUri;
		$this->narrowerUri = $row->narrowerUri;
	}
}

class mapping
{
	public $fromUri;
	public $toUri;
}

class navigationLink
{
	public $navigationUri;
	public $label;
	public $url;
	
	public function buildFromRow($row) {
		$this->navigationUri = $row->navigationUri;
		$this->label = $row->label;
		$this->url = $row->url;
	}
}

class serviceInteractionLink
{
	public $authorityUri;
	public $serviceUri;
	public $interactionUri;
	public $interactionLabel;
	public $label;
	public $url;
	
	
	public function buildFromRow($row) {
		$this->authorityUri = $row->authorityUri;
		$this->serviceUri = $row->serviceUri;
		$this->interactionUri = $row->interactionUri;
		$this->interactionLabel = $row->interactionLabel;
		$this->label = $row->label;
		$this->url = $row->url;
	}
}

?>