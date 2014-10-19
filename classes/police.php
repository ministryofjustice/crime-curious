<?php

	class police_api {
		public function last_update() {
			$ch = curl_init();

			$url = "http://data.police.uk/api/crime-last-updated";

			curl_setopt($ch,CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 

			$results = json_decode(curl_exec($ch));

			return $results->date;
		}
	}

?>

