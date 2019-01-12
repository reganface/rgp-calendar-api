<?php

require_once "class.calendar.php";

$widget_guid = "ed43c50ed6b947ecb4a628fd4f0b5c66";

try {
	$calendar = new Calendar($widget_guid);
	$schedule = $calendar->get_data();
} catch (Exception $e) {
	echo "<h3 style='color:red;'>{$e->getMessage()}</h3>";
	die();
}


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