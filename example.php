<?php

require_once "class.calendar.php";

$widget_guid = "";

try {
	// class throws exceptions on errors so it should be in a try block
	$calendar = new Calendar($widget_guid);
	$schedule = $calendar->get_data();
} catch (Exception $e) {
	echo "<h3 style='color:red;'>{$e->getMessage()}</h3>";
	die();
}


// output
foreach($schedule as $date => $classes) {
	echo "<h4>" . date("l, F jS, Y", strtotime($date)) . "</h4>";

	foreach($classes as $class) {
		echo "{$class["title"]} | {$class["instructor"]} - {$class["spaces"]} -- starting at ";
		echo date("g:i a", strtotime($class["start"]));
		echo " <a href='{$class["url"]}'>Information and Dates</a>";
		echo "<br>";
	}
	echo "<br><br>";
}

?>