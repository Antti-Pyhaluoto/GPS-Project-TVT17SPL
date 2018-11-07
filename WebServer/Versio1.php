<?php

$servername = "localhost";	//Localhost, koska nykyinen palvelin vielä Raspberry Pi:llä.
$username = "nettisivu"; 	//Vain INSERT -oikeuksilla
$password = "Marraskuu5";
$dbname = "GPS";

							//Luo yhteys
$conn = new mysqli($servername, $username, $password, $dbname);
							//Yhteys meni pieleen
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

							//Jos saatu HTTP-viesti sisältää vastaavat muuttujat-> lue ne .php-sivulla
if( $_GET["Lon"] || $_GET["Lat"] || $_GET["Speed"] ) 
	{ 
      echo "Longitude: ". $_GET['Lon'];
      echo "<br>";
      echo "Latitude: ". $_GET['Lat'];
      echo "<br>";
      echo "Speed: ". $_GET['Speed']; 
	} 

$sql = "INSERT INTO gps_taulu (Lon, Lat, Speed) VALUES ('".$_GET["Lon"]."','".$_GET["Lat"]."','".$_GET["Speed"]."')";

if ($conn->query($sql) === TRUE) {
    echo "Uusi GPS-paikannus";
} else {
    echo "Virhe: " . $sql . "<br>" . $conn->error;
}
echo "valmis";

?>
