<html>

	<head>
		<title>Crime Curious<?php if(isset($_GET['username']) && strlen($_GET['username'])>0) echo " for ".$_GET['username']; ?></title>
		<link rel="stylesheet" href="assets/css/style.css">
		<link rel="stylesheet" href="assets/css/colours.css">
		<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,600,300' rel='stylesheet' type='text/css'>
		<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
		<link rel="icon" href="/favicon.ico" type="image/x-icon">
	</head>

	<body>

		<div id="spinner-container" class="hidden">
			<div class="spinner">
				<img src="assets/img/spinner.gif">
			</div>
		</div>

		<header>
			<a href="./"><h1>Crime Curious</h1></a>
			<h4>For TechCrunch Disrupt London 2014</h4>
		</header>

		<div id="user-form-container">
			<form id="user=form" name="user-form" method="GET" onsubmit="document.getElementById('spinner-container').className='';">
				<input id="username" name="username" placeholder="Twitter username" value="<?php echo $_GET['username']; ?>">
			</form>
		</div>

		<main>

	<?php

		// For debugging
		error_reporting(E_ALL);
		ini_set('error_reporting', E_ALL);
		ini_set('display_errors',1);

		if(isset($_GET['username']) && strlen($_GET['username'])>0) {
			// Load classes
			require('classes/twitter.php');	// Twitter API calls
			require('classes/police.php');	// UK Police data API calls

			$username = $_GET['username'];

			$twitter_api = new twitter_api();
			$police_api = new police_api();

			$month_tweets = $twitter_api->get_tweets_month($username);

			if($month_tweets['tweets']) {
				$crimes_matched = $police_api->match_crimes($month_tweets['tweets']);
				$total_crimes = count($crimes_matched);
				$tweets_analysed = $month_tweets['total'];
			} else {
				$tweets_analysed = 0;
				$total_crimes = 0;
			}
		?>
		<div id="user-info">
			<div class="info-line">
				<h2>Twitter User</h2>
				<div><?php echo $username; ?></div>
			</div>
			<div class="info-line">
				<h2>Period</h2>
				<div><?php echo date("F Y",strtotime($police_api->last_update_cached())); ?></div>
			</div>
			<div class="info-line">
				<h2>Tweets Analysed</h2>
				<div><?php echo $tweets_analysed; ?></div>
			</div>
			<div class="info-line">
				<h2>Tweets with Geocoding</h2>
				<div><?php echo count($month_tweets['tweets']); ?></div>
			</div>
			<div class="info-line">
				<h2>Crimes Matched</h2>
				<div><?php echo $total_crimes; ?></div>
			</div>
			<div class="danger-quotient">
				<h2>DANGER QUOTIENT</h2>
				<?php 
					if(count($month_tweets['tweets'])>0) {
						echo round(((($total_crimes/count($month_tweets['tweets']))*$month_tweets['total']/cal_days_in_month(CAL_GREGORIAN,date("m",strtotime($police_api->last_update_cached())),date("Y",strtotime($police_api->last_update_cached()))))*100));
					} else {
						echo "N/A";
					}
				?>
			</div>
			<?php if($total_crimes>0) {
				echo "<h3>If you are concerned about any of the crimes featured on this page please contact your local police station, or call <a href='https://crimestoppers-uk.org/'>Crimestoppers</a> on 0800 555 111.</h3>";
			} ?>
		</div>
		<div id="matched-crimes">

			<?php
			if($month_tweets['tweets']) {
				$crimes_matched = $police_api->match_crimes($month_tweets['tweets']);
				foreach($crimes_matched as $crime) { ?>
				<article>
					<!--<?php print_r($crime); ?>-->
					<div class="field-title category">Category</div><div class="field-value category"><?php echo $crime->category; ?></div>
					<div class="field-title location">Location</div><div class="field-value location"><?php echo $crime->location->street->name; ?></div>
					<div class="field-title status">Location</div><div class="field-value status"><?php echo $crime->outcome_status->category; ?></div>
					<iframe src="https://www.google.com/maps/embed?pb=!1m14!1m12!1m3!1d9374.12023630866!2d<?php echo $crime->location->longitude; ?>!3d<?php echo $crime->location->latitude; ?>!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!5e0!3m2!1sen!2suk!4v1413703105819" height="300" frameborder="0" style="border:0;width:100%;"></iframe>
				</article>
				<?php }
			} ?>

		</div>

		<?php } else { echo "<h2 class='aligncenter'>Please enter a Twitter username above</h2>"; } ?>

		</main>

	<footer>

	</footer>

	</body>

</html>