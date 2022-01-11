<?php
namespace klassen;

class Category_data_mapper
{
	public $object;

	// new Verpackungsart_data_mapper($this, $mode);
	public function __construct($object, $mode)
	{

		$this->object = $object;

		switch($mode)
		{
			// Ein Datensatz
			case "lesen":
				// Select
				$array = $this->selectAusfuehren();

				// Ergebnis vom Query an das Objekt übergeben
				if(isset($array[0]["category_id"]))
				{
					$this->object->setCategoryId($array[0]["category_id"]);
				}
				if(isset($array[0]["category_name"]))
				{
					$this->object->setCategoryName($array[0]["category_name"]);
				}
			break;
			case "speichern":
				// Insert
				$this->object->setCategoryId($this->insertAusfuehren());
			break;
			case "aendern":
				// Update
				$antwort = $this->aendernAusfuehren();
				//echo $antwort;
			break;
			case "loeschen":
				// Delete
				$antwort = $this->loeschenAusfuehren();
				//echo $antwort;
			break;

		}
	}

	public function selectAusfuehren()
	{
		$db = new \klassen\PDONamespace\Datenbank();
		$sql = "SELECT *
				FROM categories
				WHERE category_id = :category_id";
		$parameters = array(
							"category_id"		=> $this->object->getCategoryId()
							);
		return $db->sql_select($sql, $parameters);
	}

	public function selectByNameAusfuehren()
	{
		$db = new \klassen\PDONamespace\Datenbank();
		$sql = "SELECT *
				FROM categories
				WHERE category_name = :category_name";
		$parameters = array(
							"category_name"		=> $this->object->getCategoryName()
							);
		return $db->sql_select($sql, $parameters);
	}

	public function insertAusfuehren()
	{
		$db = new \klassen\PDONamespace\Datenbank();
		// nur einfügen, wenn noch nicht vorhanden
		if(!$this->selectByNameAusfuehren())
		{
			$sql = "INSERT INTO categories (category_name)
					VALUES (:category_name)";
			$parameters = array(
								"category_name"	=> $this->object->getCategoryName()
								);
			return $db->sql_insert($sql, $parameters);
		}
	}
	public function aendernAusfuehren()
	{
		$db = new \klassen\PDONamespace\Datenbank();
		$sql = "UPDATE categories
				SET category_name=:category_name
				WHERE category_id=:category_id";
		$parameters = array(
							"category_id"		=> $this->object->getCategoryId(),
							"category_name"	=> $this->object->getCategoryName()
							);
		return $db->sql_update($sql, $parameters);
	}
	public function loeschenAusfuehren()
	{
		$db = new \klassen\PDONamespace\Datenbank();
		$sql = "DELETE FROM categories
				WHERE category_id=:category_id";
		$parameters = array(
							"category_id"		=> $this->object->getCategoryId()
							);
		return $db->sql_delete($sql, $parameters);
	}
}
?>