<?php
	session_start();
	include('../inc/mysqli_connect.php');
?>
<?php

	if(isset($_GET['b']) && is_numeric($_GET['b']) && $_GET['b'] > 0) {
		// we have a book; get book details
		$current_book = $_GET['b'];
		
		#///////
		$book_query = "SELECT * FROM `book` WHERE `id` = ".$current_book.";";
		$book_result = mysqli_query($dbc, $book_query);
		
		// if only a single book is returned
		if(mysqli_num_rows($book_result) == 1) {
			// list book
			$book = mysqli_fetch_assoc($book_result);
			
			// echo out book details
			
			echo 'book details';
			ppa($book);
			makeSingleBook($book);
			
			// do we also have a page from this book?
			if(isset($_GET['p']) && is_numeric($_GET['p']) && $_GET['p'] > 0) {
				// get current page details, too
				$current_page = $_GET['p'];
				
				#///////
				$page_query = "
				SELECT 
					*
				FROM `page`
				WHERE `book_id` = ".$current_book."
				AND `id` = ".$current_page."
				ORDER BY `number` ASC
				;";
				$page_result = mysqli_query($dbc, $page_query);
				
				if(mysqli_num_rows($page_result) == 1) {
					$page = mysqli_fetch_assoc($page_result);
					
					// also get page's existing choices
								
					#///////
					$choicesquery = "SELECT
						*
					FROM `choice`
					WHERE `book_id` = ".$current_book."
					AND `page_number` = ".$current_page."
					ORDER BY `sort_order`
					;";
					$choicesresult = mysqli_query($dbc, $choicesquery);
					
					if(mysqli_num_rows($choicesresult) > 0) {
						$page['choices'] = array();
						while($c = mysqli_fetch_assoc($choicesresult)) {
							$page['choices'][] = $c;
						}
					}
					// echo page details
					echo 'page details, with ('.count($page).') choices';
					ppa($page);
					makeSinglePage($page);
				}
			} else {
				// no set page, get a list of all pages from this book
				#///////
				$pagesquery = "
				SELECT 
					`page`.*,
					COUNT(`choice`.`id`) as num_choices
				FROM `page`
				LEFT JOIN `choice`
				ON `choice`.`page_number` = `page`.`id`
				WHERE `page`.`book_id` = ".$current_book."
				ORDER BY `number` ASC;";
				$pagesresult = mysqli_query($dbc, $pagesquery);
				
				if(mysqli_num_rows($pagesresult) > 0) {
					$pages = array();
					while($p = mysqli_fetch_assoc($pagesresult)) {
						$pages[$p['number']] = $p;
						ok somehow the stuff isn't coming through here for all pages' - do something... Fixy?
					}
					
					// loop through all pages & echo contents
					echo 'page list w/ part-content of '.count($pages).' pages';
					ppa($pages);
					makePagesList($pages);
				}
			}
			echo '<div class="make new page fa fa-plu">New Page</div>';
		} else {
			// no existing book! display message and offer backlink
			echo 'no book; provide backlink to main management page';
		}
	} else {
		// we don't have a book chosen; get a list of all books
		#///////
		$booksquery = "SELECT
			`book`.*,
			COUNT(`page`.`id`) AS num_pages
		FROM `book`
		LEFT JOIN `page`
		ON `page`.`book_id` = `book`.`id`
		;";
		$booksresult = mysqli_query($dbc, $booksquery);
		
		if(mysqli_num_rows($booksresult) > 0) {
			$books = array();
			while($b = mysqli_fetch_assoc($booksresult)) {
				$books[] = $b;
			}
			
			// list all books
			echo 'books list!';
			ppa($books);
			makeBooksList($books);
		}
		echo '<div class="make new book fa fa-plu">New Book</div>';
	}
	function ppa($a) {
		echo "<PRE>",print_r($a),"</PRE>";
	}
	function makeBooksList($list) {
		if(is_array($list) && count($list) > 0) {
			echo '<div class="booklist">';
			foreach($list as $item) {
				echo
				'<div class="item">
					<div class="title">'.$item['title'].'</div>
					<div class="subtitle">'.$item['subtitle'].'</div>
					<div class="number_of_pages">Pages: <span class="number">'.$item['num_pages'].'</span></div>
					<div class="acknowledgements">'.$item['acknowledgements'].'</div>
					<div class="notes">'.$item['notes'].'</div>
					<a class="outref" href="?b='.$item['id'].'"></a>
				</div>';
			}
			echo '</div>';
		}
	}
	function makeSingleBook($item) {
		echo
		'<div class="single book">
			<div class="title">'.$item['title'].'</div>
			<div class="subtitle">'.$item['subtitle'].'</div>
			<div class="number_of_pages">Pages: <span class="number">'.$item['num_pages'].'</span></div>
			<div class="acknowledgements">'.$item['acknowledgements'].'</div>
			<div class="notes">'.$item['notes'].'</div>
		</div>';
	}
	function makePagesList($list) {
		if(is_array($list) && count($list) > 0) {
			echo '<div class="pagelist">';
			foreach($list as $item) {
				$visible_class = ($item['publish'] == 1)? ' yes': '';
				echo
				'<div class="item">
					<div class="page_number">Page #<span class="number">'.$item['number'].'</span></div>
					<div class="title">'.$item['title'].'</div>
					<div class="content">'.substr($item['content'], 0, 150).'&hellip;</div>
					<div class="date first_added">'.$item['added'].'</div>
					<div class="date last_modified">'.$item['last_modified'].'</div>
					<div class="currently visible'.$visible_class.'"></div>
					<a class="outref" href="?b='.$item['book_id'].'&p='.$item['id'].'"></a>
				</div>';
			}
			echo '</div>';
		}
	}
	function makeSinglePage($item) {
		$visible_class = ($item['publish'] == 1)? ' yes': '';
		echo '<div class="single page" data-id="'.$item['id'].'">
			<div class="page_number">Page #<span class="number" contenteditable="true">'.$item['number'].'</span></div>
			<div class="title">'.$item['title'].'</div>
			<div class="content" contenteditable="true">'.$item['content'].'</div>
			<div class="currently visible'.$visible_class.'"></div>'. makeChoicesList($item['choices']).'</div>';
	}
	function makeChoicesList($list) {
		if(is_array($list) && count($list) > 0) {
			echo '<div class="page choices">';
			foreach($list as $choice) {
				echo '<div class="choice" data-id="'.$choice['id'].'">
					<div class="relation">
						<div class="current target page" data-target="'.$choice['target_page'].'">Target Page: '.$choice['target_page'].'</div>
						<span class="move_choice">Assign Choice to Different Page</span>
						<span class="move_target">Change Target Page</span>
					</div>
					<div class="choice_text" contenteditable="true">'.$choice['text'].'</div>
				</div>';
			}
			echo '</div>';
		}
		echo '<div class="make new choice fa fa-plus"> New Choice</div>';
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Manage Your Own Adventure!</title>

<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" />
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