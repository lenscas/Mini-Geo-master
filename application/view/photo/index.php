 <div class="content">
	 <?php foreach ($this->pictures as $picture) {  ?>
	 
	 <a href="<?php echo URL."/game/play/".$picture->id ; ?>"><img src="<?php echo URL . URL_PUBLIC_FOLDER . "/uploads/" . $picture->file ;?>" height="150" width="150"></a>
	<?php } ?>
</div>