<?php
	if($_SERVER['HTTP_HOST'] == 'localhost') {
		// LOCAL
		$database = "henrycod_cyoa";
		$mysqli = new mysqli("localhost", "root", "root", $database);
		$dbc = mysqli_connect("localhost", "root", "root", $database);
	} else {
		// LIVE
		$database = "henrycod_cyoa";
		$mysqli = new mysqli("localhost", "henrycod_cyoa", "-T+)(f2WTO%;", $database);
		$dbc = mysqli_connect("localhost", "henrycod_cyoa", "-T+)(f2WTO%;", $database);
	}
?>