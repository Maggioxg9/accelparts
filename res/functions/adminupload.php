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
		$uploadOk = 1;
		if($_POST['categoryname']=='Form'){
			$target_dir = "/var/www/html/accelparts/res/uploads/forms/";
			$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
			$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
			if(isset($_POST["submit"])) {
				if ($_FILES["fileToUpload"]["size"] > 4194304) {
					$uploadOk = 0;
				}
				if($imageFileType !="xlsm"){
					$uploadOk = 0;
				}
				if ($uploadOk == 0) {
					$_SESSION['uploadsuccess']=false;
					header("Location: ../../adminupload.html");
					exit();
					// if everything is ok, try to upload file
				} else {
					if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
						$_SESSION['uploadsuccess']=true;
						header("Location: ../../adminupload.html");
						exit();
					} else {
						$_SESSION['uploadsuccess']=false;
						header("Location: ../../adminupload.html");
						exit();
					}
				}
			}
		}else{
			$target_dir = "/var/www/html/accelparts/res/uploads/categories/" . $_POST['categoryname'] . "/";
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
			if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
				$uploadOk = 0;
			}
			if ($uploadOk == 0) {
				$_SESSION['uploadsuccess']=false;
				header("Location: ../../adminupload.html");
				exit();
				// if everything is ok, try to upload file
			} else {
				if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
					$_SESSION['uploadsuccess']=true;
					header("Location: ../../adminupload.html");
					exit();
				} else {
					$_SESSION['uploadsuccess']=false;
					header("Location: ../../adminupload.html");
					exit();
				}
			}
		}
	}
?>