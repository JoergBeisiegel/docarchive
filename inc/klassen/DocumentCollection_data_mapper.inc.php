<?php
namespace klassen;

class DocumentCollection_data_mapper
{
	protected $object = array();

	// new Verpackungsart_data_mapper($this, $mode);
	public function __construct($object, $mode)
	{
		$this->object = $object;
		switch($mode)
		{
			// Alle Datensätze
			case "allelesen":
				// Select
				$array = $this->selectAllAusfuehren();
				$this->mapQueryToObject($array);
			break;
			// Gefilterte Datensätze
			case "filterlesen":
				// Select
				$array = $this->selectFilteredAusfuehren();
				$this->mapQueryToObject($array);
			break;
		}
	}

	public function mapQueryToObject($array)
	{
		// Alle Datensätze durchiterieren
		// Für jede Zeile dem object der Klasse
		// ein neues Document-Objekt hinzufügen
		$last_document_id = null;
		$objects = array();
		foreach($array as $row)
		{
			$current_document_id = $row["document_id"];
			$object = new \klassen\Document();
			if($last_document_id !== $current_document_id)
			{
				$object->setDocument_id($row["document_id"]);
				$object->setDocument_name($row["document_name"]);
				$object->setDocument_title($row["document_title"]);
				$object->setDocument_description($row["document_description"]);
				$object->setCreation_date($row["creation_date"]);
				$objects[] = $object;
			}
			$last_document_id = $row["document_id"];
		}
		// Die Kategorien werden dem object hinzugefügt
		foreach($objects as $object)
		{
			foreach ($array as $row)
			{
				if($object->getDocument_id() == $row["document_id"])
				{
					if( isset($row["category_id"]) )
					{
						$newCategory = new \klassen\Category();
						$newCategory->setCategory_id($row["category_id"]);
						$newCategory->setCategory_name($row["category_name"]);
						$object->setNewCategory($newCategory);
						// $object->setCategory($row);
						$newDocumentCategory = new \klassen\DocumentCategory();
						$newDocumentCategory->setDoc_number($row["document_id"]);
						$newDocumentCategory->setCat_number($row["category_id"]);
						$object->setNewDocumentCategory($newDocumentCategory);
						// $object->setDocumentCategory($row);
					}
				}
			}
		}
		$this->object = $objects;
	}

	public function getObject()
	{
		return $this->object;
	}

	public function selectAllAusfuehren()
	{
		$db = new \klassen\PDONamespace\Datenbank();
		$sql = "SELECT
				documents.document_id,
				documents.document_name,
				documents.document_title,
				documents.document_description,
				documents.creation_date,
				categories.category_id,
				categories.category_name
				FROM
				documents
				INNER JOIN document_categories
				ON documents.document_id = document_categories.doc_number
				INNER JOIN categories
				ON document_categories.cat_number = categories.category_id
				ORDER BY documents.creation_date DESC";
		$parameters = array();

		return $db->sql_select($sql, $parameters);

	}

	public function selectFilteredAusfuehren()
	{
		$db = new \klassen\PDONamespace\Datenbank();
		$filter = $this->getSqlFilter();
		$sql = "SELECT
				documents.document_id,
				documents.document_name,
				documents.document_title,
				documents.document_description,
				documents.creation_date,
				categories.category_id,
				categories.category_name
				FROM
				documents
				INNER JOIN document_categories
				ON documents.document_id = document_categories.doc_number
				INNER JOIN categories
				ON document_categories.cat_number = categories.category_id
				$filter
				ORDER BY documents.creation_date DESC";
		$parameters = array();
		/*
		echo "<pre>";
		echo $sql;
		echo "</pre>";
		*/

		return $db->sql_select($sql, $parameters);
	}

	public function getSqlFilter()
	{
		$filterArray =  array();
		if($this->object->getDocument_title())
		{
			$filterArray[] = "documents.document_title LIKE \"" . $this->object->getDocument_title() . "\"";
		}
		if($this->object->getDocument_description())
		{
			$filterArray[] = "documents.document_description LIKE \"" . $this->object->getDocument_description() . "\"";
		}
		if($this->object->getCategory_ids())
		{
			$filterArray[] = "categories.category_id IN (" . implode(", ", $this->object->getCategory_ids()) . ")";
		}
		return "\nWHERE\n" . implode("\nAND\n", $filterArray) . "\n";
	}

}
?>