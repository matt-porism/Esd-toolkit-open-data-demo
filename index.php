<?php
require_once(dirname(__FILE__)."/classes/database.class.php");
require_once(dirname(__FILE__)."/classes/authority.class.php");
require_once(dirname(__FILE__)."/classes/concept.class.php");

$database = new database();

$authorities = $database->selectAuthorities();

$navigationHierarchy = $database->selectHierarchicalNavigation();

function sortByLabel(concept $a, concept $b) {
	return strcmp($a->label, $b->label);
}

function renderConcept(concept $concept) {
	$cssClass = get_class($concept);
	echo("<a class='".$cssClass."' href='#".$concept->uri."' title='no links'>");
	echo($concept->label);
	echo("</a>");
	if (empty($concept->narrowers)) {
		return;
	}
	echo("<ul>");
	$narrowers = &$concept->narrowers;
	usort($narrowers, "sortByLabel");
	foreach ($narrowers as $narrower) {
		echo("<li>");
		renderConcept($narrower);
		echo("</li>");
	}
	echo("</ul>");
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
		<meta http-equiv="X-UA-Compatible" content="chrome=1">
		<title>Open data demo</title>
		<link rel="stylesheet" href="styles/cupertino/jquery-ui-1.8.17.custom.css" type="text/css" />
		<link rel="stylesheet" href="scripts/themes/default/style.css" type="text/css" />
		<link rel="stylesheet" href="styles/default.css?v=1.1" type="text/css" />

		<script language="javascript" type="text/javascript" src="scripts/jquery-1.7.1.min.js"></script>
		<script language="javascript" type="text/javascript" src="scripts/jquery-ui-1.8.17.custom.min.js"></script>
		<script language="javascript" type="text/javascript" src="scripts/jquery.jstree.js"></script>
		<script language="javascript" type="text/javascript" src="scripts/jquery.plugins.js?v=1.1"></script>
		<script language="javascript" type="text/javascript" src="scripts/general.js?v=1.1"></script>

		<link rel="stylesheet" href="styles/overwrites.css?v=1.1" type="text/css" />
	</head>
	<body>
		<header>
			<div id="header">
				<h1>Open data demo</h1>

				<div id="authority-select">
					<label for="authority">Council</label>
					<select id="authority">
						<option value=''>please select a local authority</a>
						<?php foreach ($authorities as $authority) { ?>
						<option value='<?php echo $authority->uri ?>'>
						<?php echo $authority->label ?>
						</option>
						<?php } ?>
					</select>
				</div>
			</div>
		</header>
		<div id="article">
			<div class="left">
				<div id="navigation-tree-legend">
					<a href="#" id="navigation-tree-legend-button">key</a>
					<div id="navigation-tree-legend-content" style="display: none;">
						<ul>
							<li><img src="images/link.general.png" title="General links"> General links</li>
							<li><img src="images/link.local.png" title="Local links"> Local links</li>
							<li><img src="images/link.both.png" title="General and local links"> General and local links</li>
						</ul>
					</div>
				</div>
				<div id="navigation-tree" style="display: none;">
					<ul>
						<?php
						usort($navigationHierarchy, "sortByLabel");
						foreach ($navigationHierarchy as $navigation) { ?>
						<li>
							<?php renderConcept($navigation); ?>
						</li>
						<?php } ?>
					</ul>
				</div>
			</div>
			<div class="right">
				<div id="concept-content">
				</div>
			</div>
		</div>
	</body>
</html>

