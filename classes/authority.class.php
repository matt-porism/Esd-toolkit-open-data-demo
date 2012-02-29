<?php

class authority
{
	public $label;
	public $uri;
	public $code;
	public $authorityTypeUri;
	
	public function buildFromRow($row) {
		$this->uri = $row->uri;
		$this->label = $row->label;
		$this->code = $row->code;
		$this->authorityTypeUri = $row->authorityTypeUri;
	}
}

class authorityType
{
	public $label;
	public $uri;
}

class geography
{
	public $label;
	public $uri;
	public $code;
	public $geographyTypeUri;
	public $authorityUri;
	public $containedByUri;
}

class geographyType
{
	public $label;
	public $uri;
}

class neighbour
{
	public $geographyUri;
	public $neighbourUri;
}

class geographyHierarchy
{
	public $parentUri;
	public $childUri;
}

?>