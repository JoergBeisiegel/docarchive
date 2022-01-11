<?php

namespace traits; // Erstellen von einem Namespace

trait ArrayMappable
{
	public function mapFromArray(array $data, $checkMethod = true)
	{
		if ($data) {
			foreach ($data as $key => $value) {
				$setterName = 'set' . ucfirst($key);
				if($checkMethod == true)
				{
					if(method_exists($this, $setterName)) {
						$this->$setterName($value);
					}
				}
				elseif($checkMethod == false)
				{
					$this->$key = $value;
				}
			}
		}
	}

	public function mapToArray($withId = true)
	{
		$attributes = get_object_vars($this);

		if ($withId === false) {
			unset($attributes['id']);
		}

		return $attributes;
	}
}

?>