<?php
require "funktiot.php";

try{
	$con = openDatabase();
	if(isset($_GET['ID'])){
		$kysely = $con->prepare("SELECT Aika, Lat, Lon, Paiva FROM Paikka WHERE ID = " . $_GET['ID'] . ";");
		
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

if(isset($_GET['ID'])){
	echo "var points = new Array(";
	foreach($kaikki as $yksi){
		if($eka == 0){
			$eka = 1;
		}
		else{
			echo ",";
		}
		echo "new OpenLayers.Geometry.Point(". $yksi['Lon'] ."," . $yksi['Lat'] .").transform( fromProjection, toProjection)";
	}
	echo ");
	var ajat = new Array(";
	$eka = 0;
	$tunnit = 0;
	$vali = 0;
	$minuutit = 0;
	$sekunnit = 0;
	foreach($kaikki as $yksi){
		$tunnit = floor($yksi['Aika']/10000);
		
		$vali = $yksi['Aika'] - $tunnit * 10000;
		$minuutit = floor($vali/100);
		
		$sekunnit = $vali - $minuutit * 100;
		
		if($tunnit < 10){
			$tunnit = "0" . $tunnit;
		}
		if($minuutit < 10){
			$minuutit = "0" . $minuutit;
		}
		if($sekunnit < 10){
			$sekunnit = "0" . $sekunnit;
		}
		
		if($eka == 0){
			$eka = 1;
		}
		else{
			echo ",";
		}
		echo "'" . $yksi['Paiva'] . " " . $tunnit . ":" . $minuutit . ":" . $sekunnit . "'";
	}
	echo ");";
	
}
loppu();
$con = null;
?>
