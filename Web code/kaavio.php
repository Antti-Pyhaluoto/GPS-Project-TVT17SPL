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

if($_GET['ID'] > 0){
		
	$eka = 0;
	$tunnit = 0;
	$minuutit = 0;
	$sekunnit = 0;
	$vali = 0;
	$nopeus = 0;
	
	$vanhaAika = 0;
	
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
		
		if($eka == 0){//Pilkku kirjoitetaan aina paitsi ensimmäisellä kerralla.
			$eka = 1;
			
			$eka = 1;
			$mistaLat = deg2rad($yksi['Lat']);
			$mistaLon = deg2rad($yksi['Lon']);
			$vanhaAika = $yksi['Aika'];
			
			echo "{
				x: moment('" . $tunnit . ":" . $minuutit . ":" . $sekunnit . "', 'HH:mm:ss'),
				y: 0
			}";
		}
		else{
			$lat = deg2rad($yksi['Lat']);
			$lon = deg2rad($yksi['Lon']);
			$etaisyys = acos(sin($mistaLat)*sin($lat)+cos($mistaLat)*cos($lat)*cos($lon-$mistaLon)) * 6371000;
			
			$aikaEro = $yksi['Aika'] - $vanhaAika;
			
			if(floor($aikaEro / 10000) != 0){
				$vali = floor($aikaEro / 10000);
				$valiH = $vali * 60 * 60;
			}
			if(floor($aikaEro / 100) != 0){
				$vali = floor($aikaEro / 100);
				$valim = $vali * 60;
			}
			
			$nopeus = $etaisyys / ($aikaEro + $valiH + $valiM);
			
			$valiH = 0;
			$valiM = 0;
			
			echo ",{
				x: moment('" . $tunnit . ":" . $minuutit . ":" . $sekunnit . "', 'HH:mm:ss'),
				y: " . $nopeus . "
			}";
			$mistaLat = deg2rad($yksi['Lat']);
			$mistaLon = deg2rad($yksi['Lon']);
			$vanhaAika = $yksi['Aika'];
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
	echo"<p>Anna ID kaavion piirtämiseksi.</p>
	<p>Reitit 1 ja 2 dummy dataa.<br>
	Reitit 3-9, 11-12 yksittäisiä pisteitä.<br>
	Reitti 10, Afrikan matka. Ei sudoatettu nolla tuloksia.<br>
	Reitti 13 ensimmäinen kävely - float muuttujilla.<br>
	Reitti 17 paikallan.<br>
	Reitti 18 ensimmäinen double muuttujilla.<br>
	Reitit 19 ja 20 tehty USB virtapankin avulla. Molemmat kaatuivat.<br>
	Reitti 21 paikallaa palaverin aikana.<br>
	Reitti 22 kävely. Kaatui kesken jatkettiin lennosta.<br>
	Reitti 23 jatkoa edellisestä.<br>
	Reitti 24 kävely väärillä laskuilla. Kompailattiin kesken kaiken.<br>
	Reitti 25 jatko edellisestä. Oikeilla laskelmilla.</p>
	<p>Live painikkeella avautuu karttanäkymä, joka hakee tietokannasta 1 sekunnin välein viimeisemmän karttapisteen.</p>
	";
}
?>
