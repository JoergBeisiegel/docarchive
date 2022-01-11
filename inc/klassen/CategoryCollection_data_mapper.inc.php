<?php
namespace klassen;

class CategoryCollection_data_mapper
{
	protected $object = array();

	// new Verpackungsart_data_mapper($this, $mode);
	public function __construct($object, $mode)
	{
		switch($mode)
		{
			// Ein Datensatz
			case "allelesen":
				// Select
				$array = $this->selectAllAusfuehren();
				// Alle Datensätze durchiterieren
				// Für jede Zeile dem object der Klasse
				// ein neues Category-Objekt hinzufügen
				foreach($array as $row)
				{
					$category = new \klassen\Category();
					foreach ($row as $key => $value)
					{
						$newKey = "set" . ucfirst($key);
						$category->{$newKey}($value);
					}
					$this->object[] = $category;
				}
			break;
		}
	}

	public function getObject()
	{
		return $this->object;
	}

	public function selectAllAusfuehren()
	{
		$db = new \klassen\PDONamespace\Datenbank();
		$sql = "SELECT category_id, category_name
				FROM categories";
		$parameters = array();

		return $db->sql_select($sql, $parameters);

	}
}
?>