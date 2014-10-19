<?php

	// For debugging
	error_reporting(E_ALL);
	ini_set('error_reporting', E_ALL);
	ini_set('display_errors',1);

	// Load classes
	require('classes/twitter.php');	// Twitter API calls
	require('classes/police.php');		// UK Police data API calls

	$username = $_GET['username'];

	$twitter_api = new twitter_api();

	$month_tweets = $twitter_api->get_tweets_month($username);

	print_r($month_tweets);

?>