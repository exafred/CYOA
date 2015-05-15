<?php
	if(isset($_POST)) {
		
		include('mysqli_connect.php');
		
		function getInfo($data, $conn) {
			
		}
		echo json_encode(getInfo($_POST, $dbc));
	}
?>