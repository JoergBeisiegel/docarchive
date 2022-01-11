<?php
namespace seitensteuerung;

class FormData
{
	use \traits\ArrayMappable; // Trait einbinden

	public function __construct(array $array = array())
	{
		$this->mapFromArray($array);
	}
}
?>