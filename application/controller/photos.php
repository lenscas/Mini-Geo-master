<?php
class photos extends controller
{
	public function index()
	{
		//show all the photos
		if (!isset($model)) {
			$model=$this->loadModel("photo");
		}
		$this->view->pictures = $model->getAllPictures();
		$this->view->render('photo/index' );
	}
	public function upload()
	{
		Auth::handleLogin();
		if(!isset($_POST["submit"])){
			$this->view->render('photo/upload' );
		}
		else{
			$model=$this->loadModel("photo");
			$succes=$model->upload();
			if($succes){
				$succes=$model->fileInDatabase(1,$succes);
				$this->view->pictures = $model->getAllPictures();
				$this->view->render('photo/index' );
			}
			else{
				$this->view->render('photo/upload' );
			}
		
		}
	}
}