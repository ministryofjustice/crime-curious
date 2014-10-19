<html>

	<head>
		<title>Crime Curious<?php if(isset($_GET['username']) && strlen($_GET['username'])>0) echo " for ".$_GET['username']; ?></title>
		<link rel="stylesheet" href="assets/style.css">
		<link rel="stylesheet" href="assets/colours.css">
		<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,600,300' rel='stylesheet' type='text/css'>
	</head>

	<body>

		<header>
			<h1>Crime Curious</h1>
			<h4>For TechCrunch Disrupt London 2014</h4>
		</header>

		<div id="user-form-container">
			<form id="user=form" name="user-form" method="GET">
				<input id="username" name="username" placeholder="Twitter username" value="<?php echo $_GET['username']; ?>">
			</form>
		</div>

		<main>

	<?php

		// For debugging
		// error_reporting(E_ALL);
		// ini_set('error_reporting', E_ALL);
		// ini_set('display_errors',1);

		if(isset($_GET['username'])) {
			// Load classes
			require('classes/twitter.php');	// Twitter API calls
			require('classes/police.php');	// UK Police data API calls

			$username = $_GET['username'];

			$twitter_api = new twitter_api();
			$police_api = new police_api();

			$month_tweets = $twitter_api->get_tweets_month($username);
		}
		?>
		<div id="user-info">
			<h2>Twitter user</h2>
			<div><?php echo $username; ?></div>
			<h2>Date</h2>
			<div><?php echo $police_api->last_update_cached(); ?></div>
		</div>
		<div id="matched-crimes">

			<?php
			if($month_tweets) {
				$crimes_matched = $police_api->match_crimes($month_tweets);
				foreach($crimes_matched as $crime) { ?>
				<article>
					<!--<?php print_r($crime); ?>-->
					<div class="field-title category">Category</div><div class="field-value category"><?php echo $crime->category; ?></div>
					<div class="field-title location">Location</div><div class="field-value location"><?php echo $crime->location->street->name; ?></div>
				</article>
				<?php }
			} ?>

		</div>

	</main>

	<footer>

	</footer>

	</body>

</html>