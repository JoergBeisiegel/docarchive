<?php
namespace klassen\PDONamespace;

class Datenbank
{
	// Attribute
	public $host;
	public $port;
	public $dbname;
	public $user;
	public $kennwort;
	public $db_objekt;	// PDO-Objekt
	################################################################################################
	// Magische Methoden
	public function __construct()
	{
		$this->verbindung_herstellen();
	}
	############################################################################################

	// Methoden
	protected function verbindung_herstellen()
	{
		$this->db_objekt = new \PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME.";port:330".DB_PORT,DB_USER,DB_PWD,
			array
			(
				\PDO::ATTR_ERRMODE					=> \PDO::ERRMODE_WARNING,
				\PDO::ATTR_DEFAULT_FETCH_MODE		=> \PDO::FETCH_ASSOC,
				\PDO::MYSQL_ATTR_USE_BUFFERED_QUERY	=> true,
				\PDO::MYSQL_ATTR_INIT_COMMAND		=> "SET NAMES utf8"
			)
		);
	}

	public function abfrage_ausfuehren( $sql, $array = array() )
	{
		// Antwort 
		$antwort = $this->db_objekt->prepare($sql);
		$antwort->execute($array);
		return $antwort;
	}

	public function sql_insert( $sql, $array = array() )
	{
		$antwort = $this->abfrage_ausfuehren($sql, $array);
		// var_dump($antwort);
		return $this->db_objekt->lastInsertId();

	}
	public function sql_update( $sql, $array = array() )
	{
		$antwort = $this->abfrage_ausfuehren($sql, $array);
		if($antwort->rowCount() == 0)
		{
			return "Update fehlgeschlagen: " . $antwort->rowCount() . " Datensätze geändert";
		}
		else
		{
			return "Update erfolgreich: " . $antwort->rowCount() . " Datensätze geändert";
		}
	}
	public function sql_delete( $sql, $array = array() )
	{
		$antwort = $this->abfrage_ausfuehren($sql, $array);
		if($antwort->rowCount() == 0)
		{
			return "Löschen fehlgeschlagen: " . $antwort->rowCount() . " Datensätze gelöscht";
		}
		else
		{
			return "Löschem erfolgreich: " . $antwort->rowCount() . " Datensätze gelöscht";
		}
	}
	public function sql_select( $sql, $array = array() )
	{
		$antwort = $this->abfrage_ausfuehren($sql, $array);
		$daten = $antwort->fetchAll(); // Alle Datensätze
		return $daten;
	}
}
############################################################################################
/*
$db = new Datenbank();
$insert_id = $db->sql_insert("INSERT INTO mitarbeiter (nachname, vorname) VALUES('Mustermann','Max');");
echo $insert_id;
echo $db->sql_update("update mitarbeiter set vorname='Fritz';");
echo "<hr />";
print_r($db->sql_select("select * from mitarbeiter;"));
echo "<hr />";
echo $db->sql_delete("delete from mitarbeiter;");
*/
?>