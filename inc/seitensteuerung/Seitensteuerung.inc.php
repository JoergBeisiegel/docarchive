<?php
namespace seitensteuerung;

class Seitensteuerung
{
	// Eigenschaften
	public $action 		= "";
	public $formData 	= array();									// Formulardaten
	public $template	= "templates/grundgeruest.html";			// HTML Seite
	public $content		= "Inhalt ist noch leer";					// Das ist der Seiteninhalt

	use \traits\ArrayMappable;
	// Methoden
		function selectPage($action)
	{
		// Eigenschaft setzen aus $_GET['action=xxx']
		$this->action = $action;
		// Anzeige der Seite
		switch($this->action)
		{
			case "dokument_anlegen":		$this->action_document_anlegen();						break;
			case "dokumente_durchsuchen":   $this->action_document_browse();    					break;
			case "dokument_bearbeiten":		$this->action_document_bearbeiten();					break;
			case "dokument_loeschen":		$this->action_document_loeschen();						break;
			case "dokument_suchen":			$this->action_document_suchen();						break;
			case "kategorie_anlegen":		$this->action_kategorie_anlegen();						break;
			case "kategorie_aendern":		$this->action_kategorie_aendern();						break;
			case "kategorie_loeschen":		$this->action_kategorie_loeschen();						break;
			case "home":					$this->action_home();									break;
			default:						$this->action_error_page();
		}

		// Template-Vorlage holen (Datei lesen und in Variable speichern)
		$h1 = "Dokumentenverwaltung";
		$replace = array(
							"__#__H1__#__"				=> $h1,
							"__#__CONTENT__#__"			=> $this->content
		);

		return \seitensteuerung\Form::fillTemplate($replace, $this->template);
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
			$this->formData = $validator;

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
		}


		// Auswahlliste mit Optionen erstellen
		$selectCategoryOptions = (new \seitensteuerung\Form())->getCategoriesToDataList();

		$h2 = "Archivdokument anlegen";
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

	function action_document_browse()
	{
		//Formulardaten bereinigen, optional validieren und in die Eigenschaft schreiben
		if(isset($_POST["userForm"]))
		{
			$validations = array(
									"document_id" => ["type" => "string", "maxLength" => 255, "minLength" => 1]
			);
			$validator = new \funktionen\Validator($_POST, $validations, false);
			$this->formData = $validator;
		}

		// Alle Dokumente in Datenbank suchen
		$documentCollection = new \klassen\DocumentCollection();
		$browseDocuments = (new \seitensteuerung\Form)->getBrowseDocuments($documentCollection);

		// Template-Vorlage holen (Datei lesen und in Variable speichern)
		$h2 = "Dokumente durchsuchen";
		// Formular zum Erfassen neuer Dokumente in der Datenbank
		$formTemplate = "templates/browse_documents_form.html";
		// Daten in Formular einfügen
		$replace = array(
							"__#__H2__#__"						=> $h2,
							"__#__BROWSEDOCUMENTS_LIST__#__"	=> $browseDocuments,
		);
		$this->content = \seitensteuerung\Form::fillTemplate($replace, $formTemplate);
	}

	function action_document_bearbeiten()
	{
		// Formular wird über URL-Parameter aufgerufen: /index.php?action=dokument_bearbeiten&document_id=(int)id
		if(isset($_GET["document_id"]))
		{
			$document_id = (int) $_GET["document_id"];
			$validations = array(
									"document_id" => ["type" => "string", "maxLength" => 255, "minLength" => 1], 
				);
			$validator = new \funktionen\Validator($_GET, $validations);
			$this->formData = $validator;
			// Datensatz mit der Id in Datenbank suchen
			$document = new \klassen\Document(array("document_id" => $this->formData->document_id));
			$this->formData->document_title = $document->getDocument_title();
			$this->formData->document_description = $document->getDocument_description();
			$this->formData->document_name = $document->getDocument_name();
			$this->formData->category_ids = $document->getDocumentCategorNumbers();
			// $validator = new \funktionen\Validator((array) $document);
			$document->setCategoryAction();

			//Formulardaten bereinigen, optional validieren und in die Eigenschaft schreiben
			if(isset($_POST["userForm"]))
			{
				// Zurück zur Übersicht, wenn Abbrechen gedrückt wird
				if(isset($_POST["Abbrechen"]))
				{
					header("Location: index.php?action=dokumente_durchsuchen");
				}
				$validations = array(
										"document_title" => ["type" => "string", "maxLength" => 50, "minLength" => 3], 
										"document_description" => ["type" => "string", "maxLength" => 255, "minLength" => 5],
										"category_ids" => ["type" => "array", "min_length" => 0]
				);
				$validator = new \funktionen\Validator($_POST, $validations);
				$this->formData = $validator;
			}
	
			// Datenbank mit Formulardaten füllen
			if( isset($this->formData->userForm) && !isset($this->formData->hasErrors) )
			{
				$category_ids = isset($this->formData->category_ids) ? $this->formData->category_ids : null;
				// Neues Archivdokument erstellen
				$constructor = array(
										"document_id"			=> $this->formData->document_id,
										"document_title"		=> $this->formData->document_title,
										"document_description"	=> $this->formData->document_description,
										"document_name"			=> $this->formData->document_name,
										"category_ids"			=> $category_ids
				);
				$document = new \klassen\Document($constructor);
				header("Location: index.php?action=dokumente_durchsuchen");
	
				// TODO: Meldungen über Erfolg oder Misserfolg ausgeben
			}
	
			// Auswahlliste mit Optionen erstellen
			$categoryCollection = new \seitensteuerung\Form();
			$selectCategoryOptions = $categoryCollection->getCategoriesToOptionListWithMultiPreselection($this->formData->category_ids);
	
			$h2 = "Archivdokument anlegen";
			// Formular zum Erfassen neuer Dokumente in der Datenbank
			$formTemplate = "templates/edit_document_form.html";
			// Daten in Formular einfügen
			$document_id = isset($this->formData->document_id) ? $this->formData->document_id : "";
			$document_title = isset($this->formData->document_title) ? $this->formData->document_title : "";
			$document_title_error = isset($this->formData->document_title_error) ? $this->formData->document_title_error : "";
			$document_name = isset($this->formData->document_name) ? $this->formData->document_name : "";
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
								"__#__DOCUMENT_NAME__#__"				=> $document_name,
								"__#__CATEGORY_IDS_ERROR__#__"			=> $category_ids_error
			);
			$this->content = \seitensteuerung\Form::fillTemplate($replace, $formTemplate);
		}
	}

	function action_document_loeschen()
	{
		//Formulardaten bereinigen, optional validieren und in die Eigenschaft schreiben
		if(isset($_POST["userForm"]))
		{
			$validations = array(
									"document_id" => ["type" => "string", "maxLength" => 255, "minLength" => 1]
			);
			$validator = new \funktionen\Validator($_POST, $validations, false);
			$this->formData = $validator;
			if(isset($_POST["button"]))
			{
				if($_POST["button"] == "Abbrechen")
				{
					header("Location: index.php?action=dokumente_durchsuchen");
				}
				elseif($_POST["button"] == "Löschen")
				{
				   // Dokument in Datenbank löschen
					$newDocument = new \klassen\Document(array(
																"document_id"			=> $this->formData->document_id,
																"document_title"		=> $this->formData->document_title,
																"document_description"	=> $this->formData->document_description,
																"document_name		"	=> $this->formData->document_name,
					));
					header("Location: index.php?action=dokumente_durchsuchen");
				}
			}
		}

		if(isset($_GET["document_id"]))
		{
			$validations = array(
									"document_id" => ["type" => "string", "maxLength" => 255, "minLength" => 1]
			);
			$validator = new \funktionen\Validator($_GET, $validations, false);
			$this->formData = $validator;
		}

		// Template-Vorlage holen (Datei lesen und in Variable speichern)
		$h2 = "Dokument wirklich löschen?";
		$document_id = isset($this->formData->document_id) ? $this->formData->document_id : "";
		// Formular zum Bestätigen des Löschens in der Datenbank
		$formTemplate = "templates/delete_document_form.html";
		// Daten in Formular einfügen
		$replace = array(
							"__#__H2__#__"						=> $h2,
							"__#__DOCUMENT_ID__#__"				=> $document_id,
		);
		$this->content = \seitensteuerung\Form::fillTemplate($replace, $formTemplate);
	}

	function action_document_suchen()
	{
		// Standardwerte für formData einfügen
		$defaults = array(
							"document_title"				=> "",
							"document_description"			=> "",
							"category_ids"					=> array(),
							"search_error"					=> "",
		);
		$this->formData =  new \funktionen\Validator($defaults);

		//Formulardaten bereinigen, optional validieren und in die Eigenschaft schreiben
		if(isset($_POST["userForm"]))
		{
			$validations = array(
									"document_title" => ["type" => "string", "maxLength" => 50, "minLength" => 0], 
									"document_description" => ["type" => "string", "maxLength" => 255, "minLength" => 0],
									"category_ids" => ["type" => "array", "min_length" => 0]
			);
			$validator = new \funktionen\Validator($_POST, $validations, false);
			$this->formData = $validator;
			// darf nicht NULL sein
			if(!isset($this->formData->category_ids))
			{
				$this->formData->category_ids = array();
			}
			// Es muss mindestens ein Suchkriterium angegeben werden
			$validator->isValidSearch();

			// Datenbank mit Formulardaten füllen
			if( isset($this->formData->userForm) && !isset($this->formData->hasErrors) )
			{
				// Gefilterte Dokumente finden
				$constructor = array(
										"document_title"		=> $this->formData->document_title,
										"document_description"	=> $this->formData->document_description,
										"category_ids"			=> $this->formData->category_ids
				);
				$documentCollection = new \klassen\DocumentCollection($constructor);
				// $document = new \klassen\Document($constructor);
				// header("Location: index.php?action=dokumente_durchsuchen");
	
				// TODO: Meldungen über Erfolg oder Misserfolg ausgeben
			}
	
		}
		if(!isset($documentCollection))
		{
			$documentCollection = new \klassen\DocumentCollection();
		}
		// Auswahlliste mit Optionen erstellen
		$selectCategoryOptions = (new \seitensteuerung\Form())->getCategoriesToOptionListWithMultiPreselection($this->formData->category_ids);

		// Alle Dokumente in Datenbank suchen
		$browseDocuments = (new \seitensteuerung\Form)->getBrowseDocuments($documentCollection);

		// Template-Vorlage holen (Datei lesen und in Variable speichern)
		$h2 = "Dokumente suchen";
		// Formular zum Erfassen neuer Dokumente in der Datenbank
		$formTemplate = "templates/search_documents_form.html";
		// Daten in Formular einfügen
		$replace = array(
							"__#__H2__#__"						=> $h2,
							"__#__DOCUMENT_TITLE__#__"			=> $this->formData->document_title,
							"__#__DOCUMENT_DESCRIPTION__#__"	=> $this->formData->document_description,
							"__#__SEARCH_ERROR__#__"			=> $this->formData->search_error,
							"__#__BROWSEDOCUMENTS_LIST__#__"	=> $browseDocuments,
							"__#__CATEGORYLIST__#__"			=> $selectCategoryOptions,
		);
		$this->content = \seitensteuerung\Form::fillTemplate($replace, $formTemplate);
	}

	function action_kategorie_anlegen()
	{

		if(isset($_POST["userForm"]))
		{
			$validations = array(
									"category_name" => ["type" => "string", "maxLength" => 30, "minLength" => 3]
			);
			$validator = new \funktionen\Validator($_POST, $validations);
			//Formulardaten in die Eigenschaft schreiben
			$this->formData = $validator;
		}

		// Auswahlliste mit Optionen erstellem
		$categoryCollection = new \seitensteuerung\Form();
		$selectCategoryOptions = $categoryCollection->getCategoriesToDataList();
		$formTemplate = "templates/new_category_form.html";
		// Daten in Formular einfügen
		$h2 = "Neue Kategorie anlegen";
		$category_name = isset($this->formData->category_name) ? $this->formData->category_name : "";
		$category_name_error = isset($this->formData->category_name_error) ? $this->formData->category_name_error : "";

		// Datenbank mit Formulardaten füllen
		if( isset($this->formData->userForm) && !isset($this->formData->hasErrors) )
		{
			// Neue Kategorie erstellen
			$category = new \klassen\category(array("category_name" => $category_name));
			// Formular zurücksetzen
			unset($this->formData);

			// TODO: Meldungen über Erfolg oder Misserfolg ausgeben
		}

		// Formular füllen
		$replace = array(
							"__#__H2__#__"						=> $h2,
							"__#__CATEGORYLIST__#__"			=> $selectCategoryOptions,
							"__#__CATEGORY_NAME__#__"			=> $category_name,
							"__#__CATEGORY_NAME_ERROR__#__"		=> $category_name_error,
		);
		$this->content = \seitensteuerung\Form::fillTemplate($replace, $formTemplate);
	}

	function action_kategorie_aendern()
	{
		// Auswahlliste mit Optionen erstellem
		$categoryCollection = new \seitensteuerung\Form();
		$selectCategoryOptions = $categoryCollection->getCategoriesToOptionList();

		$category_name = null;
		$categorySubmit = "Auswählen";
		if(isset($_POST["userForm"]))
		{
			$validations = array(
									"category_name" => ["type" => "string", "maxLength" => 30, "minLength" => 3]
			);
			// Formulardaten in die Eigenschaft schreiben
			$this->formData = new \funktionen\Validator($_POST, $validations);
			$categorySubmit = "Speichern";
			$category_name = $categoryCollection->getCategoryByIdInCollection($this->formData->category_id);
			$selectCategoryOptions = $categoryCollection->getCategoriesToOptionList($this->formData->category_id);

			if ( 
				isset($this->formData->category_id) 
				&& isset($this->formData->category_name)
				AND !isset($this->formData->hasErrors)
			   )
			{
				// Kategorie ändern
				$category = new \klassen\Category(array(
														"category_id" => $this->formData->category_id,
														"category_name" => $this->formData->category_name
														));
				// Formulardaten zurücksetzen
				$this->formData->category_id = 0;
				$this->formData->category_name = "";
				header("Location: index.php?action=kategorie_aendern");
			}
		}

		$h2 = "<h2>Kategorie ändern</h2>";
		$formTemplate = "templates/edit_category_form.html";
		$category_name_error = isset($this->formData->category_name_error) ? $this->formData->category_name_error : "";
		// Formular füllen
		$replace = array(
							"__#__H2__#__"						=> $h2,
							"__#__CATEGORYLIST__#__"			=> $selectCategoryOptions,
							"__#__CATEGORY_NAME__#__"			=> $category_name,
							"__#__CATEGORY_NAME_ERROR__#__"		=> $category_name_error,
							"__#__CATEGORY_SUBMIT__#__"			=> $categorySubmit,
		);
		$this->content = \seitensteuerung\Form::fillTemplate($replace, $formTemplate);
	}

	function action_kategorie_loeschen()
	{
		$this->formData = $_POST; //Formulardaten in die Eigenschaft schreiben

		if(isset($this->formData["userForm"]))
		{
			// Kategorie löschen
			$category = new \klassen\Category(array(
													"category_id" => $this->formData["category_id"],
													"category_name" => null
													));
		}

		// Auswahlliste mit Optionen erstellem
		$categoryCollection = new \seitensteuerung\Form();
		$selectCategoryOptions = $categoryCollection->getCategoriesToOptionList();
		// Template-Vorlage holen (Datei lesen und in Variable speichern)

		$h2 = "<h2>Kategorie löschen</h2>";
		$formTemplate = "templates/delete_category_form.html";
		// Formular füllen
		$replace = array(
							"__#__H2__#__"						=> $h2,
							"__#__OPTIONLIST__#__"				=> $selectCategoryOptions,
		);
		$this->content = \seitensteuerung\Form::fillTemplate($replace, $formTemplate);
	}

	function action_home()
	{
		$this->content = "Startseite";
	}

	function action_error_page()
	{
		$this->content = "Fehler 404<br />Seite nicht gefunden";
	}
}
?>