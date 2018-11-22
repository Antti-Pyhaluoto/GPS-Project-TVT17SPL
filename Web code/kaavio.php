<?php
require "funktiot.php";

try{
	$con = openDatabase();
	if(isset($_GET['ID'])){
		$kysely = $con->prepare("SELECT Aika, Lat, Lon, HDOP, Nopeus FROM Paikka WHERE ID = " . $_GET['ID'] . " ORDER BY Aika;");
		
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

if(isset($_GET['ID'])){
		
	$eka = 0;
	$tunnit = 0;
	$minuutit = 0;
	$sekunnit = 0;
	$vali = 0;

	alkuKaavio();
	
	foreach($kaikki as $yksi){
		
		$tunnit = floor($yksi['Aika']/10000);
		
		$vali = $yksi['Aika'] - $tunnit * 10000;
		$minuutit = floor($vali/100);
		
		$sekunnit = $vali - $minuutit * 100;
		
		if($eka == 0){//Pilkku kirjoitetaan aina paitsi ensimmäisellä kerralla.
			$eka = 1;
		}
		else{
			echo ",";
		}
		echo "{
			x: moment('" . $tunnit . ":" . $minuutit . ":" . $sekunnit . "', 'HH:mm:ss'),
			y: " . $yksi['Nopeus'] . "
		}";
	}
	
	vali();
	$eka = 0;
	$mistaLat = 0;
	$mistaLon = 0;
	$etaisyys = 0;
	$kokoEtaisyys = 0;
	$lat = 0;
	$lon = 0;
	foreach($kaikki as $yksi){
		
		$tunnit = floor($yksi['Aika']/10000);
		
		$vali = $yksi['Aika'] - $tunnit * 10000;
		$minuutit = floor($vali/100);
		
		$sekunnit = $vali - $minuutit * 100;
		
		
		if($eka == 0){
			$eka = 1;
			$mistaLat = deg2rad($yksi['Lat']);
			$mistaLon = deg2rad($yksi['Lon']);
			
			echo "{
				x: moment('" . $tunnit . ":" . $minuutit . ":" . $sekunnit . "', 'HH:mm:ss'),
				y: " . $kokoEtaisyys . "
			}";
		}
		else{
			$lat = deg2rad($yksi['Lat']);
			$lon = deg2rad($yksi['Lon']);
			$etaisyys = acos(sin($mistaLat)*sin($lat)+cos($mistaLat)*cos($lat)*cos($lon-$mistaLon)) * 6371;
			
			$kokoEtaisyys = $kokoEtaisyys + $etaisyys;
			
			//echo " " . $lat . "," . $lon . "," . $mistaLat . "," . $mistaLon . "," . $etaisyys . "," . $kokoEtaisyys . " <br> ";
			
			echo ",{
				x: moment('" . $tunnit . ":" . $minuutit . ":" . $sekunnit . "', 'HH:mm:ss'),
				y: " . $kokoEtaisyys . "
			}";
			$mistaLat = deg2rad($yksi['Lat']);
			$mistaLon = deg2rad($yksi['Lon']);
		}
	}
	loppuKaavio();
}
else{
	echo"<p>Anna ID kaavion piirtämiseksi.</p>";
}
?>
