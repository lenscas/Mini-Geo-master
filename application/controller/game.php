<?php
//to much comments don't exist (hopefully)
class game extends controller
{
	function __construct()
    {
        parent::__construct();
        // this controller should only be visible/usable by logged in users, so we put login-check here
        Auth::handleLogin();
    }
	
	private function distance($lat1, $lon1, $lat2, $lon2, $unit)
	{
		$theta = $lon1 - $lon2;
		$dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
		$dist = acos($dist);
		$dist = rad2deg($dist);
		$miles = $dist * 60 * 1.1515;
		$unit = strtoupper($unit);

		if ($unit == "K") {
			return ($miles * 1.609344);
		} else if ($unit == "N") {
			return ($miles * 0.8684);
		} else {
			return $miles;
		 }
	}
	//function to "safely" load the photo data use file to get file name and longLat for the latitude and longitude
	//returns array wait state=did it result in a photo (boolean)
	//message=error (string)
	//data = the data that you asked for(if state==true then array else it is null)
	private function getUsablePhoto($input,$what)
	{
		//get the number out of user input as when the controller gets it it is a string >_>
		//prevent possible crash from people entering stuff like game/play/crash
		// note game/play/crash1 becomes 0 and game/play/1crash becomes 1 
		$num = (int)$input;
		//check if the input could be used (it needs to be higher then 0 else it either didn't contain an int or something else unusable)
		if ($num>0) {
			//load the model
			$model = $this->loadModel("game");
			//use the desired function (I know I could loaded all but I like to have thing separate)
			if($what==="file") {
				//load file name and id code here
				$photo=$model->getPictureById($num);
			} elseif ($what==="longLat") {
				//load latitude and longitude code here
				$photo=$model->getLongLat($num);
			} else {
				//scream code here (YOU USED IT WRONG)
				$error="you used the function wrong use file or longLat instead";
				//yes that is an echo in a controller no I am not going to remove it if it screams at you than you used it wrong
				echo "you used the function wrong use file or longLat instead";
			}
				
			//check if a photo got returned
			if (isset($photo[0])) {
				//return state and result
				//well it looks like the input was usable what a surprise(I mean it it surprises me)
				return array("state"=>true,"message"=>"no errors","data"=>$photo);
			} else {
				//there was no photo with that id(deleted/broken link/stupid user)
				$error="no photo";
			}
		} else {
			//there was no usable int in the input(broken link/stupid user)
			$error="no int";
		}
		//oops something went wrong
		return(array("state"=>false,"message"=>$error,"data"=>null));
	}
	
	public function index()
	{
		$this->view->render('game/index');
	}
	public function play($id=null)
	{
		$valid=true;
		//check if a value has been given if not assume user wanted a random game
		if($id===null) {
			$model= $this->loadModel("game");
			$photo=$model->getRandomPicture();
			$this->view->photo=$photo->file;
			$this->view->id=$photo->id;
		} else {
			//get the photo details safely
			$results=$this->getUsablePhoto($id,"file");
			//check if there was a result
			if ($results["state"]) {
				//game code
				$this->view->photo=$results["data"][0]->file;
				$this->view->id=$results["data"][0]->id;
			} else {
				//crash code
				$valid=false;
			}
		}	
		//to make sure that it only renders if its safe(prevent errors because an variable is not set)
		if($valid) {
			$this->view->render('game/game');
		}
	}
	
	public function getLongLat($id=null)
	{
		//make sure everything will be loaded properly
		//user input is unusable /malicious unless proven otherwise
		$valid=false;
		
		if($id!==null) {
			$results=$this->getUsablePhoto($id,"longLat");
			if ($results["state"]) {
				//it is usable (I still don't want to trust it)
				$valid=true;
			} else {
				//error for false id (see I told you)
				$error=$results["message"];
			}
		} else {
			//no id error(he wanted a crash(or I had a faulty link))
			$error="no id";
		}
		
		//check if there were errors if there where send them else run code to send long and lat
		if ($valid) {
			//code to send long lat
			if(isset($_POST["lat"]) and isset($_POST["lon"])){
				$diffrence=$this->distance($results["data"][0]->lat, $results["data"][0]->long, $_POST["lat"], $_POST["lon"], "k");
				$points=500-$diffrence;
				echo $points;
			}
		} else {
			//code to send error
			echo $error;
		}	
	}
}
	