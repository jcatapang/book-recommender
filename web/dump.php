<?php
	require_once("dbcontroller.php");
	$db_handle = new DBController();

	function save_file($file_url, $file_name) {
		header('Location: '.$file_url);
	}

	function export_csv_full() {
		global $db_handle;
		$query = "SELECT * FROM `yaraku_tbl`";
		$result = $db_handle->runQuery($query);
		$fp = fopen('yaraku_full.csv', 'w');
		fputcsv($fp, array("Title", "Author"));
		foreach($result as $r) {
			fputcsv($fp, array($r['Title'], $r['Author']));
		}
		$url = "https://yaraku-booklist.000webhostapp.com/yaraku_full.csv";
		save_file($url, "yaraku_full.csv");
	}

	function export_xml_full() {
		global $db_handle;
		$query = "SELECT * FROM `yaraku_tbl`";
		$result = $db_handle->runQuery($query);
		$writer = new XMLWriter();
		$writer->openURI('yaraku_full.xml');
		$writer->startDocument("1.0");
		$writer->startElement("books");
		foreach($result as $r) {
			$writer->startElement("book");
			$writer->startElement("title");
			$writer->writeRaw($r['Title']);
			$writer->endElement();
			$writer->startElement("author");
			$writer->writeRaw($r['Author']);
			$writer->endElement();
			$writer->endElement();
		}
		$writer->endElement();
		$writer->endDocument();
		$writer->flush();
		$url = "https://yaraku-booklist.000webhostapp.com/yaraku_full.xml";
		save_file($url, "yaraku_full.xml");
	}

	function export_csv_title() {
		global $db_handle;
		$query = "SELECT Title FROM `yaraku_tbl`";
		$result = $db_handle->runQuery($query);
		$fp = fopen('yaraku_titles.csv', 'w');
		fputcsv($fp, array("Title"));
		foreach($result as $r) {
			fputcsv($fp, array($r['Title']));
		}
		$url = "https://yaraku-booklist.000webhostapp.com/yaraku_titles.csv";
		save_file($url, "yaraku_titles.csv");
	}

	function export_xml_title() {
		global $db_handle;
		$query = "SELECT Title FROM `yaraku_tbl`";
		$result = $db_handle->runQuery($query);
		$writer = new XMLWriter();
		$writer->openURI('yaraku_titles.xml');
		$writer->startDocument("1.0");
		$writer->startElement("books");
		foreach($result as $r) {
			$writer->startElement("book");
			$writer->startElement("title");
			$writer->writeRaw($r['Title']);
			$writer->endElement();
			$writer->endElement();
		}
		$writer->endElement();
		$writer->endDocument();
		$writer->flush();
		$url = "https://yaraku-booklist.000webhostapp.com/yaraku_titles.xml";
		save_file($url, "yaraku_titles.xml");
	}

	function export_csv_author() {
		global $db_handle;
		$query = "SELECT Author FROM `yaraku_tbl`";
		$result = $db_handle->runQuery($query);
		$fp = fopen('yaraku_authors.csv', 'w');
		fputcsv($fp, array("Author"));
		foreach($result as $r) {
			fputcsv($fp, array($r['Author']));
		}
		$url = "https://yaraku-booklist.000webhostapp.com/yaraku_authors.csv";
		save_file($url, "yaraku_authors.csv");
	}

	function export_xml_author() {
		global $db_handle;
		$query = "SELECT Author FROM `yaraku_tbl`";
		$result = $db_handle->runQuery($query);
		$writer = new XMLWriter();
		$writer->openURI('yaraku_authors.xml');
		$writer->startDocument("1.0");
		$writer->startElement("books");
		foreach($result as $r) {
			$writer->startElement("book");
			$writer->startElement("author");
			$writer->writeRaw($r['Author']);
			$writer->endElement();
			$writer->endElement();
		}
		$writer->endElement();
		$writer->endDocument();
		$writer->flush();
		$url = "https://yaraku-booklist.000webhostapp.com/yaraku_authors.xml";
		save_file($url, "yaraku_authors.xml");
	}
?>