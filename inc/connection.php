<?php

try {
	$db = new PDO("mysql:host=localhost;dbname=database;port=3306","root","root");
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
	echo "Unable to connect";
	echo $e->getMessage();
	exit;
}


try {
	$results = $db->query("SELECT title, category, img FROM Media");
} catch (Exception $e) {
	echo "Unable to retrieved results";
	exit;
}

$catalog = $results->fetchAll(PDO::FETCH_ASSOC);