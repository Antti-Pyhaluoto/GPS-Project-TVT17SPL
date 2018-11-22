<?php
require "funktiot.php";

try{
	$con = openDatabase();
	if(isset($_GET['ID'])){
		$kysely = $con->prepare("SELECT Aika, Lat, Lon, HDOP, Nopeus FROM Paikka WHERE ID = " . $_GET['ID'] . ";");
		
		$kysely->execute();
		$kaikki = $kysely->fetchAll();
		$kysely->closeCursor();
	}else{
	}
}
catch(PDOException $e){
	echo "Tapahtui vihre.";
	die("Virhe:" . $e->getMessage());
}
$eka = 0;
if(isset($kaikki) && !empty($kaikki)){
	$keskiLat = 0;
	$keskiLon = 0;
	$i = 0;
	foreach($kaikki as $yksi){
		$i = $i + 1;
		$keskiLat = $keskiLat + $yksi['Lat'];
		$keskiLon = $keskiLon + $yksi['Lon'];
	}
	$keskiLat = $keskiLat / $i;
	$keskiLon = $keskiLon / $i;
}else{
	$keskiLat = 64.99891;
	$keskiLon = 25.51198;
}


alku($keskiLon, $keskiLat);

if($_GET['ID']){
	foreach($kaikki as $yksi){
		if($eka == 0){
			$eka = 1;
		}
		else{
			echo ",";
		}
		echo "new OpenLayers.Geometry.Point(". $yksi['Lon'] ."," . $yksi['Lat'] .").transform( fromProjection, toProjection)";
	}
}
loppu();

?>