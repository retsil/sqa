<?php

require_once('settings.php');

require_once('moodle.inc');


 if (isloggedin() && $USER->username != 'guest') {
     $moodle_id = $USER->id;
 } else {
   die("Not logged in");
 }

   if (isloggedin() && $USER->username != 'guest') {
     $moodle_id = $USER->id;
   } else {
     die("Not logged in");
   }


$link = mysql_connect($db_server,$db_username,$db_password);
if (! mysql_select_db($db_database)) {
  die(mysql_error());
}

if (empty( $sheet_id)) {
  if (preg_match('/^\d+$/', $_POST['sheet_id'])) {
    
    $query = sprintf('SELECT sheet_id FROM module_sheets WHERE sheet_id=%s AND moodle_id=%s',
		     $_POST['sheet_id'],
		     $moodle_id);
    $result = mysql_query($query);
    if (!$result) {
      $message  = 'Invalid query: ' . mysql_error() . "\n";
      $message .= 'Whole query: ' . $query;
      die($message);
    }
    if ($row = mysql_fetch_assoc($result)) {
      $sheet_id =  $row['sheet_id'];
    } else {
      $message = 'Unknown sheet id ' . $_GET['sheet_id'] . " for user " . $moodle_id;
      die($message);
    }      
  } else {
    $message = 'Invalid sheet id';
    die($message);
    return;
  }
 }

$query = sprintf('DELETE FROM module_sheets WHERE sheet_id=%s',$sheet_id);
    $result = mysql_query($query);
    if (!$result) {
      $message  = 'Invalid query: ' . mysql_error() . "\n";
      $message .= 'Whole query: ' . $query;
      die($message);
    }


$query = sprintf('DELETE FROM module_data WHERE sheet_id=%s',$sheet_id);
    $result = mysql_query($query);
    if (!$result) {
      $message  = 'Invalid query: ' . mysql_error() . "\n";
      $message .= 'Whole query: ' . $query;
      die($message);
    }

header("Location:http://anzsnm-test.barnett.id.au/sqa/admin_audits.php");

?>