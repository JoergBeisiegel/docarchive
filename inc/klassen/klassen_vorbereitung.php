<?php
require("klassengenerator.php");

$neue_klasse = new Klassengenerator();
$antwort = $neue_klasse->generiere_php_datei("Document", array("Document_id", "Document_name", "Document_title", "Document_desription", "Creation_date"));
$antwort = $neue_klasse->generiere_php_datei("Category", array("Category_id","Category_name"));
$antwort = $neue_klasse->generiere_php_datei("Document_category", array("Doc_number","Cat_number"));
echo $antwort;
?>