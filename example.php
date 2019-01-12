<?php

require_once "class.calendar.php";

$widget_guid = "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx";	// replace this with your calendar widget guid

try {
	// class throws exceptions on errors so it should be in a try block
	$calendar = new Calendar($widget_guid);
	$schedule = $calendar->get_data();

	// loop through $schedule to access each day in the range
	foreach($schedule as $date => $classes) {
		echo "<h4>" . date("l, F jS, Y", strtotime($date)) . "</h4>";

		// loop through every class on this day
		foreach($classes as $class) {
			echo "{$class["title"]} | {$class["instructor"]} - {$class["spaces"]} -- starting at ";
			echo date("g:i a", strtotime($class["start"]));
			echo " <a href='{$class["url"]}'>Information and Dates</a>";
			echo "<br>";
		}

		echo "<br><br>";
	}

} catch (Exception $e) {
	echo "<h3 style='color:red;'>{$e->getMessage()}</h3>";
	die();
}




?>
