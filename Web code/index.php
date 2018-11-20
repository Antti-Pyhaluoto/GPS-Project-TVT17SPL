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
			if(id != 0){
				document.getElementById("ikkuna").src = "indexOL2.php?ID=" + id;
			}
			else{
				document.getElementById("ikkuna").src = "indexOL2.php;
			}
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
	
	<div class="row align-items-center justify-content-center">
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
					echo "<option value=\"0\">-</option>";
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
		</div>
		
		<div class="col-md-2">
			<button type="button" onclick="vaihdaIkkuna()">Valitse</button>
		</div>
		
		<div class="col-md-2">
			<button type="button">Live (joku päivä)</button>
		</div>
		
		<div class="col-md-3"></div>
	</div>
	
    <div class="row">
	  <div class="col-xs-6">
        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur euismod ornare mi, vel ultrices dolor facilisis a. Sed est lorem, rhoncus et bibendum eget, iaculis id libero. Sed a porta purus. Duis fermentum erat sit amet arcu congue, sit amet porta odio gravida. Proin porta enim felis, vel gravida justo efficitur sit amet. Cras mollis porttitor augue, non mattis odio dictum non. Vivamus tempor vitae mi ut rhoncus. Praesent lacinia gravida gravida. Fusce porttitor velit at est porta facilisis. Etiam aliquet congue diam, vel dapibus massa mattis vitae. Aliquam a molestie urna. Mauris placerat tincidunt quam ac vestibulum. Curabitur quis.
		Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur euismod ornare mi, vel ultrices dolor facilisis a. Sed est lorem, rhoncus et bibendum eget, iaculis id libero. Sed a porta purus. Duis fermentum erat sit amet arcu congue, sit amet porta odio gravida. Proin porta enim felis, vel gravida justo efficitur sit amet. Cras mollis porttitor augue, non mattis odio dictum non. Vivamus tempor vitae mi ut rhoncus. Praesent lacinia gravida gravida. Fusce porttitor velit at est porta facilisis. Etiam aliquet congue diam, vel dapibus massa mattis vitae. Aliquam a molestie urna. Mauris placerat tincidunt quam ac vestibulum. Curabitur quis.
		Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur euismod ornare mi, vel ultrices dolor facilisis a. Sed est lorem, rhoncus et bibendum eget, iaculis id libero. Sed a porta purus. Duis fermentum erat sit amet arcu congue, sit amet porta odio gravida. Proin porta enim felis, vel gravida justo efficitur sit amet. Cras mollis porttitor augue, non mattis odio dictum non. Vivamus tempor vitae mi ut rhoncus. Praesent lacinia gravida gravida. Fusce porttitor velit at est porta facilisis. Etiam aliquet congue diam, vel dapibus massa mattis vitae. Aliquam a molestie urna. Mauris placerat tincidunt quam ac vestibulum. Curabitur quis.
		Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur euismod ornare mi, vel ultrices dolor facilisis a. Sed est lorem, rhoncus et bibendum eget, iaculis id libero. Sed a porta purus. Duis fermentum erat sit amet arcu congue, sit amet porta odio gravida. Proin porta enim felis, vel gravida justo efficitur sit amet. Cras mollis porttitor augue, non mattis odio dictum non. Vivamus tempor vitae mi ut rhoncus. Praesent lacinia gravida gravida. Fusce porttitor velit at est porta facilisis. Etiam aliquet congue diam, vel dapibus massa mattis vitae. Aliquam a molestie urna. Mauris placerat tincidunt quam ac vestibulum. Curabitur quis.
		</p>
      </div>
	  <div class="col-xs-6">
		<iframe src="indexOL2.php" style="height:500px;width:100%;" id="ikkuna"></iframe>
      </div>
    </div>
  </div>
  </body>
</html>