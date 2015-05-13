<?php
	session_start();
	include('inc/mysqli_connect.php');
	
	// $book will eventually be generated automatically. For testing, it is 1
	$book = 1;
	
	// set variables for book size properties
	$page_ratio =  0.70;
	$page_orientation = 'p';
?>
<?php
	$pagesquery = "
	SELECT 
		`number`,
		`title`,
		`content`
	FROM `page`
	WHERE `publish` = 1
	AND `book_id` = '$book'
	ORDER BY `number` ASC;";
	$pagesresult = mysqli_query($dbc, $pagesquery);
	
	$pages = array();
	while($p = mysqli_fetch_assoc($pagesresult)) {
		$pages[$p['number']] = $p;
		$pages[$p['number']]['choices'] = array();
	}
	
	$choicesquery = "SELECT * FROM `choice` WHERE `book_id` = $book;";
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
	

	// create globovar for pages' orientation: p | l
	var orientation = "<?=$page_orientation?>";
	
	// create globovar for the "margins" of the book, relative to its parent
	var percentage_offset = 3;
	
	
	// create globovar for the ratio to use for page (see above for ref)
	// 0.7067 : original (drafft4) dimensions
	var page_ratio = <?=$page_ratio?>;
	
	// resize on initial load
	resizeBook(CYOA, orientation, percentage_offset, true, page_ratio);
	
	// resize on window resize
	$(window).resize(function(e) {
		resizeBook(CYOA, orientation, percentage_offset, false, page_ratio);
	});

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

/*################
## RESIZE PAGES ##
################*/
function resizeBook(pajs_book, orientation, percentage_offset, is_initial_load, ratio) {
	var w,h,mt;
	
	// currently working on $(window), this could later be changed to work on pajs_book's parent
	
	// calculate height and width based on orientation
	// the "shorter" side is near-exactly 70.70% shorter than the longer side
	// e.g. for portrait, width is 70.7% of height
	if(orientation == "p") {
		h = pajs_book.parent().height();
		w = h * ratio;
	} else {
		h = pajs_book.parent().height();
		w = h / ratio;
	}
	
	// modify with percentage offset
	w	= Math.round(w - (w * (percentage_offset / 100)));
	mt	= Math.round(h * (percentage_offset / 100));
	h	= Math.round(h - (2 * mt));
	
	//console.log("book dims & top margin value:", h,w,mt);
	pajs_book.css({
		"width":		w+"px",
		"height":		h+"px",
		"top":			mt+"px",
	});
}

</script>
</head>
<body>
</body>
</html>