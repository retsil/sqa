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
  if (! preg_match('/^\d+$/', $year)) { return(''); }
  if (! preg_match('/^\d+$/', $month)) { return(''); }
  if (! preg_match('/^\d+$/', $day)) { return(''); }
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

if (preg_match('/^\d+$/', $_POST['institution_id'])) {

  $query = sprintf('SELECT institution_id, visible_priv FROM institution_auth WHERE moodle_id=%d AND institution_id=%d',
		   $moodle_id,$_POST['institution_id']);

  
  $result = mysql_query($query);
  if (!$result) {
    $message  = 'Invalid query: ' . mysql_error() . "\n";
    $message .= 'Whole query: ' . $query;
    die($message);
  }
  
  if ($row = mysql_fetch_assoc($result)) {
    $institution_id = $row['institution_id'];
  } else {
    die('Invalid institution');
  }
 } else {
  die('Invalid institution: No institution_id');
 }


if ($_POST['scope'] == '') die("Scope cannot be empty");

$request_error = 'Request sent';

$query = sprintf('insert into collection (scope,start_date,end_date,module_id,institution_id) ' .
		 ' values ("%s","%s","%s",%d,%d)',
		 mysql_real_escape_string( $_POST['scope']),
		 parse_date3($_POST['start_dateYYYY'],$_POST['start_dateMM'],$_POST['start_dateDD']),
		 parse_date3($_POST['end_dateYYYY'],$_POST['end_dateMM'],$_POST['end_dateDD']),
		 $module_id,$institution_id);


  $result = mysql_query($query);
  if (!$result) {
    $message  = 'Invalid query: ' . mysql_error() . "\n";
    $message .= 'Whole query: ' . $query;
    die($message);
  }

header("Location: " . $sqa_www_root . "/admin_audits.php");

?>