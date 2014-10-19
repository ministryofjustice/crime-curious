<html>

	<head>
		<link rel="stylesheet" href="assets/style.css">
	</head>

	<body>

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
		?>

		<div id="user-info">
		</div>
		<div id="matched-crimes">

			<?php
			if($month_tweets) {
				$police_api = new police_api();
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

	</body>

</html>