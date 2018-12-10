<?php

function openDatabase(){//Luodaan tietokanta yhteys ja palautetaan se.
	$host="localhost";
	$database="Oikea";
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

function alkuKaavio(){ //Kaavio.php
	echo "<!DOCTYPE html>
<html>
<head>
	<meta charset='utf-8'>
	<meta name='viewport' content='width=device-width, initial-scale=1'>
	<title>GPS project</title>
</head>
<script src='js/moment.min.js'></script>
<script src='js/Chart.min.js'></script>
<body>

<canvas id='nopeus' width='400' height='150'></canvas>
<canvas id='paikka' width='400' height='150'></canvas>
<script>
var ctx = document.getElementById('nopeus').getContext('2d');
var ctx2 = document.getElementById('paikka').getContext('2d');
var myChart = new Chart(ctx, {
    type: 'line',
    data: {
		datasets: [{
            label: 'Nopeus',
			fill: false,
            borderColor: ['rgba(0,0,0,1)'],
            borderWidth: 1,
			lineTension: 0.1,
			data:[";
}

function vali(){ //Kaavio.php
	echo "]
        }]
    },
    options: {
		animation: {
			duration: 0,
		},
		scales: {
			xAxes: [{
				type: 'time',
				display: true,
				scaleLabel: {
					display: true,
					labelString: 'Time'
				},
				ticks: {
					major: {
						fontStyle: 'bold',
						fontColor: '#FF0000'
					}
				}
			}],
			yAxes: [{
				display: true,
				scaleLabel: {
					display: true,
					labelString: 'Speed | km/h'
				}
			}]
		}
    }
});
var myChart2 = new Chart(ctx2, {
type: 'line',
data: {
	datasets: [{
		label: 'Paikka',
		fill: false,
		borderColor: ['rgba(0,0,0,1)'],
		borderWidth: 1,
		lineTension: 0.1,
		data:[";
}

function loppuKaavio(){ //Kaavio.php
echo "]
	}]
},
options: {
	animation: {
		duration: 0,
	},
	scales: {
		xAxes: [{
			type: 'time',
			display: true,
			scaleLabel: {
				display: true,
				labelString: 'Time'
			},
			ticks: {
				major: {
					fontStyle: 'bold',
					fontColor: '#FF0000'
				}
			}
		}],
		yAxes: [{
			display: true,
			scaleLabel: {
				display: true,
				labelString: 'Place | km'
			}
		}]
	}
}
});
</script>

</body>
</html> ";
}

function alku($Lon, $Lat){ //Kartta.php
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
	<script src="OpenLayers/OpenLayers.js"></script>
	<script>
		function init() {
			//Luodaan kartta ja määritellään sille oletus sijainti ja oletus zoom.
			map = new OpenLayers.Map("basicMap");
			var mapnik         = new OpenLayers.Layer.OSM();
			var fromProjection = new OpenLayers.Projection("EPSG:4326");   // Transform from WGS 1984
			var toProjection   = new OpenLayers.Projection("EPSG:900913"); // to Spherical Mercator Projection
			var position       = new OpenLayers.LonLat('. $Lon .',' . $Lat . ').transform( fromProjection, toProjection);
			var zoom           = 14; 
			
			//Lisättään kartalle ulkonäkö(?) ja otetaan oletus arvot käyttöön.
			map.addLayer(mapnik);
			map.setCenter(position, zoom );
			
			//Määritellään pisteet ja ajat.
			';
}

function loppu(){ //Kartta.php
	echo"
			var style = new OpenLayers.StyleMap({
				'strokeWidth': 2,
				'strokeColor': '#444444'
			});
			
			// Määritellään vectori taso viivalle.
			var viiva = new OpenLayers.Layer.Vector(\"Route\", {styleMap: style});
			
			// Luodaan viiva.
			var line = new OpenLayers.Feature.Vector(new OpenLayers.Geometry.LineString(points));
			
			// Add new feature to layer named by vector
			viiva.addFeatures([line]);
			
			// Add vector layer to map
			map.addLayers([viiva]);
			
			// new vector graphic layer
			var merkit = new OpenLayers.Layer.Vector(\"Route\");
			
			//Käydään points taulukko läpi lisäten jokaiselle pisteelle oma merkki kartalle millä on aikaleima.
			for(var i = 0; i < points.length; i++){				
				var paikka = points[i];
				
				var feature = new OpenLayers.Feature.Vector(
					paikka,
					{description: \"marker number \" + i} ,
					{
						externalGraphic: 'marker.png',
						graphicHeight: 25,
						graphicWidth: 21,
						graphicXOffset: -12,
						graphicYOffset: -25,
						title: ajat[i]
					}
				);
				merkit.addFeatures(feature);
			}
			// Lisätään merkit kartalle.
			map.addLayer(merkit);
		}
	</script>
</head>
<body onload=\"init();\">
<div id=\"basicMap\"></div>

</body>
</html>
	";
}

function live($Lon, $Lat){ //live.php
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
	<script src="OpenLayers/OpenLayers.js"></script>
	<script>
		var size = new OpenLayers.Size(21,25);
		var offset = new OpenLayers.Pixel(-(size.w/2), -size.h);
		var icon = new OpenLayers.Icon("marker.png", size, offset);
		
		var fromProjection = new OpenLayers.Projection("EPSG:4326");   // Transform from WGS 1984
		var toProjection   = new OpenLayers.Projection("EPSG:900913"); // to Spherical Mercator Projection
		
		var merkki = 0;
		var marks = new OpenLayers.Layer.Markers("Markers");
	
		function init() {
			//Luodaan kartta ja määritellään sille oletus sijainti ja oletus zoom.
			map = new OpenLayers.Map("basicMap");
			var mapnik         = new OpenLayers.Layer.OSM();
			var position       = new OpenLayers.LonLat('. $Lon .',' . $Lat . ').transform( fromProjection, toProjection);
			var zoom           = 15; 
			
			//Lisättään kartalle ulkonäkö(?) ja otetaan oletus arvot käyttöön.
			map.addLayer(mapnik);
			map.setCenter(position, zoom );
			
			merkki = new OpenLayers.Marker(position ,icon);
			
			map.addLayer(marks);
			marks.addMarker(merkki);
			//merkki.icon.imageDiv.title = "Live";
		}
		function paivita(){
			var xmlhttp = new XMLHttpRequest();
			xmlhttp.onreadystatechange = function(){
				if(this.readyState == 4 && this.status  == 200){
					//Koodia millä luodaan uusi marker ja poistetaan vanha.
					var txt = this.responseText;
					var Lon = 0;
					var Lat = 0;
					
					//Koodia mikä erottelee vastauksesta Lat ja Lon arvot
					//this.responseText;
					
					var lonSij = txt.indexOf("Lon");
					
					var Lat = parseFloat(txt.slice(4,lonSij));
					
					var mista = lonSij + 4;
					var mihin = txt.length;
					
					var Lon = parseFloat(txt.slice(mista, mihin));
					
					var merkkiuusi = new OpenLayers.Marker(new OpenLayers.LonLat(Lon, Lat).transform( fromProjection, toProjection),icon.clone());
					marks.removeMarker(merkki);
					marks.addMarker(merkkiuusi);
					merkki = merkkiuusi;
				}
			}
			xmlhttp.open("GET", "uusin.php");
			xmlhttp.send();
		}
		
		window.setInterval(function(){
			paivita();
		}, 1000);
		
	</script>
</head>
<body onload="init();">
<div id="basicMap"></div>

</body>
</html>
';
}
?>
