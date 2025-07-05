<?php
if (file_exists($anadizin."/_y/s/f/domain/domain.php"))require_once("$anadizin/_y/s/f/domain/domain.php");
if (file_exists($anadizin."/_y/s/f/anahtar/anahtar.php"))require_once("$anadizin/_y/s/f//anahtar/anahtar.php");
if (file_exists($anadizin."/_y/s/f/sql.php"))require_once("$anadizin/_y/s/f/sql/sql.php");
$data="";
function Veri($deger)
{
	if($deger==true)
	{
		global $servername;
		global $username;
		global $password;
		global $database;
		global $data;
		// Create connection
		$data = new mysqli($servername, $username, $password,$database);
		
		// Check connection
		if ($data->connect_error) {
			die("Veri tabanı bağlantısı sağlanamadı: " . $data->connect_error);
		} 

		$data->query("SET NAMES utf8");
		$data->query("SET CHARACTER SET utf-8");
		$data->query("SET COLLATION_CONNECTION = 'utf8mb4_unicode_ci'");
	}
}
?>