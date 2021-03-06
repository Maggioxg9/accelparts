<?php
	session_start();
	if(isset($_SESSION['adminmode']) && $_SESSION['adminmode']==true){
		//user logged in proceed
	}else{
		//not logged in, redirect to login
		header("Location: ../../login.html");
		exit();
	}
	if(count($_POST) >0){
		$uploadOk = 1;
		$_SESSION['selected']=htmlspecialchars($_POST['categoryname']);
		
		if($_POST['categoryname']=='Form'){ //uploading part request form
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
				}else {
					//change filename to constant for URL
					$target_file = $target_dir . "Part-Request-Form.xlsm";
					//upload magic
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
		}else{ //uploading part image and descriptions
			$target_dir = "/var/www/html/accelparts/res/uploads/categories/" . $_POST['categoryname'] . "/";
			$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
			$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
			$check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
			if($check == false) {
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
					//upload succeeded so add it to the database
					$servername = "localhost";
					$username = "root";
					$password = "accelmkgt123";
					$dbname = "accelparts";
					$description = htmlspecialchars($_POST['description']);
					$accelnumber = htmlspecialchars($_POST['accelnumber']);
					$categoryname = htmlspecialchars($_POST['categoryname']);
					$imgpath = "res/uploads/categories/" . $categoryname . "/" . basename($_FILES["fileToUpload"]["name"]);
					$thumb = "/var/www/html/accelparts/res/uploads/categories/" . $categoryname . "/thumb_" . basename($_FILES["fileToUpload"]["name"]);
					$thumbpath = "res/uploads/categories/" . $categoryname . "/thumb_" . basename($_FILES["fileToUpload"]["name"]);
					$thumbadmin = "/var/www/html/accelparts/res/uploads/categories/" . $categoryname . "/thumbadmin_" . basename($_FILES["fileToUpload"]["name"]);
					$thumbadminpath = "res/uploads/categories/" . $categoryname . "/thumbadmin_" . basename($_FILES["fileToUpload"]["name"]);
					chmod($target_file, 0777);
					
					$img = new Imagick($target_file);
					$imgadmin = clone $img;
					
					$img->resizeImage(285,285,Imagick::FILTER_LANCZOS,1,TRUE);
					$img->stripImage(); 
					$img->writeImage($thumb);
					chmod($thumb, 0777);
					
					$imgadmin->resizeImage(100,100,Imagick::FILTER_LANCZOS,1,TRUE);					
					$imgadmin->stripImage(); 
					$imgadmin->writeImage($thumbadmin);
					chmod($thumbadmin, 0777);
					
					if($imageFileType == "jpg"){
						shell_exec("jpegoptim --max=45 --strip-all --all-progressive ".$target_file);
					}
					if($imageFileType == "png"){
						shell_exec("optipng -o2 -strip all".$target_file);
					}
					
					try{
						$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
						$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
						
						$getcategory = $conn->prepare("select categoryid from categories where categoryname= :categoryname");
						
						$getcategory ->execute(array(":categoryname" => "$categoryname"));
						$result = $getcategory->fetch(PDO::FETCH_ASSOC);
						$categoryidresult = $result['categoryid'];
						
						$insertimages = $conn->prepare("insert into parts (description, accelnumber,categoryid, imgpath, imgthumbpath, imgadmin) values (:description,:accelnumber, :categoryid, :imgpath, :imgthumbpath, :imgadmin)");
						$insertimages->execute(array(":description" => "$description", ":accelnumber" => "$accelnumber", ":categoryid" => "$categoryidresult", ":imgpath" => "$imgpath", ":imgthumbpath" => "$thumbpath", ":imgadmin" => "$thumbadminpath"));
						$conn = null;
					}catch(PDOException $e){
						//print error details to screen
						echo $result . "<br>" . $e->getMessage();
						//close database connection
						$conn = null;
					}
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
	}else{ // request didnt come from post, redirect to index
		header("Location: ../../index.html");
		exit();
	}
?>