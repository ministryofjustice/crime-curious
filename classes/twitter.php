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

			return $oauth_results->access_token;
		}

		public function get_tweets($twitter_user) {
			$tweet_url = "https://api.twitter.com/1.1/statuses/user_timeline.json?screen_name=$twitter_user&count=20";

			$headers = array(
			    'Authorization: Bearer '. $this->get_token()
			);

			$ch = curl_init();

			curl_setopt($ch,CURLOPT_URL, $tweet_url);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 

			$tweet_results = json_decode(curl_exec($ch));

			print_r($tweet_results);
		}
	}

?>