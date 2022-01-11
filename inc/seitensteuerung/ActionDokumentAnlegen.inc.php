<?php
namespace seitensteuerung;

class ActionDokumentAnlegen extends Seitensteuerung
{
	// Eigenschaften
	public $h2 = "Archivdokument anlegen";
	public $document_id;
	public $document_title;
	public $document_title_error;
	public $document_description;
	public $document_description_error;
	public $category_ids = array();
	public $category_ids_error;

	// Methoden
	public function getDocument_id()
	{
		return $this->document_id;
	}
	public function setDocument_id($document_id)
	{
		$this->getDocument_id = $document_id;
	}

	public function getDocument_title()
	{
		return $this->document_title;
	}
	public function setDocument_title($document_title)
	{
		$this->document_title = $document_title;
	}

	public function getDocument_title_error()
	{
		return $this->document_title_error;
	}
	public function setDocument_title_error($document_title_error)
	{
		$this->document_title_error = $document_title_error;
	}







	function action_document_anlegen()
	{
		//Formulardaten bereinigen, optional validieren und in die Eigenschaft schreiben
		if(isset($_POST["userForm"]))
		{
			$validations = array(
									"document_title" => ["type" => "string", "maxLength" => 50, "minLength" => 3], 
									"document_description" => ["type" => "string", "maxLength" => 255, "minLength" => 5],
									"category_ids" => ["type" => "array", "min_length" => 0]
				);
			$validator = new \funktionen\Validator($_POST, $validations);
			// $this->formData = $validator->getValidator();
			$this->formData = $validator;
		}

		// Datenbank mit Formulardaten füllen
		if( isset($this->formData->userForm) && !isset($this->formData->hasErrors) )
		{
			$category_ids = isset($this->formData->category_ids) ? $this->formData->category_ids : null;
			// Neues Archivdokument erstellen
			$constructor = array(
									"document_title"		=> $this->formData->document_title,
									"document_description"	=> $this->formData->document_description,
									"category_ids"			=> $category_ids
			);
			$document = new \klassen\Document($constructor);
			unset($this->formData);

			// TODO: Meldungen über Erfolg oder Misserfolg ausgeben
		}

		// Auswahlliste mit Optionen erstellen
		$categoryCollection = new \seitensteuerung\Form();
		$selectCategoryOptions = $categoryCollection->getCategoriesToOptionList();

		// Formular zum Erfassen neuer Dokumente in der Datenbank
		$formTemplate = "templates/new_document_form.html";
		// Daten in Formular einfügen
		$document_id = isset($this->formData->document_id) ? $this->formData->document_id : "";
		$document_title = isset($this->formData->document_title) ? $this->formData->document_title : "";
		$document_title_error = isset($this->formData->document_title_error) ? $this->formData->document_title_error : "";
		$document_description = isset($this->formData->document_description) ? $this->formData->document_description : "";
		$document_description_error = isset($this->formData->document_description_error) ? $this->formData->document_description_error : "";
		$category_ids = isset($this->formData->category_ids) ? $this->formData->category_ids : array();
		$category_ids_error = isset($this->formData->category_ids_error) ? $this->formData->category_ids_error : "";
		$replace = array(
							"__#__H2__#__"							=> $h2,
							"__#__CATEGORYLIST__#__"				=> $selectCategoryOptions,
							"__#__DOCUMENT_ID__#__"					=> $document_id,
							"__#__DOCUMENT_TITLE__#__"				=> $document_title,
							"__#__DOCUMENT_TITLE_ERROR__#__"		=> $document_title_error,
							"__#__DOCUMENT_DESCRIPTION__#__"		=> $document_description,
							"__#__DOCUMENT_DESCRIPTION_ERROR__#__"	=> $document_description_error,
							"__#__CATEGORY_IDS_ERROR__#__"			=> $category_ids_error
		);
		$this->content = \seitensteuerung\Form::fillTemplate($replace, $formTemplate);
	}
}
?>