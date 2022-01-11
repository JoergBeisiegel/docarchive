<?php
class Klassengenerator
{
	// Konstanten
	const ZEILENUMBRUCH = "\r\n"; // nicht änderbar
	const TABULATOR = "\t"; // nicht änderbar
	
	protected $klassenname;
	protected $attribute = array();
	
	protected function setKlassenname($klassenname)
	{
		$this->klassenname = $klassenname;
	}
	
	protected function getKlassenname()
	{
		return $this->klassenname;
	}
	
	protected function setAttribute($attribute)
	{
		$this->attribute = $attribute;
	}
	
	public function generiere_php_datei($klassenname, $attribute)
	{
		$this->setKlassenname($klassenname);
		$this->setAttribute($attribute);
		
		$string = "<?php" . self::ZEILENUMBRUCH;
		$string .= "class " . $this->getKlassenname() . self::ZEILENUMBRUCH;
		$string .= "{" . self::ZEILENUMBRUCH;
		
		// Attribute
		$string .= self::TABULATOR . "// Attribute" . self::ZEILENUMBRUCH;
		//		Array			Neue Variable
		foreach($attribute as $attribut)
		{
			$string .= self::TABULATOR . "protected " . '$' . lcfirst($attribut) . ";" . self::ZEILENUMBRUCH;
		}
		
		$string .= self::ZEILENUMBRUCH;
		// GET- und SET-Methoden
		$string .= self::TABULATOR . "// GET- und SET-Methoden" . self::ZEILENUMBRUCH;
		//		Array			Neue Variable
		foreach($attribute as $attribut)
		{
			$string .= self::TABULATOR . "public function get$attribut()" . self::ZEILENUMBRUCH;
			$string .= self::TABULATOR . "{" . self::ZEILENUMBRUCH;
			$string .= self::TABULATOR . self::TABULATOR . "return " . '$' . "this->" . lcfirst($attribut) . ";" . self::ZEILENUMBRUCH;
			$string .= self::TABULATOR . "}" . self::ZEILENUMBRUCH;
			
			$string .= self::TABULATOR . "public function set$attribut(" . '$' . lcfirst($attribut) . ")" . self::ZEILENUMBRUCH;
			$string .= self::TABULATOR . "{" . self::ZEILENUMBRUCH;
			$string .= self::TABULATOR . self::TABULATOR . '$' . "this->" . lcfirst($attribut) . " = " . '$' . lcfirst($attribut) . ";" . self::ZEILENUMBRUCH;
			$string .= self::TABULATOR . "}" . self::ZEILENUMBRUCH;
			$string .= self::ZEILENUMBRUCH;
		}
		
		$string .= "}" . self::ZEILENUMBRUCH;
		$string .= "?>" . self::ZEILENUMBRUCH;
		file_put_contents($this->klassenname . ".php", $string);
	}
}
?>