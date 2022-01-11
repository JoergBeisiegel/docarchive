<?php
namespace klassen;

class Category
{
	// Attribute
	protected $category_id;
	protected $category_name;

	public function __construct($array = array())
	{
		if(isset($array["category_id"]))
		{
			$this->setCategoryId($array["category_id"]);
		}

		if(isset($array["category_name"]))
		{
			$this->setCategoryName($array["category_name"]);
		}

		########################################
		if(	$this->category_id !== null && count($array) == 1)
		{
			$this->syncDB("lesen");
			//echo "lesen";
		}
		########################################
		else
		if(	$this->category_id === null &&
			$this->category_name !== null)
		{
			$this->syncDB("speichern");
			//echo "speichern";
		}
		########################################
		else
		if(	$this->category_id !== null &&
			$this->category_name === null)
		{
			$this->syncDB("loeschen");
			//echo "loeschen";
		}
		########################################
		else
		if(	$this->category_id !== null &&
			$this->category_name !== null) //&&
		{
			$this->syncDB("aendern");
			//echo "aendern";
		}

	}

	// GET- und SET-Methoden
	public function getCategoryId()
	{
		return $this->category_id;
	}

	public function setCategoryId($category_id)
	{
		$this->category_id = $category_id;
	}

	public function getCategoryName()
	{
		return $this->category_name;
	}

	public function getCategory_id() {
		return $this->category_id || null;
	}
	
	public function setCategory_id($category_id)
	{
		$this->category_id = $category_id;
	}
	
	public function getCategory_name() {
		return $this->category_name;
	}
	public function setCategoryName($category_name)
	{
		$this->category_name = $category_name;
	}
	public function setCategory_name($category_name)
	{
		$this->category_name = $category_name;
	}

	###########################################################
	// Aufruf Data-Mapper
	###########################################################
	public function syncDB($mode)
	{
		// mode = lesen, speichern, löschen, ändern
		$mapper = new \klassen\Category_data_mapper($this, $mode);
		$this->setCategoryId($mapper->object->getCategoryId());
		$this->setCategoryName($mapper->object->getCategoryName());
	}
}
?>
