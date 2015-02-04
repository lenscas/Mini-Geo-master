<div class="photoMap">
    <style type="text/css">
      #map-canvas {  margin: 0; padding: 0;border:black 1px solid; position:none;}
    </style>
    <script type="text/javascript"
      src="https://maps.googleapis.com/maps/api/js?key=AIzaSyD25UpFigcyIJf2r9LrfBij8ETtIHSpEms">
    </script>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
	<script>
		
	</script>
	<script type="text/javascript">
function initialize() {
	var myLatlng = new google.maps.LatLng(52.228936,5.321492);
	var mapOptions = {
		zoom: 7,
		center: myLatlng
	}
	var map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);

	var marker = new google.maps.Marker({
		position: myLatlng,
		map: map,
		draggable: true,
		title: 'your guess'
	});
	$(document).ready(function(){
		$("button").click(function(){
			var position=marker.getPosition();
			$.post("<?php echo URL ."game/getLongLat/" . $this->id ; ?>",
				{
					lat: position["k"],
					lon: position["D"]
				},
			function(data, status){
				alert("Data: " + data + "\nStatus: " + status);
			});
		});
	});
}

		google.maps.event.addDomListener(window, 'load', initialize);



      google.maps.event.addDomListener(window, 'load', initialize);
    </script>
	<img src="<?php echo URL . URL_PUBLIC_FOLDER . "/uploads/" . $this->photo ;?>" height="150" width="150" style="float:left;" >
	<button>make guess</button>
	<div id="map-canvas"></div>
		

</div>