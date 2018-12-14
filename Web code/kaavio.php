<?php
require "funktiot.php";

try{
	$con = openDatabase();
	if(isset($_GET['ID'])){
		$kysely = $con->prepare("SELECT Aika, Lat, Lon, HDOP, Paiva FROM Paikka WHERE ID = " . $_GET['ID'] . " ORDER BY Aika;");
		
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

if($_GET['ID'] > 0){
	$eka = 0;
	$tunnit = 0;
	$minuutit = 0;
	$sekunnit = 0;
	$vali = 0;
	$nopeus = 0;
	
	$vanhaAika = 0;
	$aikaSekunteina = 0;
	
	$mistaLat = 0;
	$mistaLon = 0;
	$etaisyys = 0;
	$kokoEtaisyys = 0;
	$lat = 0;
	$lon = 0;
	
	$aikaEro = 0;
	
	$valiH = 0;
	$valiM = 0;
	
	alkuKaavio();
	
	foreach($kaikki as $yksi){
		
		$tunnit = floor($yksi['Aika']/10000);
		
		$vali = $yksi['Aika'] - $tunnit * 10000;
		$minuutit = floor($vali/100);
		
		$sekunnit = $vali - $minuutit * 100;
		
		$aikaSekunteina =  $tunnit * 60 * 60 + $minuutit * 60 + $sekunnit;
		
		if($eka == 0){//Pilkku kirjoitetaan aina paitsi ensimmäisellä kerralla.
			$eka = 1;
			$mistaLat = deg2rad($yksi['Lat']);
			$mistaLon = deg2rad($yksi['Lon']);
			$vanhaAika = $aikaSekunteina;
			
			echo "{
				x: moment('" . $yksi['Paiva'] ." " . $tunnit . ":" . $minuutit . ":" . $sekunnit . "', 'YYYY-MM-DD HH:mm:ss'),
				y: 0
			}";
		}
		else{
			$lat = deg2rad($yksi['Lat']);
			$lon = deg2rad($yksi['Lon']);
			$etaisyys = acos(sin($mistaLat)*sin($lat)+cos($mistaLat)*cos($lat)*cos($lon-$mistaLon)) * 6371000;
			
			$aikaEro = $aikaSekunteina - $vanhaAika;
			
			$nopeus = $etaisyys / $aikaEro;
			
			$nopeus = $nopeus * 3.6;
			
			$valiH = 0;
			$valiM = 0;
			
			echo ",{
				x: moment('" . $yksi['Paiva'] ." " . $tunnit . ":" . $minuutit . ":" . $sekunnit . "', 'YYYY-MM-DD HH:mm:ss'),
				y: " . $nopeus . "
			}";
			$mistaLat = deg2rad($yksi['Lat']);
			$mistaLon = deg2rad($yksi['Lon']);
			$vanhaAika = $aikaSekunteina;
		}
		
		
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
				x: moment('" . $yksi['Paiva'] ." " . $tunnit . ":" . $minuutit . ":" . $sekunnit . "', 'YYYY-MM-DD HH:mm:ss'),
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
				x: moment('" . $yksi['Paiva'] ." " . $tunnit . ":" . $minuutit . ":" . $sekunnit . "', 'YYYY-MM-DD HH:mm:ss'),
				y: " . $kokoEtaisyys . "
			}";
			$mistaLat = deg2rad($yksi['Lat']);
			$mistaLon = deg2rad($yksi['Lon']);
		}
	}
	loppuKaavio();
}
else{
	/*echo"<p>Anna ID kaavion piirtämiseksi.</p>
	<p>Reitit 1, 3 paikallaan.<br>
	Reitti 4 matka koululta Verkkokauppaan.<br>
	Reitti 7 paluu koululle.<br>
	Reitit 8-11 testejä paikallaan.</p>
	<p>Live painikkeella avautuu karttanäkymä, joka hakee tietokannasta 1 sekunnin välein viimeisemmän karttapisteen.</p>
	";*/
	echo "<p>Give ID to select route.</p>
	<p>
	Routes 1, 3 stationary.<br>
	Route 2 update frequency test.<br>
	Route 4 trip from school to Verkkokauppa.<br>
	Route 7 return to school.<br>
	Routes 8-32 stationary tests.
	</p>
	<p>Live button opens a map view that displays the most recent loction point and refreshes every second.</p>
	";
}
$con = null;
?>
