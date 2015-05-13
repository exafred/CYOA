<?php
	if(isset($_POST)) {
		
		include('mysqli_connect.php');
				
		function getList($post, $conn) {
			switch ($post['type']) {
				// get list of books
				case "book":
					$query = "SELECT
					id as 'book_id',
					title,
					subtitle
					FROM `book`;";
				break;
				case "page":
				// get list of pages within a given book
					if($post['book_id'] != "false") {
						$book_id = mysql_real_escape_string($post['book_id'], $dbc);
						$query = "
						SELECT 
							book_id,
							number as 'page_number',
							title,
							content
						FROM `page`
						WHERE `book_id` = $book_id
						ORDER BY `number` ASC;";
					} else {
						$query = false;
					}
				break;
				case "choice":
				// get list of choices for a given book's given page
					if($post['book_id'] != "false" && $post['page_number'] != "false") {
						$book_id = mysql_real_escape_string($post['book_id'], $dbc);
						$page_number = mysql_real_escape_string($post['page_number'], $dbc);
						$query = "
						SELECT
							*
						FROM `choice`
						WHERE `book_id` = '$book_id'
						AND `page_number` = '$page_number'
						ORDER BY `number` ASC;";
					} else {
						$query = false;
					}
				break;
				default: 
					$query = false;
				break;
			}
			
			if($query != false) {
				$queryresult = mysqli_query($conn, $query);
				$result = array("result"=>array());
				while($r = mysqli_fetch_assoc($queryresult)) {
					$result["result"][] = $r;
				}
				return $result;
			} else {
				return "Failure";
			}
		}
		echo json_encode(getList($_POST, $dbc));
	}
?>