<?php
	session_start();
	include('inc/mysqli_connect.php');
?>
<?php
	$pagesquery = "
	SELECT 
		`number`,
		`title`,
		`content`
	FROM `page`
	WHERE `publish` = 1
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
<title>Choose Your Own Adventure!</title>

<link rel="stylesheet" href="css/main.css" />
<link rel="stylesheet" href="css/pages.css" />

<script type="text/javascript" src="js/jquery.js"></script>
<script type="text/javascript">var pages = <?=json_encode($pages);?>;</script>
<script type="text/javascript">
$(document).ready(function(e) {
	// create the book
	
	if($('div.cyoa').length < 1) {
		var cyoa = $('<div/>', {
			"class":"cyoa"
		});
		var structure = ''+
		'<div class="page">'+
			'<p class="pageNo"></p>'+
			'<p class="pageTitle"></p>'+
			'<div class="pageContent"></div>'+
			'<div class="pageChoices"></div>'+
		'</div>'+
		'';
		cyoa.html(structure);
		$('body').append(cyoa);
	}
	
	// declare globally accessible CYOA
	var CYOA = $('div.cyoa');
	
	// create first page
	createPage(CYOA, pages, 1);
	
	// handle choice selection
	$(document).on('click', 'div.cyoa div.page div.pageChoices p.choice', function(e) {
		e.stopPropagation();
		createPage(CYOA, pages, $(this).attr('data-target'));
	});
});

//	create a given page and replace currently displayed page's content
//	Args
//		instance	CYOA instance
//		list		multidimensional array
//		target		integer
function createPage(instance, list, target) {
	
	var newChoices = '';
	
	if(target in list) {
		// set target page object
		var page = list[target];
		
		// get choices from target page
		var choices = page.choices;
		
		// create new choices for each of the entries in the choices array
		$.each(choices, function (i, v) {
			newChoices += ''+
			'<p class="choice" data-target="'+v.target_page+'">' +
				'<span class="targetPage">Go to page ' + v.target_page + '</span>' +
				'<span class="choiceDescription">' + v.text + '</span>' +
			'</p>';
		});
		
		// replace content of existing .page elements with appropriate values, fadeOut-then-In the page
		$("html, body").animate({ scrollTop: 0 }, 400);
		instance.find('.page *').fadeOut(250, function() {
			instance.find('.pageNo').html('<span class="pageLabel">Page</span>' + page.number);
			instance.find('.pageTitle').html(page.title);
			instance.find('.pageContent').html(page.content);
			instance.find('.pageChoices').html(newChoices);			
			instance.find('.page *').fadeIn(250);
		});
	}
}
</script>
</head>
<body>
</body>
</html>