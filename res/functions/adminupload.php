<?php
	session_start();
	if(isset($_SESSION['adminmode']) && $_SESSION['adminmode']==true){
		//user logged in proceed
	}else{
		//not logged in, redirect to login
		header("Location: login.html");
		exit();
	}
	if(count($_POST) >0){
	
				$servername = "localhost";
				$username = "root";
				$password = "accelmkgt123";
				$dbname = "accelparts";
				
				try{
					$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
					$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
					
					$getcategories = $conn->prepare("select * from categories");
					$getcategories->execute();
					$allcategories = $getcategories->fetchAll(PDO::FETCH_ASSOC);
					$_SESSION['categoryarray']=json_encode($allcategories);
					$conn = null;
				}catch(PDOException $e){
					//print error details to screen
					echo $result . "<br>" . $e->getMessage();

					//close database connection
					$conn = null;
				}
		$uploadOk = 1;
		$target_dir = "res/uploads/categories/Bally/";
		$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
		$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
		if(isset($_POST["submit"])) {
			$check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
			if($check !== false) {
				$uploadOk = 1;
			} else {
			$uploadOk = 0;
			}
		}
		if (file_exists($target_file)) {
			$uploadOk = 0;
		}
		if ($_FILES["fileToUpload"]["size"] > 4194304) {
			$uploadOk = 0;
		}
		if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
			$uploadOk = 0;
		}
		if ($uploadOk == 0) {
			$_SESSION['uploadsuccess']=false;
			// if everything is ok, try to upload file
		} else {
			if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
				echo "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.";
			} else {
				echo json_encode($_FILES);
			}
		}
	}
?>