<?php
class GameModel
{
	//used to get a random string out of a list of possibilities
	//needs a numeric array
	private function randomText($stringArray)
	{
		//get highest key
		$max=count($stringArray)-1;
		//select a random key
		$randomKey=rand(0,$max);
		//return the value 
		return $stringArray[$randomKey];
	}
	public function getRandomPicture()
	{
		//I use this instead of ORDER BY rand() because that would place a random number on all entry's even with limit 1 this shouldn't do that 
		//but beware as it is still possible its even possible that it does so multiple times
		//if it is increase the extra chance but this you shouldn't make it to high as it will reduce the randomness 
		
		//get amount of entries in the database 
		//if you know the amount use that instead but right now it could grow as people can still upload
		$sql="select count(*) as counter from photo";
		$query = $this->db->prepare($sql);
        $query->execute();
		$countArray=$query->fetch();
		$count=$countArray->counter;
		$entry=rand(1,$count)-1;
		//try to get a random picture from the database
		$sql = sprintf("SELECT id,`file` FROM photo LIMIT %s,1", $entry);
		$query = $this->db->prepare($sql);
		$query->execute();
		//we now have a random selected picture
		return $query->fetch();
	}
	//if we know the id get it out the database will only read the file and id
	public function getPictureById($id)
	{
		$sql = "SELECT id,file FROM photo WHERE id=:id LIMIT 1";
		$query = $this->db->prepare($sql);
		$query->execute(array(":id"=>$id));
		return $query->fetchAll();
	}
	//if we know the id get it out the database will only read the long/lat and id
	public function getLongLat($id)
	{
		$sql = "SELECT id,`long`,lat FROM photo WHERE id=:id LIMIT 1";
		$query = $this->db->prepare($sql);
		$query->execute(array(":id"=>$id));
		return $query->fetchAll();
	}
}