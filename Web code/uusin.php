<?php
require "funktiot.php";

$conn = openDatabase();

$kysely = $conn->prepare("CALL Uusin();");
$kysely->execute();
$kaikki = $kysely->fetchAll();
$kysely->closeCursor();

$eka = 0;
foreach($kaikki as $yksi){
	if($eka == 0){
		echo "Lat:" . $yksi['Lat'] . "Lon:" . $yksi['Lon'];
		$eka = 1;
	}
}
?>