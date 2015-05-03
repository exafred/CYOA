<?php
	session_start();
	include('inc/mysqli_connect.php');
?>
<?php
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

<link rel="stylesheet" href="../css/global.css" />
<link rel="stylesheet" href="../css/manage_pages.css" />

<script type="text/javascript" src="../js/jquery.js"></script>
<script type="text/javascript">var pages = <?=json_encode($pages);?>;</script>
<script type="text/javascript">
$(document).ready(function(e) {
	
});
</script>
</head>
<body>
	OKSO
	<br />make a list of AJAX updateable pages and choices
	<br />----with auto-renumbering for pages
	<br />----HTML editing capabilities for page content
	<br />----AJAX'd link info for choices (i.e. popup for intended target page content)
	<br />
	<br />make add new page ability
	<br />----again, with renumbering (can this be done in MySQL?)
	<br />
	<br /><em>don't forget that choices need to be updated when page numbers are, btw</em>
	<br />
	<br />... + ?
	<br />
	<br /><em><small>I'd also like the ability to create a link diagram for all the choices &amp; pages</small></em>
	<?php
		foreach($pages as $page) {
			echo
			'<div class="page" data-id="">
				<div class="element">
					<div class="label">number</div>
				</div>
		}
	?>
	
</body>
</html>