<?php
	session_start();
	$servername = "localhost";
	$username = "root";
	$password = "accelmkgt123";
	$dbname = "accelparts";
	
	try{
		$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
		$getimages = $conn->prepare("select * from parts")
		$getimages->execute();
		$allimages = = $getimages->fetchAll(PDO::FETCH_ASSOC);
		$_SESSION['imagearray']=json_encode($allimages);
		
		$conn = null;
	}catch(PDOException $e){
		//print error details to screen
		echo $result . "<br>" . $e->getMessage();

		//close database connection
		$conn = null;
	}
	
?>
<!DOCTYPE html>
<html><head>
	<meta content='width=device-width, height=device-height, initial-scale=1' name='viewport'>
	<link rel="stylesheet" type="text/css" media="screen and (max-width: 879px)" href="mobilepage.css">
	<link rel="stylesheet" type="text/css" media="screen and (min-width: 880px)" href="desktoppage.css">
	<title>Accel Parts Department</title>
	
	<script>
		function populateImages(){
			var imagedata=<?php echo $_SESSION['imagearray'];?>;
			var imageTable = document.getElementById("gallerytable");
			if(imageTable){
				for(var i=0; i<imageTable.length; i++){
					var row = imageTable.insertRow(i);
					var cell = row.insertCell(0);
					var celldiv = document.createElement("div");
					celldiv.setAttribute('class', "celldiv");
					
					var imgcell = document.createElement("div");
					imgcell.setAttribute('class', "imgcell");
					imgcell.setAttribute('src', imageTable[i].imagepath)
					celldiv.appendChild(imgcell);
					
					var descriptioncell = document.createElement("div");
					descriptioncell.setAttribute('class', "descriptioncell");
					descriptioncell.textContent=imageTable[i].description;
					celldiv.appendChild(descriptioncell);
					
					var accelnumbercell = document.createElement("div");
					accelnumbercell.setAttribute('class', "accelnumbercell");
					accelnumbercell.textContent=imageTable[i].accelnumber;
					celldiv.appendChild(accelnumbercell);
					
					cell.appendChild(celldiv);	
				}
			}
		}
	</script>
</head>
<body>
	<div id="container">
			<div id="nav">
				<div id="navwrap">
					<div id="backwrap">
						<a href="index.html"><span class="menu"><< Home</span></a>
					</div>
					<div id="imgwrap">
						<a href="index.html"><img class="imglogo" src="res/htmlcontent/partsdept.png"></a>
					</div>
					<div id="imgwraphide">
							<a href="login.html"><span class="menu">Admin Login</span></a>
					</div>
				</div>
			</div>
		<div id="content">
			<div id="pagetitle">Bally
			</div>
			<div id="gallerywrap">
				<table id="gallerytable">	
				</table>
				<script>
					populateImages();
				</script>
			</div>
		</div>
	</div>
</body>
</html>