<?php

	// For debugging
	error_reporting(E_ALL);
	ini_set('error_reporting', E_ALL);
	ini_set('display_errors',1);

	// Load Twitter class
	require('/classes/twitter.php');

	$username = $_GET['username'];

	$api = new twitter_api();

	$api->get_tweets($username);

?>