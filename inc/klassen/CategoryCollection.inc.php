<?php
namespace klassen;

class CategoryCollection
{
	protected $categories = array();
	
	// use \traits\insertable; // Trait einbinden
	// use \traits\selectable; // Trait einbinden

	public function __construct($array = null)
	{
		if(empty($array)){
			$this->syncDB("allelesen");
		}
	}

	public function setNewCategories($objects)
	{
		foreach($objects as $object)
		{
			$newCategory = new Category();
			$this->categories[] = $object;
		}
	}

	public function getCategories()
	{
		return $this->categories;
	}

	public function getCategoryByIdInCollection($categoryId)
	{
		// sucht in der Collection in einer Eigenschaft nach einem Wert
		// liefert den Index oder NULL zurück
		$categoriesKey = array_search($categoryId, array_column($this->categories, "category_name"));
		return $this->categories[$categoriesKey];
	}
	
	public function syncDB($mode)
	{
		// mode = allelesen, lesen, speichern, löschen, ändern
		$mapper = new \klassen\CategoryCollection_data_mapper($this, $mode);

		$this->setNewCategories($mapper->getObject());
	}

}


?>
