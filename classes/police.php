<?php

	class police_api {
		private function get_content($file,$hours = 24) {
			//vars
			$current_time = time(); $expire_time = $hours * 60 * 60; $file_time = filemtime($file);
			//decisions, decisions
			if(file_exists($file) && ($current_time - $expire_time < $file_time)) {
				return file_get_contents($file);
			}
			else {
				$content = $this->last_update();
				// $content.= '<!-- cached:  '.time().'-->';
				file_put_contents($file,$content);
				return $content;
			}
		}

		private function last_update() {
			$ch = curl_init();

			$url = "http://data.police.uk/api/crime-last-updated";

			curl_setopt($ch,CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 

			$results = json_decode(curl_exec($ch));

			return $results->date;
		}

		public function last_update_cached() {
			return $this->get_content("last-update.txt");
		}

		private function get_crimes($lat,$lng) {
			$date = date('Y-m', strtotime($this->get_content("last-update.txt")));
			$ch = curl_init();

			$url = "http://data.police.uk/api/crimes-at-location?date=$date&lat=$lat&lng=$lng";

			curl_setopt($ch,CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 

			$results = json_decode(curl_exec($ch));

			return $results;
		}

		public function match_crimes($month_tweets) {
			$crimes = array();
			foreach($month_tweets as $tweet) {
				$lat = $tweet->coordinates->coordinates[1];
				$lng = $tweet->coordinates->coordinates[0];
				$new_crimes = $this->get_crimes($lat,$lng);
				if(is_array($new_crimes)) {
					$crimes = array_merge($crimes,$new_crimes);
				}	
			}
			return $crimes;
		}
	}

?>

