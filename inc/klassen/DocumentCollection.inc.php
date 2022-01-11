<?php
namespace klassen;

class DocumentCollection extends Document
{
	protected $documents = array();
	
	use \traits\ArrayMappable; // Trait einbinden

	public function __construct($array = array())
	{
		// alle Elemente des Arrays auf Objekteigenschaften mappen
		$this->mapFromArray($array);
		if(empty($array)){
			$this->syncDB("allelesen");
		}
		else
		if(	isset($array["document_title"])
			&& isset($array["document_description"])
			&& isset($array["category_ids"]) )
		{
			$this->syncDB("filterlesen");
		}
	}

	public function setNewDocuments($objects)
	{
		foreach($objects as $object)
		{
			$newDocument = new Document();
			$this->documents[] = $object;
		}
	}

	public function getDocuments()
	{
		return $this->documents;
	}

	public function getDocumentByIdInCollection($documentId)
	{
		// sucht in der Collection in einer Eigenschaft nach einem Wert
		// liefert den Index oder NULL zurück
		$documentsKey = array_search($documentId, array_column($this->documents, "document_name"));
		return $this->documents[$documentsKey];
	}
	
	public function syncDB($mode)
	{
		// mode = allelesen, lesen, speichern, löschen, ändern
		$mapper = new \klassen\DocumentCollection_data_mapper($this, $mode);

		$this->setNewDocuments($mapper->getObject());
	}

}


?>
