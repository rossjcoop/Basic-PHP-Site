<?php

try {
	$db = new PDO("mysql:host=localhost;dbname=database;port=3306","root","root");
	var_dump($db);
} catch (Exception $e) {
	echo "Unable to connect";
	exit;
}


echo "Connected to the database";