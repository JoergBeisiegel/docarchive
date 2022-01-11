<?php
namespace seitensteuerung;

class Form
{
	private $categories = array();

	public function getResult()
	{
		return $this->result;
	}
	public function setResult($result)
	{
		$this->result = $result;
	}
	public function getCategoryCollection()
	{
		$categoryCollection = new \klassen\CategoryCollection();
		return $categoryCollection->getCategories();
	}
	
	public function getCategoriesToOptionList($selectedIndex = 0)
	{
		$list = NULL;
		$categories = $this->getCategoryCollection();
		$templateName = "templates/select_categories_option_list_item.html";
		$template = file_get_contents($templateName);
		$categories = $this->getCategoryCollection();
		foreach($categories as $category)
		{
			$selected = $category->getCategoryId() == $selectedIndex ? " selected" : "";
			$replace = array(
								"__#__CATEGORY_ID__#__"				=> $category->getCategoryId(),
								"__#__CATEGORY_NAME__#__"			=> $category->getCategoryName(),
								"__#__IS_SELECTED__#__"				=> $selected,
			);
			// Platzhalter in HTML-Fragment ersetzen
			$list .= self::fillTemplateString($replace, $template);
		}
		return $list;
	}

	public function getCategoriesToOptionListWithMultiPreselection($preselection = array())
	{
		$list = NULL;
		$categories = $this->getCategoryCollection();
		$templateName = "templates/select_categories_option_list_item.html";
		$template = file_get_contents($templateName);
		$categories = $this->getCategoryCollection();
		foreach($categories as $category)
		{
			$selected = in_array($category->getCategoryId(), $preselection) ? " selected" : "";
			$replace = array(
								"__#__CATEGORY_ID__#__"				=> $category->getCategoryId(),
								"__#__CATEGORY_NAME__#__"			=> $category->getCategoryName(),
								"__#__IS_SELECTED__#__"				=> $selected,
			);
			// Platzhalter in HTML-Fragment ersetzen
			$list .= self::fillTemplateString($replace, $template);
		}
		return $list;
	}

	public function getCategoriesToDataList()
	{
		$list = NULL;
		$templateName = "templates/select_categories_option_list_item.html";
		$template = file_get_contents($templateName);
		$categories = $this->getCategoryCollection();
		foreach($categories as $category)
		{
			$selected = "";
			$replace = array(
								"__#__CATEGORY_ID__#__"				=> $category->getCategoryName(),
								"__#__CATEGORY_NAME__#__"			=> $category->getCategoryName(),
								"__#__IS_SELECTED__#__"				=> $selected,
			);
			// Platzhalter in HTML-Fragment ersetzen
			$list .= self::fillTemplateString($replace, $template);
		}
		return $list;
	}

	public function getCategoryByIdInCollection($categoryId)
	{
		// sucht in der Collection in einer Eigenschaft nach einem Wert
		// liefert den Index oder NULL zurück
		$category_name = null;
		$categories = (array) $this->getCategoryCollection();
		foreach($categories as $category)
		{
			if($category->getCategoryId() == $categoryId)
			{
				$category_name = $category->getCategoryName();
				return $category_name;
			}
		}
		return $category_name;
	}

	public function getBrowseDocuments($documentCollection)
	{
		$templateName = "templates/browse_documents_list_item.html";
		$template = file_get_contents($templateName);
		$list = null;
		foreach($documentCollection->getDocuments() as $document)
		{
			// Kagegorien-Objekt in Arrray, Dupliakte entfernen und in Liste umwandeln
			$categories = array();

			foreach($document->getCategories() as $category)
			{
				$categories[] = $category->getCategory_name();
			}
			$replace = array(
								"__#__DOCUMENT_ID__#__"					=> $document->getDocument_id(),
								"__#__DOCUMENT_TITLE__#__"				=> $document->getDocument_title(),
								"__#__DOCUMENT_DESCRIPTION__#__"		=> $document->getDocument_description(),
								"__#__DOCUMENT_NAME__#__"				=> $document->getDocument_name(),
								"__#__CREATION_DATE__#__"				=> $document->getCreation_date(),
								"__#__CATEGORIES__#__"					=> implode(", ", array_unique($categories)),
			);
			// Platzhalter in HTML-Fragment ersetzen
			$list .= self::fillTemplateString($replace, $template);
		}
		return $list;
	}

	public static function fillTemplateString($replacement, $templateString)
	{
		$search = array_keys($replacement);
		$replace = array_values($replacement);
		// Daten in Formular einfügen
		$templateString = \funktionen\Validator::suchen_und_ersetzen($search, $replace, $templateString);
		return $templateString["neue_zeichenkette"];
	}

	public static function fillTemplate($replacement, $templateName)
	{
		$templateString = file_get_contents($templateName);
		// Daten in Formular einfügen
		return self::fillTemplateString($replacement, $templateString);
	}
}
?>
