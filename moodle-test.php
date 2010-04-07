

<?php 
require_once('moodle.inc');

#   global $USER;

   if (isloggedin() && $USER->username != 'guest') {
   	echo "<p>You are currently logged in as: " . $USER->firstname . " " . $USER->lastname . "</p>";
	echo "<p>Your ID is: " . $USER->id . "</p>";
   }
?>

