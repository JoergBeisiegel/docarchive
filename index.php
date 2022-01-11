<?php
function autoLoad($name)
{
	$path = "inc" . DIRECTORY_SEPARATOR .  str_replace("\\", DIRECTORY_SEPARATOR, $name) . ".inc.php";
	if(file_exists($path))	 	{require_once($path);}
}

// Autoladen aktivieren
spl_autoload_register("autoLoad"); // Das soll die Autoload-Funktion sein

// Externe Dateien einbinden #################################################
require_once("inc/config.inc.php");
// require_once("inc/funktionen/funktionen.inc.php");

// ###########################################################################

// Wenn kein action per URL übergeben wurde, setze Standard auf 'home' #######
if(!isset($_GET["action"]))
{
	$_GET["action"] = "home";
}
// ###########################################################################

$controller = new \seitensteuerung\Seitensteuerung();
// Seitennavigation
echo $controller->selectPage($_GET["action"]); // Seite auswählen und anzeigen

?>
