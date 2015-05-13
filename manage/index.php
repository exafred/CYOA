<?php
	session_start();
	include('../inc/mysqli_connect.php');
?>
<?php
	$booksquery = "SELECT * FROM `book`;";
	$booksresult = mysqli_query($dbc, $booksquery);
	
	$books = array();
	while($b = mysqli_fetch_assoc($booksresult)) {
		$books[] = $b;
	}
	
	#///////
	$pagesquery = "
	SELECT 
		*
	FROM `page`
	ORDER BY `number` ASC;";
	$pagesresult = mysqli_query($dbc, $pagesquery);
	
	$pages = array();
	while($p = mysqli_fetch_assoc($pagesresult)) {
		$pages[$p['number']] = $p;
		$pages[$p['number']]['choices'] = array();
	}
	
	#///////
	$choicesquery = "SELECT * FROM `choice`;";
	$choicesresult = mysqli_query($dbc, $choicesquery);
	
	while($c = mysqli_fetch_assoc($choicesresult)) {
		$pages[$c['page_number']]['choices'][] = $c;
	}
	
	#echo "<PRE>",print_r($pages),"</PRE>";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Manage Your Own Adventure!</title>

<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
<link rel="stylesheet" href="../css/main.css" />
<link rel="stylesheet" href="../css/manage_pages.css" />

<script type="text/javascript" src="../js/jquery.js"></script>
<script type="text/javascript">
$(document).ready(function(e) {
	
	var GD = 250;
	
});
</script>
</head>
<body>
	
</body>
</html>