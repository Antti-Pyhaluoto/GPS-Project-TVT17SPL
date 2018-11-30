<!doctype html>
<html lang="en">
  <head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
	
	<script>
		function vaihdaIkkuna(){
			var pudotus = document.getElementById("pudotus");
			var id = pudotus.options[pudotus.selectedIndex].value;
			
			document.getElementById("ikkuna").src = "kartta.php?ID=" + id;
			document.getElementById("ikkunaGraph").src = "kaavio.php?ID=" + id;
		}
		function liveIkkuna(){
			document.getElementById("ikkuna").src = "live.php";
			document.getElementById("ikkunaGraph").src = "kaavio.php";
		}
		function hallintaIkkuna(){
			document.getElementById("ikkuna").src = "live.php";
			document.getElementById("ikkunaGraph").src = "hallinta.html";
		}
	</script>
    
	<title>GPS Project</title>
	
  </head>
  <body>
  <div class="container-fluid">
    <div class="row">
		<div class="col-xs-12">
			<h1 class="text-center">GPS Project</h1>
		</div>
	</div>
	
	<div class="row">
		<div class="col-md-3"></div>
		
		<div class="col-md-2">
			<select id="pudotus">
			<?php
				require "funktiot.php";
				try{
					$con = openDatabase();
					$kysely = $con->prepare("CALL Reitit();");
					$kysely->execute();
					$kaikki = $kysely->fetchAll();
					$kysely->closeCursor();
					$con = null;
					echo "<option value=\"0\">Info</option>";
					foreach($kaikki as $yksi){
						echo "<option value = \"" . $yksi['ID'] . "\">Reitti " . $yksi['ID'] . "</option>";
					}
					
				}catch(PDOException $e){
					//Muuta kirjoittamaan lokiin.
					echo "<option>Tapahtui vihre.</option>";
					echo "<option>Virhe:" . $e->getMessage() . "</option>";
				}
				
			?>
			</select>
			<button type="button" onclick="vaihdaIkkuna();">Valitse</button>
		</div>
		
		<div class="col-md-2">
			<button type="button" onclick="liveIkkuna()">Live</button>
		</div>
		
		<div class="col-md-2">
			<button type="button" onclick="hallintaIkkuna()">Hallinta</button>
		</div>
		
		<div class="col-md-3"></div>
	</div>
	
    <div class="row">
	  <div class="col-xs-6">
	  
		<iframe src="kaavio.php" style="height:600px;width:100%;" id="ikkunaGraph"></iframe>
		
      </div>
	  <div class="col-xs-6">
		<iframe src="kartta.php" style="height:600px;width:100%;" id="ikkuna"></iframe>
      </div>
    </div>
  </div>
  </body>
</html>
