<?php

function openDatabase(){//Luodaan tietokanta yhteys ja palautetaan se.
	$host="localhost";
	$database="testi";
	$username="sivu";
	$password="sivu1234";
	
	try{
		$con = new PDO("mysql:host=$host;dbname=$database", $username, $password);
		$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
		$con->exec("SET NAMES utf8");
		
		//echo "Yhteys toimii";
		
		return $con;
	}
	catch(PDOException $e){
		echo "Ei yhteyttä: " . $e->getMessage();
	}
}

function alku($Lon, $Lat){
	echo '<!DOCTYPE HTML>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	
	<title>GPS project</title>
	<style type="text/css">
		html, body, #basicMap {
		width: 100%;
		height: 100%;
		margin: 0;
		}
		img.olTileImage {
        max-width: none;
    }
	</style>
	<!-- <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css"></link> -->
	<script src="OpenLayers/OpenLayers.js"></script>
	<script>
		function init() {
			//Luodaan kartta ja määritellään sille oletus sijainti ja oletus zoom.
			map = new OpenLayers.Map("basicMap");
			var mapnik         = new OpenLayers.Layer.OSM();
			var fromProjection = new OpenLayers.Projection("EPSG:4326");   // Transform from WGS 1984
			var toProjection   = new OpenLayers.Projection("EPSG:900913"); // to Spherical Mercator Projection
			var position       = new OpenLayers.LonLat('. $Lon .',' . $Lat . ').transform( fromProjection, toProjection);
			var zoom           = 15; 
			
			//Lisättään kartalle ulkonäkö(?) ja otetaan oletus arvot käyttöön.
			map.addLayer(mapnik);
			map.setCenter(position, zoom );
			
			//Määritellään pisteet.
			
			var points = new Array(';
}
function loppu(){
	echo"
	);
			
			/*
			var points = new Array();
			for(i = 0; i < tiedon pituus; i++){
				points.push(new OpenLayers.Geometry.Point(Lon, Lat).transform( fromProjection, toProjection));
			}
			*/

			var style = new OpenLayers.StyleMap({
				'strokeWidth': 3,
				'strokeColor': '#000000'
			});
			// new vector graphic layer
			var route = new OpenLayers.Layer.Vector(\"Route\", {styleMap: style});

			// Make line
			var line = new OpenLayers.Feature.Vector(new OpenLayers.Geometry.LineString(points));
			// Add new feature to layer named by vector
			route.addFeatures([line]);
			// Add vector layer to map
			map.addLayers([route]);
			
			
		}
	</script>
</head>
<body onload=\"init();\">
<div id=\"basicMap\"></div>

</body>
</html>
	";
}

?>