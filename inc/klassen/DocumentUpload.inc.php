<?php
namespace klassen;

class DocumentUpload
{
	public $uploadedDocument	= array();
	public $allowedMimeTypes = array("application/pdf"/*, "image/jpeg", "image/jpg", "image/png", "image/gif"*/);
	public $name = NULL;
	public $type = NULL;
	public $size = NULL;
	public $error = NULL;
	public $tmp_name = NULL;
	public $errorMessage = NULL;
	public $fileTarget = NULL;

	public function __construct($uploadedDocument, $document_name)
	{
		$this->setUploadedDocument($uploadedDocument, $document_name);
		$this->uploadDocument();
	}

	public function getFileTarget()
	{
		return $this->fileTarget;
	}
	public function getName()
	{
		return $this->name;
	}
	public function getUploadedDocument()
	{
		return $this->uploadedDocument;
	}
	public function setUploadedDocument($uploadedDocument, $document_name)
	{
		$this->uploadedDocument = $uploadedDocument;
		$this->name = $uploadedDocument['name'];
		$this->type = $uploadedDocument['type'];
		$this->error = $uploadedDocument['error'];
		$this->size = $uploadedDocument['size'];
		$this->tmp_name = $uploadedDocument['tmp_name'];
		$this->fileTarget = $document_name;

		return $this->isAllowedMimeType();
}

	public function isAllowedMimeType()
	{
		if ( !in_array($this->type, $this->allowedMimeTypes) )
		{
			// Fehlerfall
			$this->errorMessage = "Dies ist kein gültiger Dokumenttyp!";
			return $this->errorMessage;
		}
	}

	public function uploadDocument()
	{
		if( !$this->errorMessage ) {
			if( copy($this->tmp_name, $this->fileTarget) ) {
				// Erfolgsfall
				// echo "<p class='debugImageUpload'>Bild wurde erfolgreich unter $this->fileTarget gespeichert.</p>";
			} else {
				// Fehlerfall
				$this->errorMessage = "Fehler beim Speichern der Datei!";
			}
		}
		return array("imageError" => $this->errorMessage, "PDFPath" => $this->fileTarget);
	}
}
?>