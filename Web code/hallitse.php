<?php
require "funktiot.php";

$con = openDatabase();

try{
	$con = openDatabase();
	$kysely = $con->prepare("UPDATE Hallinta SET Taajuus = " . $_GET['taajuus'] . " WHERE ID = 1");
	$kysely->execute();
	//$kaikki = $kysely->fetchAll();
	//$kysely->closeCursor();
	$con = null;
	
	echo "Arvo muutettu " . $_GET['taajuus'] . " sekuntiin.";
}
catch(PDOException $e){
	echo "Tapahtui vihre.";
	die("Virhe:" . $e->getMessage());
}

?>
