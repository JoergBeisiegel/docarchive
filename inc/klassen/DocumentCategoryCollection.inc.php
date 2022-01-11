<?php
namespace klassen;

class xxxDocumentCategoryCollection
{
	private $documentCategories = array();
	use \traits\insertable; // Trait einbinden

	protected function setDocumentCategory($doc_number, $cat_number)
	{
		$newDocumentCategory = new \klassen\DocumentCategory();
		$newDocumentCategory->setDocumentCategory($doc_number,$cat_number);
		$this->documentCategories[] = $newDocumentCategory;

	}
	public function setDocumentCategories($doc_number, $catNumberArray)
	{
		foreach($catNumberArray as $cat_number)
		{
			$this->setDocumentCategory($doc_number, $cat_number);
		}
	}
	public function getDocumentCategories()
	{
		return $this->documentCategories;
	}

	public function getValuesForSQL()
	{
		$values = NULL;
		$counter = 0;
		foreach($this->documentCategories as $documentCategory)
		{
			if($counter > 0){
				$values .= ",\n";
			}
			$docNumberAndCatNumber = $documentCategory->getDocumentCategory();
			$values .= "(" . $docNumberAndCatNumber["doc_number"] . ", " . $docNumberAndCatNumber["cat_number"] . ")";
			$counter++;
		}
		return $values;
	}
	protected function saveRecords($db)
	{
		// SQL-Code erzeugen
		// doc_number und cat_number aus documentCategories auslesen
		$values = $this->getValuesForSQL();
		$sql = "insert into document_categories (doc_number, cat_number)
				values
				$values";
		// Platzhalter-Array vorbereiten
		$platzhalter = array();
		// Trait-Methode starten
		$this->sendInsertSQL($db, $sql, $platzhalter);
	}

	public function insertIntoDb($doc_number, $catNumberArray, $db)
	{
		$this->setDocumentCategories($doc_number, $catNumberArray);
		$this->saveRecords($db);
	}
}