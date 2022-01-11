<?php
namespace traits;

trait selectable
{
	public function sendSelectSQL(	$db,
									string $sql,
									array $platzhalter)
	{
		$id = $db->sql_select($sql, $platzhalter);
		return $id;
	}

}
?>