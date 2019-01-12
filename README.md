# RGP Calendar API
Basic class for fetching calendar data from RGP's servers and organizing it for use in your own front end schedule widgets.  You will need to create a Calendar Widget in the Rock Gym Pro calendar in order for this class to be functional.

## Disclaimer
The end-point in use has not changed for some time, however, RGP could make a breaking change at any point without notice.  This is unofficial code that is not maintained by Rock Gym Pro.

## Usage
Usage is fairly simple.  Include the class file, instantiate the calendar object with the widget_guid, then get the data and do what you want with it.  Instructions on where to find your widget GUID can be found at the bottom of this document.

``` php
<?php
require_once "class.calendar.php";
$widget_guid = "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx";  // replace this with your calendar widget guid

try {
    $calendar = new Calendar($widget_guid);
    $schedule = $calendar->get_data();

    // do stuff

} catch (Exception $e) {
    // the calendar class throws exceptions on errors
    echo $e->getMessage();
}
?>
```

# Details
### new Calendar(widget_guid [, start_date [, end_date]])
* `widget_guid {string}` Unique id of the calendar widget
* `start_date {int}` Optional start date as a unix time stamp.  Defaults to today.  Note that RGP's end point does not return any class information earlier than the current day
* `end_date {int}` Optional end date as a unix time stamp.  Defaults to +7 days from the start date

### get_data()
* Returns all calendar data in a formatted and sorted array.
* `data {array}`
   * `2019-01-01 {array}` array index names are dates in YYYY-MM-DD format
      * `allDay {bool}` Bool indicating that this is an all day event
	  * `backgroundColor {string}` Hex color value of the background color set in the offering options
	  * `className {string}` The class name used in RGP's widget
	  * `courseGUID {string}`
	  * `description {string}` This is the class description that is normally displayed when clicking on an event in the RGP Calendar Widget, however, the excess HTML tags, the title and instructor, and the link for information and dates has been removed.  Any formatting of the actual description (applied from within the RGP offering) has been left intact.
	  * `end {datetime}` End date and time of the class.  Includes the timezone offset `2019-01-01T18:00:00-08:00`
	  * `instructor {string}` The name of the lead instructor.  This option must be enabled in the offering options from within RGP
	  * `offeringGUID {string}`
	  * `rawDescription {string}` The class description as RGP provides it.  This is the full HTML that is displayed on the popup when clicking a class in the RGP Calendar widget.
	  * `rawTitle {string}` The class title as RGP provides it.  This includes title, instructor, and spaces remaining with HTML tags embeded
	  * `spaces {string}` Class availability.  Will show available, unavailable, or the number of spaces remaining depending on the current status and offering options
	  * `start {datetime}` Start date and time of the class.  Includes the timezone offset `2019-01-01T18:00:00-08:00`
	  * `textColor {string}` Hex color value of the text color set in the offering options
	  * `title {string}` The online display name of the offering
	  * `url {string}` This is the url to use for a booking link of this offering.

### get_raw_data()
* In case you want the data just as RGP provides it, you can use `get_raw_data()` in place of `get_data()`.  This will return an unsorted array of classes, with roughly the same information as above.  No parsing will be done, so `title` will include the offering title, instructor name, and spaces availalbe as one string.  The class description will also be called `popupHTML` instead of `description`.

# Finding Your Widget GUID
The widget GUID can be obtained through RGP by opening up the Calendar and navigating to `Manage Schedule -> Online Widgets`.  From there select an existing calendar widget, or create a new one.  On the `Widget Website Code` tab, choose `Your Website - Linked`, and then copy just the widget GUID from the URL provided.  The GUID is the 32 character long string of random letters and numbers at the end of the url.

![widget GUID location](https://i.imgur.com/HaZHBNk.png)
