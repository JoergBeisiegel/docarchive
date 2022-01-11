<?php
namespace funktionen;

class Validator
{
	// protected $hasErrors = false;

	use \traits\ArrayMappable; // Trait einbinden

	public function __construct(array $array, array $validations = array(), $doValidation = true)
	{
		// alle Elemente des Arrays auf Objekteigenschaften mappen
		$this->validate($array, $validations, $doValidation);
	}

	public function validate(array $array, array $validations = array(), $doValidation = true)
	{
		// alle Elemente des Arrays auf Objekteigenschaften mappen
		$this->mapFromArray($array, false);
		$this->cleanObject();
		if($doValidation === true)
		{
			$this->validateObjects($validations);
		}
	}

	// Getter und Setter
	public function getHasErrors() {
		return $this->hasErrors;
	}
	public function setHasErrors($hasErrors) {
		$this->hasErrors = $hasErrors;
	}

	public function cleanObject()
	{
		// Alle skalaren Werte im Objekt werden bereinigt
		foreach($this as $key => $value)
		{   
			if(!is_array($value))
			{
				// echo "<p>$key: $value</p>";
				$this->$key = self::eingabe_saeubern($value);
			}
		}
	}

	public function validateObjects(array $validations)
	{
		// Alle Werte im Objekt werden validiert
		foreach($validations as $key => $value)
		{
			/*
			echo "<pre>";
			echo "key: ";
			var_dump($key);
			echo "value: ";
			var_dump($value);
			echo "</pre>";
			*/
			$type = $value["type"];
			$maxLength = isset($value["maxLength"]) ? $value["maxLength"] : 50;
			$minLength = isset($value["minLength"]) ? $value["minLength"] : 3;
			$this->validateMethodString($key, $type, $maxLength, $minLength);
		}
	}

	public function getValidator()
	{
		return $this->mapToArray();
	}

	/**
	*
	* 	Prüft einen String auf Leerstring, Mindest- und Maximallänge
	*
	* 	@param String $inputString Der zu prüfende String
	* 	@param [String $minLength] Die erforderliche Mindestlänge
	* 	@param [String $maxLength] Die erlaubte Maximallänge
	*
	*
	* 	@return String/NULL Ein String bei Fehler, ansonsten NULL
	*/
	
	public function validateMethodString($method, $type, $maxLength=50, $minLength=3) {
		$errorMessage = NULL;
			/*
			echo "<pre>";
			echo "method: ";
			var_dump($this);
			echo "</pre>";
			*/
		if($type == "string")
		{
			// Prüfen auf Leerstring
			if( $this->$method === "" ) {
				$errorMessage = "Das ist ein Pflichtfeld!";
			// Mindestlänge prüfen
			} elseif( mb_strlen($this->$method) < $minLength ) {
				$errorMessage = "Muss mindestens $minLength Zeichen lang sein!";
			// Maximallänge prüfen
			} elseif( mb_strlen($this->$method) > $maxLength ) {
				$errorMessage = "Darf maximal $maxLength Zeichen lang sein!";
			}
		}
		elseif($type == "array")
		{
			if(!isset($this->$method))
			{
				$errorMessage = "Es wurde kein Wert ausgewählt!";
			}
		}

		$methodError = $method . '_error';
		$this->$methodError = $errorMessage;
		if(!$errorMessage == null)
		{
			$this->hasErrors = "true";
		}
	}

	public function isValidSearch()
	{
		$len_title =  strlen($this->document_title);
		$len_description = strlen($this->document_description);
		$len_categories = count($this->category_ids);
		if($len_title + $len_description + $len_categories == 0)
		{
			$errorMessage = "Es wurde kein Suchkriterium eingegeben!";
			$this->search_error = $errorMessage;
			$this->hasErrors = "true";
		}
		else
		{
			$this->search_error = "";
		}

	}
	
	public static function checkInputString($inputString, $maxLength=50, $minLength=3) {
		$errorMessage = NULL;

		// Prüfen auf Leerstring
		if( $inputString === "" ) {
			$errorMessage = "Das ist ein Pflichtfeld!";
		// Mindestlänge prüfen
		} elseif( mb_strlen($inputString) < $minLength ) {
			$errorMessage = "Muss mindestens $minLength Zeichen lang sein!";
		// Maximallänge prüfen
		} elseif( mb_strlen($inputString) > $maxLength ) {
			$errorMessage = "Darf maximal $maxLength Zeichen lang sein!";
		}
		return $errorMessage;
	}
	
	/**
	*
	* Prüft eine Email-Adresse auf Leerstring und Gültigkeit
	*
	* @param String $inputString Die zu prüfende Email-Adresse
	*
	*
	* @return String/NULL Ein String bei Fehler, ansonsten NULL
	*/
	public static function checkEmail($inputString) {
		$errorMessage = NULL;
		
		// Prüfen auf Leerstring
		if( $inputString === "" ) {
			$errorMessage = "Das ist ein Pflichtfeld!";
		// Email auf Validität prüfen
		} elseif( !filter_var($inputString, FILTER_VALIDATE_EMAIL) ) {
			$errorMessage = "Dies ist keine gültige Email-Adresse!";
		}
		return $errorMessage;
	}
	

	public static function leerzeichen_enfernen($zeichenkette, $sonderzeichen = null)
	{
		/*
		Die Zeichenliste könnte folgendes enthalten, ist aber optional:
		-" " (ASCII 32 (0x20)), ein normales Leerzeichen.
		-"\t" (ASCII 9 (0x09)), ein Tabulatorzeichen.
		-"\n" (ASCII 10 (0x0A)), einen Zeilenvorschub (Line Feed).
		-"\r" (ASCII 13 (0x0D)), ein Wagenrücklaufzeichen (Carriage Return)
		-"\0" (ASCII 0 (0x00)), das NULL-Byte.
		-"\x0B" (ASCII 11 (0x0B)), ein vertikaler Tabulator.
		*/
		if($sonderzeichen != null)
		{
			return trim($zeichenkette, $sonderzeichen); // nur spezielle Zeichen werden entfernt
		}
		else
		{
			return trim($zeichenkette);	// alles wird entfernt
		}
		
	}

	// Umwandeln von Zeichen in Großbuchstaben
	public static function gross_schreiben($zeichenkette)
	{
		return strtoupper($zeichenkette);
	}
	// Umwandeln von Zeichen in Kleinbuchstaben
	public static function klein_schreiben($zeichenkette)
	{
		return strtolower($zeichenkette);
	}
	// Den ersten Buchstaben klein schreiben
	public static function anfang_klein_schreiben($zeichenkette)
	{
		return lcfirst($zeichenkette);
	}
	// Dern ersten Buchstaben groß schreiben
	public static function anfang_gross_schreiben($zeichenkette)
	{
		return ucfirst($zeichenkette);
	}
	/*
	Umwandlung von < > & " 
	in &lt; &gt; &amp; &quot; - Wichtig wegen der Sicherheit
	*/
	public static function html_konvertieren($zeichenkette)
	{
		return htmlspecialchars($zeichenkette, ENT_HTML5 | ENT_QUOTES , "UTF-8");
	}

	public static function html_tags_entfernen($zeichenkette)
	{
		return strip_tags($zeichenkette);
		/*
		DIV-Tags und SPAN-Tags werden nicht entfernt
		NUR geöffnete Tags hinschreiben!!!
		
		return strip_tags($zeichenkette, '<div><span>');
		*/
	}

	// Zur Sicherheit alles bereinigen, was Probleme machen könnte
	public static function eingabe_saeubern($zeichenkette)
	{
		return self::html_konvertieren(self::html_tags_entfernen($zeichenkette));
	}

	// Zeilenumbrüche konvertieren
	public static function html_zeilenumbruch_erzeugen($zeichenkette)
	{
		return nl2br($zeichenkette, true); // <br />
		//     nl2br($zeichenkette, false); // <br>
	}

	public static function ausgabeformat($format, $array)
	{
		//vprintf(string format, array argumente)
		//$array = array("Zeichenkette", 72, 4.99);
		/*
		%s = Zeichenkette
		%.30s = Zeichenkette mit max. 30 Zeichen
		%d = ganze Zahl
		%f = Fließkommazahl
		$.2f = Fließkommazahl mit 2 Nachkommastellen
		*/
		//vprintf(string format, array argumente)
		vprintf($format, $array); // ohne return aber mit einem echo
	}

	public static function ausgabeformat2($format, $array)
	{
		return vsprintf($format, $array); // identisch wie vprintf aber mit return (ohne echo)
	}

	public static function laenge_der_zeichenkette($zeichenkette)
	{
		return strlen($zeichenkette);
	}
	// echo laenge_der_zeichenkette("Das ist die Zeichenkette");

	// Suche nach der Nadel im Heuhaufen
	public static function suche_in_der_zeichenkette($heuhaufen, $nadel)
	{
		return strpos($heuhaufen, $nadel);
	}

	// Ausschneiden
	public static function ausschneiden($zeichenkette, $start, $laenge = null)
	{
		if($laenge == null)
		{
			return substr($zeichenkette, $start); // bis zum Ende
		}
		else
		{
			return substr($zeichenkette, $start, $laenge); // Länge= Anzahl der Zeichen, die man haben möchte
		}
	}

	public static function suchen_und_ersetzen($suche, $ersatz, $vorlage)
	{
		/*
		$suche = Zeichenkette, die gesucht wird (Zeichenkette / Array)
		$ersatz = Der Ersatzinhalt (Zeichenkette / Array)
		$vorlage = Die Textvorlage
		&$anzahl = Hier wird gezählt wie oft etwas ersetzt wurde (Es ist eine Referenz auf einer Variable)
		*/
		/*
		$vorlage	= "Das ist eine Zeichenkette";
		$suche 		= "eine Zeichenkette";
		$ersatz 	= "ein Text";
		
		echo str_replace($suche, $ersatz, $vorlage);
		echo str_replace($suche, $ersatz, $vorlage, $anzahl); // $anzahl wird automatisch erzeugt
		echo "Es wurde(n) $anzahl Treffer ausgetauscht";
		echo "<hr>";
		
		$suchen 	= array("a","i","e");
		$ersetzen  	= array("X","Y","Z");
		
		echo str_replace($suchen, $ersetzen, $vorlage, $anzahl);
		echo "<pre>";
		print_r($anzahl);
		echo "</pre>";
		*/
		$ersetzte_zeichenkette = str_replace($suche, $ersatz, $vorlage, $anzahl); 
		$array = array("anzahl" => $anzahl, 
					"neue_zeichenkette" => $ersetzte_zeichenkette);
		return $array;
	}

	public static function timestamp_lesbar($timestamp)
	{
		return strftime("%d.%m.%Y %H:%M:%S", $timestamp );
	}

	public static function timestamp_erstellen($stunde, $minute, $sekunde, $monat, $tag, $jahr)
	{
		return mktime($stunde, $minute, $sekunde, $monat, $tag, $jahr);
	}

	public static function timestamp_anhand_datum_uhrzeit($datum, $uhrzeit)
	{
		$array_datum 	= explode(".", $datum); // String in Array umwandeln
		$array_uhrzeit 	= explode(":", $uhrzeit); // String in Array umwandeln
		
		$stunde 		=  $array_uhrzeit[0];
		$minute 		=  $array_uhrzeit[1];
		$sekunde 		=  $array_uhrzeit[2];
		
		$tag 			=  $array_datum[0];
		$monat			=  $array_datum[1];  
		$jahr 			=  $array_datum[2];
		
		return mktime($stunde, $minute, $sekunde, $monat, $tag, $jahr);
	}

	public static function timestamp_anhand_datum_uhrzeit2($datum, $uhrzeit)
	{
		// list erzeugt Variablen aus einem Array
		list($tag, $monat, $jahr)			= explode(".", $datum); // String in Array umwandeln
		list($stunde, $minute, $sekunde) 	= explode(":", $uhrzeit); // String in Array umwandeln
		
		return mktime($stunde, $minute, $sekunde, $monat, $tag, $jahr);
	}

	public static function zeichenkette_in_timestamp_konvertieren($datum, $uhrzeit)
	{
		return strtotime($datum . " ". $uhrzeit);
	}

}
?>