<?php
	// db connection
	try {
		$db = new PDO('mysql:host=localhost;dbname=ignitionchat;charset=utf8', 'root', 'koding');
	}
	catch (Exception $e) {
		die($e->getMessage());
	}