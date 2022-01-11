<?php
namespace klassen;

class DocumentCategory
{
	// Attribute
	protected $doc_number;
	protected $cat_number;

	use \traits\insertable; // Trait einbinden

	// GET- und SET-Methoden
	public function getDoc_number()
	{
		return $this->doc_number;
	}

	public function setDoc_number($doc_number)
	{
		$this->doc_number = $doc_number;
	}

	public function getCat_number()
	{
		return $this->cat_number;
	}

	public function setCat_number($cat_number)
	{
		$this->cat_number = $cat_number;
	}

	public function getDocNumber()
	{
		return $this->doc_number;
	}

	public function setDocNumber($doc_number)
	{
		$this->doc_number = $doc_number;
	}

	public function getCatNumber()
	{
		return $this->cat_number;
	}

	public function setCatNumber($cat_number)
	{
		$this->cat_number = $cat_number;
	}

	public function getDocumentCategory()
	{
		$array = array(
						"doc_number"			=> $this->doc_number,
						"cat_number"			=> $this->cat_number
		);
		return $array;
	}

	public function setDocumentCategory($doc_number, $cat_number)
	{
		$this->doc_number = $doc_number;
		$this->cat_number = $cat_number;
	}

	protected function saveRecord($db)
	{
		// SQL-Code erzeugen
		$sql = "insert into categories (doc_number, cat_number)
				values
				(:doc_number, :cat_number)";
		// Platzhalter-Array vorbereiten
		$platzhalter = $this->getDocumentCategory();
		// Trait-Methode starten
		$this->sendInsertSQL($db, $sql, $platzhalter);
		// return $this->doc_number; // Primärschlüssel als Antwort zurück
	}

	public function insertIntoDb($doc_number, $cat_number, $db)
	{
		$this->setDocumentCategory($doc_number, $cat_number);
		return $this->saveRecord($db);
	}
}
?>
