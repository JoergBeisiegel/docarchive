<?php
namespace traits;

trait insertable
{
	public function sendInsertSQL(	$db,
									string $sql,
									array $platzhalter)
	{
		$id = $db->sql_insert($sql, $platzhalter);
		return $id;
	}

}
?>