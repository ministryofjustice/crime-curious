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
	}

?>

