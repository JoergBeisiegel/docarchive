<?php
namespace klassen;

class Document_data_mapper
{
	public $object;

	public function __construct($object, $mode)
	{
		$this->object = $object;
		switch($mode)
		{
			// Ein Datensatz
			case "lesen":
				// Select
				$array = $this->selectQuery();

				if( isset($array[0]) )
				{
					// ein neues Document-Objekt hinzufügen
					$object = new \klassen\Document();

					// Diese Informationen sind in allen Ergebnissätzen des JOINS gleich
					// und werden nur einmal benötigt
					$object->setDocument_id($array[0]["document_id"]);
					$object->setDocument_name($array[0]["document_name"]);
					$object->setDocument_title($array[0]["document_title"]);
					$object->setDocument_description($array[0]["document_description"]);
					$object->setCreation_date($array[0]["creation_date"]);

					// Ergebnissäte für Kategorien aus JOIN auslesen
					foreach ($array as $row)
					{
						if( isset($row["category_id"]) )
						{
							$newCategory = new \klassen\Category();
							$newCategory->setCategory_id($row["category_id"]);
							$newCategory->setCategory_name($row["category_name"]);
							// $object->categories[] = $newCategory;
							$object->setNewCategory($newCategory);


							$newDocumentCategory = new \klassen\DocumentCategory();
							$newDocumentCategory->setDoc_number($row["document_id"]);
							$newDocumentCategory->setCat_number($row["category_id"]);
							$object->setNewDocumentCategory($newDocumentCategory);
						}
					}
					$this->object = $object;
				}
				else
				{
					// document_id der Rückgabe muss null werden!!!
					$object->setDocument_id(null);
				}
			break;
			case "speichern":
				// Insert
				$this->object->setDocument_id($this->insertQuery());

				// Nur, wenn Kategorie-Ids vorhanden sind...
				if($this->object->getInsertCategories())
				{
					$this->insertDocumentCategoriesQuery();
				}
			break;
			case "aendern":
				// Neue Kategorien einfügen
				if(!empty($this->object->getInsertCategories()))
				{
					$this->insertDocumentCategoriesQuery();
				}

				// Kategorien löschen
				if(!empty($this->object->getDeleteCategories()))
				{
					$this->deleteSelectedDocumentCategoriesQuery();
				}

				// Update documents
				$this->object->setSqlAnswer($this->updateQuery());
			break;
			case "loeschen":
				// Delete
				// Zuerst ausgewählte Kategorien löschen
				$this->deleteAllDocumentCategoriesQuery();
				$this->object->setSqlAnswer($this->deleteQuery());
				$this->object->setDocument_id(null);
				//echo $antwort;
			break;
			case "getDocumentCategories":
				$this->object->setDocumentCategories($this->getDocumentCategoriesQuery());
			break;
		}
	}

	public function getObject()
	{
		return $this->object;
	}

	public function selectQuery()
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
				WHERE documents.document_id = :document_id";
		$parameters = array(
							"document_id"			=> $this->object->getDocument_id()
							);
		return $db->sql_select($sql, $parameters);
	}

	public function selectCategoryByNameQuery()
	{
		$db = new \klassen\PDONamespace\Datenbank();
		$sql = "SELECT *
				FROM categories
				WHERE category_name = :category_name";
		$parameters = array(
							"category_name"			=> $this->object->getCategoryName()
							);
		return $db->sql_select($sql, $parameters);
	}

	public function getDocumentCategoriesQuery()
	{
		$db = new \klassen\PDONamespace\Datenbank();
		$sql = "SELECT doc_number, cat_number
				FROM document_categories
				WHERE doc_number = :doc_number";
		// Platzhalter-Array vorbereiten
		$parameters = array(
							"doc_number"			=> $this->object->getDocument_id()
		);
		return $db->sql_select($sql, $parameters);
	}

	public function deleteAllDocumentCategoriesQuery()
	{
		$db = new \klassen\PDONamespace\Datenbank();
		$sql = "DELETE FROM document_categories
				WHERE doc_number=:doc_number";
		$parameters = array(
							"doc_number"			=> $this->object->getDocument_id()
							);
		return $db->sql_delete($sql, $parameters);
	}

	public function deleteSelectedDocumentCategoriesQuery()
	{
		$db = new \klassen\PDONamespace\Datenbank();
				// Platzhalter-Array vorbereiten
				$ids = implode($this->object->getDeleteCategories(), ",");
		$sql = "DELETE FROM document_categories
				WHERE doc_number = :doc_number
				AND cat_number IN ($ids)";

		$parameters = array(
							"doc_number"			=> $this->object->getDocument_id(),
		);
	}

	public function deleteOrphanedDocumentCategoriesQuery()
	{
		$db = new \klassen\PDONamespace\Datenbank();
		$sql = "DELETE FROM document_categories
				WHERE doc_number
				NOT IN
				(
					SELECT document_id FROM documents
				)";

		// Platzhalter-Array vorbereiten
		$parameters = array(
							"doc_number"			=> $this->object->getDocument_id(),
							"cat_number"			=> implode($this->object->getDeleteCategories(), ", ")
		);
		return $db->sql_delete($sql, $parameters);
	}

	public function getValuesForSQL($objects)
	{
		$values = NULL;
		$counter = 0;
		foreach($objects as $object)
		{
			if($counter > 0){
				$values .= ",\n";
			}
			$values .= "(" . $this->object->getDocument_id() . ", " . $object . ")";
			$counter++;
		}
		return $values;
	}

	public function insertDocumentCategoriesQuery()
	{
		$db = new \klassen\PDONamespace\Datenbank();
		$values = $this->getValuesForSQL($this->object->getInsertCategories());
		$sql = "insert into document_categories (doc_number, cat_number)
				values
				$values";
		// Platzhalter-Array vorbereiten
		$parameters = array();
		return $db->sql_insert($sql, $parameters);
	}

	public function insertQuery()
	{
		$db = new \klassen\PDONamespace\Datenbank();
		// nur einfügen, wenn noch nicht vorhanden
		$sql = "insert into documents (document_name, document_title, document_description)
				values
				(:document_name, :document_title, :document_description)";
		// Platzhalter-Array vorbereiten
		$parameters = array(
							"document_name"			=> $this->object->getDocument_name(),
							"document_title"		=> $this->object->getDocument_title(),
							"document_description"	=> $this->object->getDocument_description()
							);
		return $db->sql_insert($sql, $parameters);
	}

	public function updateQuery()
	{
		$db = new \klassen\PDONamespace\Datenbank();
		$sql = "UPDATE documents
				SET
					document_title=:document_title,
					document_name=:document_name,
					document_description =:document_description
				WHERE document_id=:document_id";
		$parameters = array(
							"document_id"			=> $this->object->getDocument_id(),
							"document_title"		=> $this->object->getDocument_title(),
							"document_description"	=> $this->object->getDocument_description(),
							"document_name"			=> $this->object->getDocument_name()
							);
		return $db->sql_update($sql, $parameters);
	}

	public function deleteQuery()
	{
		$db = new \klassen\PDONamespace\Datenbank();
		$sql = "DELETE FROM documents
				WHERE document_id=:document_id";
		$parameters = array(
							"document_id"			=> $this->object->getDocument_id()
							);
		return $db->sql_delete($sql, $parameters);
	}
}
?>