<?php
require "funktiot.php";

 try{
	$con = openDatabase();
	$kysely = $con->prepare("CALL Uusin();");
	
	$kysely->execute();
	$kaikki = $kysely->fetchAll();
	$kysely->closeCursor();
}
catch(PDOException $e){
	echo "Tapahtui vihre.";
	die("Virhe:" . $e->getMessage());
}

$eka = 0;
foreach($kaikki as $yksi){
	$keskiLat = $yksi['Lat'];
	$keskiLon = $yksi['Lon'];
}

live($keskiLon, $keskiLat); 

?>
