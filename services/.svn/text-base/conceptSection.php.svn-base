<?php
require_once(dirname(__FILE__)."/../classes/database.class.php");
require_once(dirname(__FILE__)."/../classes/authority.class.php");
require_once(dirname(__FILE__)."/../classes/concept.class.php");

class conceptSection {
	
	public $navigation;
	public $authority;
	public $service;
	
	
	private function addNavigationLinks(database $database, navigation $navigation) {
		$navigation->links = $database->selectNavigation_links($navigation->uri);
	}
	
	
	private function addGeographicNeighbours(database $database, authority $authority) {
		$authority->geographicNeighbours = $database->selectGeographicNeighbouringAuthorities($authority->uri);
	}
	
	
	private function addGeographicParents(database $database, authority $authority) {
		$authority->geographicParents = $database->selectGeographicParentAuthorities($authority->uri);
	}
	
	
	private function addGeographicChildren(database $database, authority $authority) {
		$authority->geographicChildren = $database->selectGeographicChildAuthorities($authority->uri);
	}
	
	
	private function addServiceLinks(database $database, authority $authority, service $service) {
		$authority->links = $database->selectService_interaction_links($authority->uri, $service->uri);
	}
	
	
	public function __construct() {	
		$navigationUri = $_GET["navigationUri"];
		$authorityUri = $_GET["authorityUri"];
		
		if (empty($navigationUri) && empty($authorityUri)) {
			return;
		}
		
		$database = new database();
		
		if (!empty($navigationUri)) {
			$this->navigation = $database->selectNavigation($navigationUri);		
			$this->service = $database->selectServiceByNavigation($navigationUri);
		}
		
		if (!empty($authorityUri)) {
			$this->authority = $database->selectAuthority($authorityUri);
		}
		
		if (!empty($this->navigation)) {
			$this->addNavigationLinks($database, $this->navigation);
		}
		
		if (!empty($this->authority)) {
			$this->addGeographicParents($database, $this->authority);
			$this->addGeographicChildren($database, $this->authority);
			$this->addGeographicNeighbours($database, $this->authority);
			
			if (!empty($this->service)) {
				$this->addServiceLinks($database, $this->authority, $this->service);
				
				foreach ($this->authority->geographicParents as $a) {
					$this->addServiceLinks($database, $a, $this->service);
				}
				
				foreach ($this->authority->geographicChildren as $a) {
					$this->addServiceLinks($database, $a, $this->service);
				}
				
				foreach ($this->authority->geographicNeighbours as $a) {
					$this->addServiceLinks($database, $a, $this->service);
				}
			}
		}
	}
}

$c = new conceptSection();
?>


<?php if (!empty($c->navigation)) { ?>
	<h2><a href='<?php echo $c->navigation->uri ?>' target='_blank'><?php echo $c->navigation->label ?></a></h2>
	
	<?php if (!empty($c->navigation->links)) { ?>	
		<label>
			General links 
			(source: <a href='http://www.brent.gov.uk/opendata.nsf/pages/LBB-1' target='_blank'>Brent - LGSL/LGNL to external website mapping</a>)
		</label>
		<ul>
			<?php foreach ($c->navigation->links as $navigationLink) { ?>
			<li><a href='<?php echo $navigationLink->url ?>' target='_blank'><?php echo $navigationLink->label ?></a></li>
			<?php } ?>
		</ul>
	<?php } ?>
		
	<?php if (!empty($c->service)) { ?>
		<label>Service</label>
		<a href='<?php echo $c->service->uri ?>' target='_blank'><?php echo $c->service->label ?></a>
			
		<label>Service definition</label>
		<?php echo $c->service->definition ?>
		
		<?php if (!empty($c->authority)) { ?>			
			
			<?php if (!empty($c->authority->links)) { ?>
				<label>
					Links relating to <a href='<?php echo $c->authority->uri ?>' target='_blank'><?php echo $c->authority->label ?></a>
					(source: <a href='http://data.gov.uk/dataset/local_directgov_services' target='_blank'>Local directgov services list</a>)
				</label>
				<ul>
				<?php foreach ($c->authority->links as $l) { ?>
					<li>
						<a href='<?php echo $l->url ?>' target='_blank'><?php echo $l->label ?></a>
						<?php if (!empty($l->interactionUri)) { ?>
							(<a href='<?php echo $l->interactionUri ?>' target='_blank'><?php echo $l->interactionLabel ?></a>)
						<?php } ?>
					</li>
				<?php } ?>
				</ul>
			<?php } ?>

			<?php
				$parents = new stdClass;
				$parents->label = 'This district\'s <a href="http://data.ordnancesurvey.co.uk/ontology/admingeo/county" target="_blank">county</a> and related links ';
				$parents->label .= '(source of links: <a href="http://data.gov.uk/dataset/local_directgov_services" target="_blank">Local directgov services list</a>)';
				$parents->authorities = $c->authority->geographicParents;

				$children = new stdClass;
				$children->label = '<a href="http://data.ordnancesurvey.co.uk/ontology/admingeo/district" target="_blank">Districts</a> in this county and related links ';
				$children->label .= '(source of links: <a href="http://data.gov.uk/dataset/local_directgov_services" target="_blank">Local directgov services list</a>)';
				$children->authorities = $c->authority->geographicChildren;

				$neighbours = new stdClass;
				$neighbours->label = 'Geographic neighbours (<a href="http://data.ordnancesurvey.co.uk/ontology/spatialrelations/touches" target="_blank">touching</a>) and related links ';
				$neighbours->label .= '(source of links: <a href="http://data.gov.uk/dataset/local_directgov_services" target="_blank">Local directgov services list</a>)';
				$neighbours->authorities = $c->authority->geographicNeighbours;

				$others = array($parents, $children, $neighbours);				
			?>


			<?php foreach ($others as $other) { ?>
				<?php if (empty($other->authorities)) { continue; } ?>

				<label>
					<?php echo $other->label ?>
				</label>
				
				<ul>
					<?php foreach ($other->authorities as $a) { ?>
						<li>
							<a href='<?php echo $a->uri ?>' target='_blank'><?php echo $a->label ?></a>
							
							<ul>
								<?php foreach ($a->links as $l) { ?>
									<li>
										<a href='<?php echo $l->url ?>' target='_blank'><?php echo $l->label ?></a>
										<?php if (!empty($l->interactionUri)) { ?>
											(<a href='<?php echo $l->interactionUri ?>' target='_blank'><?php echo $l->interactionLabel ?></a>)
										<?php } ?>
									</li>
								<?php } ?>
							</ul>
						</li>
					<?php } ?>
				</ul>
			<?php } ?><?php // $others ?>
						
		<?php } else { ?><?php // else no authority ?>
		<label>
			Select a council to see any links it has for this service
		</label>
		<?php } ?><?php // authority ?>

	<?php } ?><?php // service ?>
<?php } ?><?php // navigation ?>