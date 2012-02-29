Some notes:

this site is currently hosted at:

odd.esd.org.uk


This is only the second web site I have written in PHP, so I have probably done many things in strange ways; missed out obvious libraries; and re-invented the wheel.

A dump of the database is in the "database" folder.  It contains data.  Connection string information needs to be configured in config.ini (there is a sample file).

There is only one web page ("index.php").  The PHP code in this file makes various calls for data (via the classes in the "classes" folder) while it is being processed.

When the page arrives at the client, it already contains a list of all authorities (in a select), and a tree of navigation and service items (as nested uls).

The page then builds the tree functionality using JavaScript (jstree library), and uses jquery to turn a select of authorities into an auto complete (the jquery UI documentation tells you how to do this, but for some reason, I never saw that, and so wrote my own script).

When you click on a node of the tree, a request is made for the content on the right hand side.  This request goes to a php called "services/conceptSection.php", and this simply delivers raw html.

When you select an authority, the right hand side is refreshed (as above), and a list of services that have data for that authority is requested.  This request is made to "services/urisWithLinks.php", and delivered as json.  This data is used to alter the image flags on the tree.  The full uri of the service is used (as opposed to just the identifier) which is probably not very efficient.

The data in the database is static.  There are 3 php files (in the "data" folder) that were used to retrieve the data from various sources.  They contain sparql queries.  The process has been repeated, but has not been automated.  The files and stored procedures might need modification before it works again (I imagine that some database dependencies mean that you won’t be able to clear all the data so easily).

I downloaded the sources of the links, and saved them in the "temp" folder.  "data/getLinkData.php" was used to read from this location, into the database.  I haven’t tried reading them directly from the source.



