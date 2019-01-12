<?php
/***************************************
 *
 * Simple class for pulling data from a
 * RGP Calendar Widget
 *
 ***************************************/


class Calendar {
	private $_widget_guid;
	private $_start_date;
	private $_end_date;
	private $_raw_data;
	private $_data;


	public function __construct($widget_guid, $start_date=null, $end_date=null) {
		$this->_widget_guid = trim($widget_guid);

		// make sure this looks like a guid
		if (strlen($this->_widget_guid) !== 32)
			throw new Exception("Invalid widget GUID");

		// default range is one week starting from today
		$this->_start_date = $start_date?:strtotime("today");
		$this->_end_date = $end_date?:strtotime("+7 days", $this->_start_date);

		// fetch data from RGP
		$this->_fetch_data();
	}


	// returns raw data from RGP before it's been reorganized
	public function get_raw_data() {
		return $this->_raw_data;
	}


	// returns formated and sorted data
	public function get_data() {
		$this->_organize_raw_data();
		$this->_parse_data();
		return $this->_data;
	}


	/***************************************
	 *
	 * Private Functions
	 *
	 ***************************************/


	// retrieve the widget data from RGP's servers
	private function _fetch_data() {
		$url = "https://app.rockgympro.com/b/widget/?a=fcfeed&widget_guid={$this->_widget_guid}&start={$this->_start_date}&end={$this->_end_date}";
		$this->_raw_data = json_decode(file_get_contents($url), true);
	}


	// organize classes by date and sort by start time
	private function _organize_raw_data() {
		$this->_data = [];

		if (empty($this->_raw_data))
			return;

		// group each class by date
		foreach($this->_raw_data as $class) {
			$date = date("Y-m-d", strtotime($class["start"]));
			$this->_data[$date][] = $class;
		}

		// sort each day by class time
		foreach($this->_data as $day => $classes) {
			usort($this->_data[$day], "class_sort");
		}

		// finally sort by date
		ksort($this->_data);
	}


	// some data points need to be extracted from text
	private function _parse_data() {
		if (empty($this->_data))
			return;

		foreach($this->_data as $date => $classes) {
			foreach($classes as $index => $class) {
				// save original title and html description
				$this->_data[$date][$index]["rawTitle"] = $class["title"];
				$this->_data[$date][$index]["rawDescription"] = $class["popupHTML"];

				// break out title, instructor, and spaces remaining
				$title = $this->_parse_data_title($class["title"]);

				// get the pure offering description from the popupHTML
				$description =$this-> _parse_data_description($class["popupHTML"]);

				// generate url for information and dates link
				$url = "https://app.rockgympro.com/b/widget/?a=offering&offering_guid={$class["offeringGUID"]}&widget_guid={$this->_widget_guid}&course_guid={$class["courseGUID"]}&mode=p";

				// add to object
				$this->_data[$date][$index]["title"] = $title["title"];
				$this->_data[$date][$index]["instructor"] = $title["instructor"];
				$this->_data[$date][$index]["spaces"] = $title["spaces"];
				$this->_data[$date][$index]["description"] = $description;
				$this->_data[$date][$index]["url"] = $url;

				// remove popupHTML as it exists as rawDescription now
				unset($this->_data[$date][$index]["popupHTML"]);
			}
		}
	}


	// accepts the raw class title and returns the title,
	// instructor, and spaces remaining (if available)
	private function _parse_data_title($raw) {
		$split = explode("<br>", $raw);
		$spaces = strip_tags($split[1]);

		if (strpos($split[0], "|") !== false) {
			// looks like the instructor name is provided
			preg_match('/^(.*)[|](.*+)$/', $split[0], $match);
			$title = trim($match[1]);
			$instructor = trim($match[2]);

		} else {
			// instructor name is not provided
			$title = trim($split[0]);
			$instructor = "";
		}

		return [
			"title" => $title,
			"instructor" => $instructor,
			"spaces" => $spaces
		];
	}


	// strips out the excess text and tags from the popupHTML description
	// leaves formatting html and offering description text
	private function _parse_data_description($raw) {
		// remove title/instructor from beginning of string
		$split = explode("<div class='link-safe-string'>", $raw);

		if (!isset($split[1]))
			return "";	// no description

		// use the "more information" link as a known point in the message
		$pos = strpos($split[1], "<a href=\"/b/widget/?a=offering");

		// remove the end of the message to get the pure description
		$description = substr($split[1], 0, $pos - 30);

		return $description;
	}

}



// sorting function for class times
function class_sort($a, $b) {
	return strtotime($a["start"]) - strtotime($b["start"]);
}
