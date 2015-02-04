<?php

class PhotoModel
{
	//used to get the long/lat out of the photo
	private function gps($coordinate, $hemisphere) {
		for ($i = 0; $i < 3; $i++) {
			$part = explode('/', $coordinate[$i]);
			if (count($part) == 1) {
				
			} else if (count($part) == 2) {
				$coordinate[$i] = floatval($part[0])/floatval($part[1]);
			} else {
				$coordinate[$i] = 0;
			}
		}
		list($degrees, $minutes, $seconds) = $coordinate;
		$sign = ($hemisphere == 'W' || $hemisphere == 'S') ? -1 : 1;
		return $sign * ($degrees + $minutes/60 + $seconds/3600);
	}
	//here is all the upload magic (yes I called it magic no I don't care)
	public function upload()
	{
		$target_dir =UPLOAD; 
		$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
		$uploadOk = 1;
		$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
		// Check if image file is a actual image or fake image
		if(isset($_POST["submit"])) {
			$check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
			if($check !== false) {
				$uploadOk = 1;
			} else {
				$_SESSION['feedback_negative'][] = FEEDBACK_FILE_NOT_IMAGE;
				return false;
			}
		}
		

		// Check if file already exists
		if (file_exists($target_file)) {
			$_SESSION['feedback_negative'][] = FEEDBACK_DUPLICATE_FILE;
			return false;
		}
		// Check file size
		/*if ($_FILES["fileToUpload"]["size"] > 500000) {
			$error= "too big.";
			$uploadOk = 0;
		}*/
		// Allow certain file formats
		if($imageFileType != "jpg" && $imageFileType != "jpeg"){
			$_SESSION['feedback_negative'][] = FEEDBACK_FALSE_FORMAT;
			return false;
		}

		if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
			return $_FILES["fileToUpload"]["name"];
		} else {
			$_SESSION['feedback_negative'][] = FEEDBACK_UNFORSEEN_ERROR_UPLOAD;
			return false;
		}
				$data=exif_read_data($_FILES["fileToUpload"]["tmp_name"], 0, true);
		if($data != null){
			if(!isset($data['GPS'])){
				$_SESSION['feedback_negative'][] = FEEDBACK_NO_GPS_DATA;
				return false;
			}
		}else{
			$_SESSION['feedback_negative'][] = FEEDBACK_NO_GPS_DATA;
			return false;
		}
	}
	//code to put the file in the database
	public function fileInDatabase($user,$fileName)
	{
		Session::init();
		//read the xif data
		$exif = exif_read_data(UPLOAD.$fileName);
		//get both the latitude and longitude 
		$latitude = $this->gps($exif["GPSLatitude"], $exif['GPSLatitudeRef']);
		$longitude = $this->gps($exif["GPSLongitude"], $exif['GPSLongitudeRef']);
		
		//insert the photo data in the database
		$sql="INSERT INTO `photo`(`file`, `user`, `long`, `lat`) VALUES (:file,:user,:long,:lat)";
		$query = $this->db->prepare($sql);
		$parameters=array(":file" =>$fileName,":user"=>Session::get("user_id"),":long"=>$longitude,":lat"=>$latitude);
		$query->execute($parameters);
		return array("state"=>true,"message"=>"no errors");
	}
	public function getAllPictures()
	{
		Session::init();
        $sql = "SELECT id,file FROM photo";
        $query = $this->db->prepare($sql);
        $query->execute();
        return $query->fetchAll();		
	
	}
}
?> 