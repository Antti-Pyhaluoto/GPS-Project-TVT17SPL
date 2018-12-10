<?php


$servername = "172.20.240.53";	// Koulun virtuaalipalvelin
$username = "sivu"; 			// Kyseisellä käyttäjällä ainoastaa
$password = "sivu1234";			// INSERT -oikeudet
$dbname = "Oikea";				// "Testi"/"Oikea" tietokanta

								//Luo yhteys
$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

if( isset($_GET["ID"]) || isset($_GET["Aika"]) || isset($_GET["Lat"]) || isset($_GET["Lon"]) || isset($_GET["HDOP"]))
	{ 
     
	$ID = $_GET ['ID'];
	$Aika = $_GET ['Aika'];
	$Lat = $_GET ['Lat'];		//Tallettaa GET viestissä tulleen datan muuttujiin
	$Lon = $_GET ['Lon'];
	$HDOP = $_GET ['HDOP'];
	
	$sec_ID = $conn->real_escape_string($ID);
	$sec_Aika = $conn->real_escape_string($Aika);
	$sec_Lon = $conn->real_escape_string($Lon);	//real_escape_string suodattaa erikoismerkkejä. Jos käyttäjä yrittää sisällyttää get viestiin merkkejä: , ; / tms,
	$sec_Lat = $conn->real_escape_string($Lat);	//niin real_escape_string lisää kenon, jolloin voidaan välttää ei haluttuja SQL kyselyjä.
	$sec_HDOP = $conn->real_escape_string($HDOP);
	
	//$title1 = $db->real_escape_string($title)
     
	}
	else{                 
		
		echo "ID puuttuu.\n";					
		$sql2 = "SELECT MAX(ID) AS ID FROM Paikka";
		$vastaus = ($conn->query($sql2));		//Kysytään viimeisin reitti ID. Tulostetaan verkkosivulle, 
		while($row = $vastaus->fetch_assoc()){ 	//josta laite lukee sen ja lisää arvoon +1, jolloin seuraava reitti saa uuden arvon.	
		echo "Uusi ID$".$row["ID"]." \n";
			}
	exit();
		
	} 
												//Kutsuu MySQL proseduuria, joka asettaa uudet arvot tietokantaan ja antaa sensoridatalle päivämäärän
$sql = "CALL Lisaa ('".$sec_ID."','".$sec_Aika."','".$sec_Lat."','".$sec_Lon."','".$sec_HDOP."')";
		

if ($conn->query($sql) === TRUE) {
    echo "Tiedot meni läpi \n";
    
    $sql3 = "SELECT Taajuus FROM Hallinta WHERE ID = 1";
    $taajuus = ($conn->query($sql3));			//MySQL "Hallinta" -taulu palauttaa lähetystaajuuden laitteelle. Laite lukee lähetystaajuuden
    while($row1 = $taajuus->fetch_assoc()){		
	echo "Taajuus on$".$row1["Taajuus"]." \n";
		}
	}
	else {
    echo "Virhe(taajuuden ilmoitus)" . $sql . "<br>" . $conn->error;
}

echo "Loppu.";
//$conn->close();
?>
