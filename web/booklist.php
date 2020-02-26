<?php
	ob_start();
	session_start();
	require_once("dbcontroller.php");
	$db_handle = new DBController();
	error_reporting(E_ERROR | E_PARSE);
?>

<html>
	<head>
		<title>Yaraku Book Recommendation System</title>

		<style>
			tr:hover {
  				background-color: lightyellow;
			}

			td:hover {
  				background-color: lightblue;
			}

			div {
				width: 1200px;
				border: 1px solid;
				padding: 10px;
				box-shadow: 5px 10px;
			}

			body {
				font-family: "Arial";
			}

			table {
			 	border-collapse: collapse;
			 	margin-top: 10px;
			 	margin-bottom: 10px;
			}

			th, td {
				padding: 5px;
			}

			table, td, th {
				border: 1px solid black;
			}
		</style>
	</head>
	<body>
		<h1>Yaraku Book Recommendation System</h1>
		
		<div>
			<h3>Add new book</h3>
			<form action="booklist.php" method="post">
				<input type="text" id="addBookField" name="addBookField" placeholder="Book title here">
				<button type="submit" name="addToList">Add Title</button>
			</form>
			<?php
				function getBetween($string, $start = "", $end = ""){
					if (strpos($string, $start)) { // required if $start not exist in $string
						$startCharCount = strpos($string, $start) + strlen($start);
						$firstSubStr = substr($string, $startCharCount, strlen($string));
						$endCharCount = strpos($firstSubStr, $end);
						if ($endCharCount == 0) {
							$endCharCount = strlen($firstSubStr);
						}
						return substr($firstSubStr, 0, $endCharCount);
					} else {
						return '';
					}
				}

				require_once("dbcontroller.php");
				function addBook($bookfield) {
					if($bookfield != '') {
						global $db_handle;
						$entered_title = $bookfield;
						$response = file_get_contents('https://yaraku-book.herokuapp.com/recommend?title='.urlencode($entered_title).'&number=0');
						$matched_title = getBetween($response, "'", "'");
						$matched_author = getBetween($response, "', '", "']]");

						$groups = file_get_contents('https://yaraku-book.herokuapp.com/group?title='.urlencode($matched_title));
						#echo $groups;
						$groups = str_replace("'", "", $groups);
						$groups = str_replace(array('[', ']'), "", $groups);

						$find = "SELECT * FROM `yaraku_tbl` WHERE Title='".$matched_title."' AND Author ='".$matched_author."'";
						$result_find = $db_handle->runQuery($find);
						if(!$result_find) {
							$query = "INSERT INTO `yaraku_tbl`(Title, Author,Groups) VALUES ('".$matched_title."', '".$matched_author."', '".$groups."')";
							$db_handle->runInsert($query);
							if($matched_author != "Unknown"){
								echo $matched_title." by ".$matched_author." has been added to your book list.";
							}
							else {
								echo $matched_title." has been added to your book list.";
							}
						}
					}
				}

				if(isset($_POST['addBookField']) and isset($_POST['addToList'])) {
					addBook($_POST['addBookField']);
				}
			?>
		</div>

		<div>
			<h3>Book list</h3>
			
			<?php

			$values = array("full", "title", "author");
			$texts = array("Titles and Authors", "Titles only", "Authors only");

			if(!isset($_POST['export_options'])) {
				echo '
				<form action="booklist.php" method="POST">
				<label for="export">Export:</label><br>
				<select id="export" name="export_options" onchange="this.form.submit()">';
				foreach($values as $idx=>$val){
					echo '<option value="'.$val.'" name="'.$val.'">'.$texts[$idx].'</option>';
				}
				echo '</select>

				<a href="booklist.php?export=csv_full" target="_blank" download>CSV</a>
				<a href="booklist.php?export=xml_full" target="_blank" download>XML</a>
				</form>
				';
			}
			else {
				echo '
				<form action="booklist.php" method="POST">
				<label for="export">Export:</label><br>
				<select id="export" name="export_options" onchange="this.form.submit()">';
				foreach($values as $idx=>$val){
					if($_POST['export_options'] == $val) {
						echo '<option value="'.$val.'" name="'.$val.'" selected>'.$texts[$idx].'</option>';
					}
					else {
						echo '<option value="'.$val.'" name="'.$val.'">'.$texts[$idx].'</option>';
					}
				}
				echo '</select>
				<a href="booklist.php?export=csv_'.$_POST['export_options'].'" target="_blank" download>CSV</a>
				<a href="booklist.php?export=xml_'.$_POST['export_options'].'" target="_blank" download>XML</a>
				</form>
				';
			}

			require_once("dump.php");
			if(isset($_GET['export']) and $_GET['export'] == "csv_full") {
				export_csv_full();
				echo "CSV successfully exported!<br><br>";
			}

			if(isset($_GET['export']) and $_GET['export'] == "xml_full") {
				export_xml_full();
				echo "XML successfully exported!<br><br>";
			}

			if(isset($_GET['export']) and $_GET['export'] == "csv_title") {
				export_csv_title();
				echo "CSV successfully exported!<br><br>";
			}

			if(isset($_GET['export']) and $_GET['export'] == "xml_title") {
				export_xml_title();
				echo "XML successfully exported!<br><br>";
			}

			if(isset($_GET['export']) and $_GET['export'] == "csv_author") {
				export_csv_author();
				echo "CSV successfully exported!<br><br>";
			}

			if(isset($_GET['export']) and $_GET['export'] == "xml_author") {
				export_xml_author();
				echo "XML successfully exported!<br><br>";
			}

			$themes = array('All groups', 'Medieval Fantasy', 'Novel', 'Family Life', 'Royalty', 'Family Fantasy', 'Human Relationships', 'General Fiction', 'Science Fiction', 'Country Affairs', 'Speculative Fiction');

			echo 
			'<label for="groups">Filter by group:</label>

			<form method="post">
				<select id="groups" name="options">';
			foreach($themes as $t) {
				if(isset($_POST['options']) and $_POST['options'] == $t) {
					echo
						'<option value="'.$t.'" selected>'.$t.'</option>';
				}
				elseif(isset($g_option) and $g_option == $t) {
					echo
						'<option value="'.$t.'" selected>'.$t.'</option>';
				}
				else {
					echo
					'<option value="'.$t.'">'.$t.'</option>';
				}
			}
			echo
				'</select>
				<button type="submit" name="group_submit">Go</button>
			</form>';
			?>

			<table>
				<tr>
					<?php
						if(isset($_POST['group_submit']) and isset($_POST['options']) and $_POST['options'] != "All groups") {
							$group = $_POST['options'];
							$query = "SELECT * FROM `yaraku_tbl` WHERE Groups LIKE '%".$group."%'";
							$result = $db_handle->runQuery($query);
							if(!$result) {
								echo "No book for the selected group.";
							}
							else {
								echo "<tr><th style='width:200px'>Title</th><th style='width:200px'>Author</th><th style='width:200px'>Group(s)</th><th style='width:180px'>Get suggestions</th><th style='width:180px'>Remove from book list</th></tr>";
								$a = array();
								foreach($result as $r) {
									echo "<tr>
									<td>".$r['Title']."</td>
									<td>".$r['Author']."</td>
									<td>".$r['Groups']."</td>
									<td style='text-align:center'><a href='booklist.php?g_title=".urlencode($r['Title'])."&g_author=".urlencode($r['Author'])."'><b>Get</b></a></td>
									<td style='text-align:center'><a href='booklist.php?d_title=".urlencode($r['Title'])."&d_author=".urlencode($r['Author'])."'><b>Delete</b></a></td>
									</tr>";
								}
							}
						}

						else {
							$query = "SELECT * FROM `yaraku_tbl`";
							$result = $db_handle->runQuery($query);
							if(!$result) {
								echo "Your book list is empty!";
							}
							else {
								echo "<tr><th style='width:200px'>Title</th><th style='width:200px'>Author</th><th style='width:200px'>Group(s)</th><th style='width:180px'>Get suggestions</th><th style='width:180px'>Remove from book list</th></tr>";
								$a = array();
								foreach($result as $r) {
									echo "<tr>
									<td>".$r['Title']."</td>
									<td>".$r['Author']."</td>
									<td>".$r['Groups']."</td>
									<td style='text-align:center'><a href='booklist.php?g_title=".urlencode($r['Title'])."&g_author=".urlencode($r['Author'])."'><b>Get</b></a></td>
									<td style='text-align:center'><a href='booklist.php?d_title=".urlencode($r['Title'])."&d_author=".urlencode($r['Author'])."'><b>Delete</b></a></td>
									</tr>";
								}
							}
						}

						if(isset($_GET['d_title']) and isset($_GET['d_author'])) {
							$query = "DELETE FROM `yaraku_tbl` WHERE Title ='".urldecode($_GET['d_title'])."' AND Author ='".urldecode($_GET['d_author'])."'";
							$result = $db_handle->runDelete($query);
							header('Location: booklist.php');
						}
					?>
				</tr>
			</table>
		</div>

		<?php
			if(isset($_GET['g_title']) and isset($_GET['g_author'])) {
				echo "<div>";
				$g_option = $_POST['options'];
				$response = file_get_contents('https://yaraku-book.herokuapp.com/recommend?title='.urlencode($_GET['g_title']).'&number=5');
				$recommmendations = explode("], [", $response);
				$i = 0;
				$main_book = explode(", ", str_replace("'", "", str_replace("[[", "", $recommmendations[0])));
				$main_title = $main_book[0];
				$main_author = $main_book[1];
				echo "<h3>Recommendation list</h3>
				Here are the top five recommendations for '".$main_title."' by ".$main_author.":<br>
				<form method='post'>
				<table>
				<tr><th style='width:200px'>Title</th><th style='width:200px'>Author</th><th style='width:180px'>Add to book list</th></tr>";
				foreach($recommmendations as $rec) {
					if($i != 0) {
						$rec_attrs = explode(", ", str_replace("'", "", str_replace("]]", "", $rec)));
						$rec_title = $rec_attrs[0];
						$rec_author = $rec_attrs[1];
						echo "<tr><td><input type='checkbox' name='check_list[]' value='".$rec_title." , ".$rec_author."'>".$rec_title."</td>
							<td>".$rec_author."</td>
							<td style='text-align:center'><a href='booklist.php?add_title=".urlencode($rec_title)."&add_author=".urlencode($rec_author)."'><b>Add Book</b></a></td></tr>";
					}
					$i += 1;
				}
				echo "</table>";
				echo "<input type='submit' name='addMultBooks' value='Add Selected Books'/></form>";
				echo "</div>";
			}

			if(isset($_GET['add_title']) and isset($_GET['add_author'])) {
				addBook($_GET['add_title']);
				header('Location: booklist.php');
			}

			if(isset($_POST['addMultBooks'])){
				if(!empty($_POST['check_list'])){
				// Loop to store and display values of individual checked checkbox.
					foreach($_POST['check_list'] as $selected){
						$selected_book = explode(" , ", $selected);
						$sel_title = $selected_book[0];
						$sel_author = $selected_book[1];
						addBook($sel_title);
					}
					header('Location: booklist.php');
				}
			}
		?>
	</body>
</html>