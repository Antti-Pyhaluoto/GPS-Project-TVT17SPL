<?php


$servername = "localhost";	//Localhost, koska nykyinen palvelin vielä Raspberry Pi:llä.
$username = "nettisivu"; 	// Kyseisellä käyttäjällä ainoastaa
$password = "Marraskuu5";	// INSERT -oikeudet
$dbname = "GPS";

							//Luo yhteys
$conn = new mysqli($servername, $username, $password, $dbname);
							//Yhteys meni pieleen
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 



if( $_GET["Lon"] || $_GET["Lat"] || $_GET["Speed"] ) 
	{ 
     
	$Lon = $_GET ['Lon'];
	$Lat = $_GET ['Lat'];
	$Speed = $_GET ['Speed'];
	
	$sec_Lon = $conn->real_escape_string($Lon);
	$sec_Lat = $conn->real_escape_string($Lat);
	$sec_Speed = $conn->real_escape_string($Speed);
	
	//$title = $db->real_escape_string($title)
     
      echo "Longitude: ". $sec_Lon;
      echo "<br>";
      echo "Latitude: ". $sec_Lat;
      echo "<br>";
      echo "Speed: ". $sec_Speed; 
	} 

$sql = "INSERT INTO gps_taulu (Lon, Lat, Speed)
		VALUES ('".$sec_Lon."','".$sec_Lat."','".$sec_Speed."')";
		

if ($conn->query($sql) === TRUE) {
    echo "Tiedot meni läpi";
} else {
    echo "Error" . $sql . "<br>" . $conn->error;
}

echo "valmis";

//$conn->close();
?>
