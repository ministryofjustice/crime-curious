<?php

	class twitter_api {
		// Set key/secret
		private $cons_key 		= "xmEFdlfmPCbrh3Oip5baLyCNf";
		private $cons_secret 	= "7SL9KhjYY1fiFCvmMFpz2keoSpNTcTTp5JJVreIQvSLQBOJnne";

		private function get_token() {

			// URL encode key/secret
			$enc_key		= urlencode($this->cons_key);
			$enc_secret		= urlencode($this->cons_secret);

			// Concatenate key/secret (seperated with colon)
			$keysecret = $enc_key . ":" . $enc_secret;

			// Base64 encode key/secret string
			$keysecret_64 = base64_encode($keysecret);

			// oAuth URL
			$url = "https://api.twitter.com/oauth2/token";

			// oAuth POST header
			$headers = array(
			    'Content-Type:application/x-www-form-urlencoded;charset=UTF-8',
			    'Authorization: Basic '. $keysecret_64
			);

			$ch = curl_init();

			curl_setopt($ch,CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 

			$oauth_results = json_decode(curl_exec($ch));

			curl_close($ch);

			if (isset($oauth_results->access_token)) {
				return $oauth_results->access_token;
			} else {
				print_r($oauth_results);
			}
		}

		private function get_tweets($twitter_user,$max = 0) {
			$tweet_url = "https://api.twitter.com/1.1/statuses/user_timeline.json?screen_name=$twitter_user&count=200";
			if($max!==0) {
				$tweet_url.="&max_id=$max";
			}

			$headers = array(
			    'Authorization: Bearer '. $this->get_token()
			);

			$ch = curl_init();

			curl_setopt($ch,CURLOPT_URL, $tweet_url);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 

			$tweet_results = json_decode(curl_exec($ch));

			return $tweet_results;
		}

		public function get_tweets_month($twitter_user) {
			$p_api = new police_api();

			$last_updated = $p_api->last_update_cached();

			// Get first and last day of month
			$first_day = date('Y-m-01', strtotime($last_updated));
			$last_day = date('Y-m-t', strtotime($last_updated));

			$returned_tweets = array();
			$tweets = $this->get_tweets($twitter_user);

			if (count($tweets)<1) {
				return array("tweets"=>array());
			}

			// While tweet still in date range
			$count = count($tweets)-1;
			$index = 0;
			$total = 0;

			while($count>0 && strtotime($tweets[$count]->created_at)>strtotime($last_day." 23:59:59 +0000")) {
				$last_tweet_id = $tweets[$count]->id;
				$tweets = $this->get_tweets($twitter_user,$last_tweet_id-1);
				$count = count($tweets)-1;
			}
			while (strtotime($tweets[$index]->created_at)>strtotime($first_day." 00:00:00 +0000")) {
				$cur_tweet = $tweets[$index];
				// If tweet not later than last day of month
				if (strtotime($cur_tweet->created_at)<=strtotime($last_day." 23:59:59 +0000")) {
					$total++;
					if (isset($cur_tweet->coordinates) && $cur_tweet->coordinates) {
						// Copy tweet to new array)
						$returned_tweets[] = $cur_tweet;
					}
				}
				$last_tweet_id = $cur_tweet->id;
				if($index==$count) {
					$tweets = $this->get_tweets($twitter_user,$last_tweet_id-1);
					$count = count($tweets)-1;
					if(strtotime($tweets[$count]->created_at)>strtotime($last_day." 23:59:59 +0000")) {
						$index=$count;
					} else {
						$index=0;
					}
				}
				$index++;
			}
			// End while

		return(array("total"=>$total,"tweets"=>$returned_tweets));
		}
	}

?>