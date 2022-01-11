<?php
namespace klassen;

class Document
{
	// Attribute
	protected $document_id;
	protected $document_name = "";
	protected $document_title;
	protected $document_desription = "";
	protected $creation_date;
	protected $documentCategories = array();
	protected $categories = array();
	protected $action;
	protected $category_ids = array();
	protected $insertCategories = array();
	protected $deleteCategories = array();
	protected $sqlAnswer;
	
	use \traits\ArrayMappable; // Trait einbinden

	public function __construct($array = array())
	{
		// alle Elemente des Arrays auf Objekteigenschaften mappen
		$this->mapFromArray($array);

		// Falls Datei hochgeladen wurde, diese verarbeiten
		$this->uploadDocument();


		########################################
		if( $this->document_id !== null && $this->action == "getDocumentCategories")
		{
			$this->syncDB("getDocumentCategories");
			$this->setCategoryAction();
			// echo "getDocumentCategories";
		}
		########################################
		else
		if( $this->document_id !== null && count($array) == 1)
		{
			$this->syncDB("lesen");
			// echo "lesen";
		}
		########################################
		else
		if( $this->document_id === null &&
			$this->document_title !== null)
		{
			if(isset($array["category_ids"]))
			{
				$this->setInsertCategories($array["category_ids"]);
			}
			$this->syncDB("speichern");
			// echo "speichern";
		}
		########################################
		else
		if( $this->document_id !== null &&
			$this->document_title === null)
		{
			$this->syncDB("loeschen");
			echo "loeschen";
		}
		########################################
		else
		if( $this->document_id !== null &&
			$this->document_title !== null) //&&
		{
			// Zugewiesene Tags auslesen
			$this->syncDB("getDocumentCategories");
			$this->setCategoryAction();
			$this->syncDB("aendern");
			// echo "aendern";
		}

	}

	// GET- und SET-Methoden
	public function getDocument_id()
	{
		return $this->document_id;
	}

	public function setDocument_id($document_id)
	{
		$this->document_id = $document_id;
	}

	public function getDocument_name()
	{
		return $this->document_name;
	}

	public function setDocument_Name($document_name)
	{
		$this->document_name = $document_name;
	}

	public function getDocument_title()
	{
		return $this->document_title;
	}

	public function setDocument_title($document_title)
	{
		$this->document_title = $document_title;
	}

	public function getDocument_description()
	{
		return $this->document_desription;
	}

	public function setDocument_description($document_desription)
	{
		$this->document_desription = $document_desription;
	}

	public function getCreation_date()
	{
		return $this->creation_date;
	}

	public function setCreation_date($creation_date)
	{
		$this->creation_date = $creation_date;
	}

	public function getDocumentCategories()
	{
		return $this->documentCategories;
	}

	public function setDocumentCategories($documentCategories)
	{
		$this->documentCategories = $documentCategories;
	}

	public function setNewDocumentCategory($documentCategories)
	{
		$this->documentCategories[] = $documentCategories;
	}

	public function getCategories()
	{
		return $this->categories;
	}    

	public function setCategories($categories)
	{
		$this->categories = $categories;
	}

	public function setNewCategory($categories)
	{
		$this->categories[] = $categories;
	}

	public function setDocumentCategory(array $documentCategory)
	{
		$newDocumentCategory = new \klassen\DocumentCategory();
		$newDocumentCategory->setDoc_number($documentCategory["doc_number"]);
		$newDocumentCategory->setCat_number($documentCategory["cat_number"]);
		$this->documentCategories[] = $newDocumentCategory;
	}

	public function setCategory(array $category)
	{
		$newCategory = new \klassen\Category();
		$newCategory->setCategory_id($category["category_id"]);
		$newCategory->setCategory_name($category["category_name"]);
		$this->Categories[] = $category;
	}

	public function getDocument()
	{
		return $this;
	}

	protected function setDocument(array $array)
	{
		// $this->setDocument_name("uploaded_documents/" . uniqid() . ".pdf");
		$this->document_title = $array["document_title"];
		$this->document_description = $array["document_description"];
	}

	###########################################################
	// Aufruf Data-Mapper
	###########################################################
	public function syncDB($mode)
	{
		// mode = lesen, speichern, löschen, ändern
		$mapper = new \klassen\Document_data_mapper($this, $mode);

		$this->setDocument_id($mapper->object->getDocument_id());
		if(null !== $mapper->object->getDocument_name())
		{
			$this->setDocument_name($mapper->object->getDocument_name());
		}
		$this->setDocument_title($mapper->object->getDocument_title());
		$this->setDocument_description($mapper->object->getDocument_description());
		$this->setCreation_date($mapper->object->getCreation_date());
		$this->setDocumentCategories($mapper->object->getDocumentCategories());
		$this->setCategories($mapper->object->getCategories());
	}
	###########################################################
	// Aufruf Data-Mapper Ende
	###########################################################

	public function deleteFile()
	{
		if($this->getDocument_name())
		{
			 if( unlink($this->getDocument_name()) ) {
				// Erfolgsfall
				// echo "<p class='debugImageUpload'>Datei wurde erfolgreich gelöscht.</p>";
			} else {
				// Fehlerfall
				return "Fehler beim Löschen der Datei!";
			}
		}
	}

	public function uploadDocument()
	{
		// TODO: Abfrage und Datei löschen in Upload-Klasse verlagern
		// Falls Datei hochgeladen wurde, diese verarbeiten
		// if($_FILES['uploadedDocument'])
		if(isset($_FILES['uploadedDocument']) && $_FILES['uploadedDocument']["tmp_name"] !== "")
		{
			// Wenn document_name bereits einen Wert hat,
			// muss diese Datei vor dem Überschreiben gelöscht werden
			$this->deleteFile();
			$this->setDocument_name("uploaded_documents/" . uniqid() . ".pdf");
			new \klassen\DocumentUpload(
											$_FILES['uploadedDocument'],
											$this->getDocument_name()
										);
		}
	}

	public function setCategoryAction()
	{
		//Auswahl, ob Einfügen oder Löschen
		$category_ids = $this->getCategory_ids();
		$currentCategories = array_column($this->getDocumentCategories(), 'cat_number');

		$this->setInsertCategories(array_diff($category_ids, $currentCategories));
		$this->setDeleteCategories(array_diff($currentCategories, $category_ids));
	}

	public function getInsertCategories() {
		return $this->insertCategories;
	}
	public function setInsertCategories($insertCategories) {
		$this->insertCategories = $insertCategories;
	}
	public function getDeleteCategories() {
		return $this->deleteCategories;
	}
	public function setDeleteCategories($deleteCategories) {
		$this->deleteCategories = $deleteCategories;
	}
	public function getAction() {
		return $this->action;
	}
	public function setAction($action) {
		$this->action = $action;
	}
	public function getCategory_ids() {
		return $this->category_ids;
	}
	public function setCategory_ids($category_ids) {
		$this->category_ids = $category_ids;
	}
	public function getSqlAnswer() {
		return $this->sqlAnswer;
	}
	public function setSqlAnswer($sqlAnswer) {
		$this->sqlAnswer = $sqlAnswer;
	}

	public function getDocumentCategorNumbers()
	{
		$array = array();
		foreach($this->documentCategories as $documentCategory)
		{
			$array[] = $documentCategory->getCat_number();
		}
		return $array;
	}
}
?>