<?php

require_once('settings.php');

require_once('moodle.inc');

 if (isloggedin() && $USER->username != 'guest') {
     $moodle_id = $USER->id;
 } else {
   die("Not logged in");
 }

$link = mysql_connect($db_server,$db_username,$db_password);
if (! mysql_select_db($db_database)) {
  die(mysql_error());
 }

function parse_date3($year,$month,$day) {
  if (! preg_match('/^\d\d\d\d$/', $year)) { return(''); }
  if (! preg_match('/^\d\d$/', $month)) { return(''); }
  if (! preg_match('/^\d\d$/', $day)) { return(''); }
  return $year . '-' . $month . '-' . $day;
}

if (preg_match('/^\d+$/', $_POST['module_id'])) {

  $query = 'SELECT module_id FROM module WHERE module_id=' . $_POST['module_id'] . '';
  
  $result = mysql_query($query);
  if (!$result) {
    $message  = 'Invalid query: ' . mysql_error() . "\n";
    $message .= 'Whole query: ' . $query;
    die($message);
  }
  
  if ($row = mysql_fetch_assoc($result)) {
    $module_id = $row['module_id'];
  } else {
    die('Invalid module');
  }
 } else {
  die('Invalid module: No module_id');
 }


$request_error = 'Request sent';

$query = sprintf('insert into request (institution_title, department, contact_name, phone_number, email_address, scope, start_date, end_date, module_id, moodle_id, lodged) values ("%s","%s","%s","%s","%s","%s","%s","%s",%d,"%s", NOW())',
		 mysql_real_escape_string( $_POST['institution_title']),
		 mysql_real_escape_string( $_POST['department']),
		 mysql_real_escape_string( $_POST['contact_name']),
		 mysql_real_escape_string( $_POST['phone_number']),
		 mysql_real_escape_string( $_POST['email_address']),
		 mysql_real_escape_string( $_POST['scope']),
		 parse_date3($_POST['start_dateYYYY'],$_POST['start_dateMM'],$_POST['start_dateDD']),
		 parse_date3($_POST['end_dateYYYY'],$_POST['end_dateMM'],$_POST['end_dateDD']),
		 $module_id,$moodle_id);


  $result = mysql_query($query);
  if (!$result) {
    $message  = 'Invalid query: ' . mysql_error() . "\n";
    $message .= 'Whole query: ' . $query;
    die($message);
  }

require('admin_audits.php');
return;

?>

