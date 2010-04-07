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

#echo $sheet_id;

   $query = sprintf('SELECT title, type_name, number_of_rows, number_of_columns FROM module_sheets LEFT JOIN module_sheet_type ON module_sheets.type_id=module_sheet_type.type_id WHERE sheet_id=%s',$sheet_id);


    $result = mysql_query($query);
    if (!$result) {
      $message  = 'Invalid query: ' . mysql_error() . "\n";
      $message .= 'Whole query: ' . $query;
      die($message);
    }
 if ($row = mysql_fetch_assoc($result)) {
   $number_of_columns =  $row['number_of_columns'];
   $number_of_rows =  $row['number_of_rows'];
   $title =  $row['title'];
   $type_name =  $row['type_name'];
 } else { 
   die('Spreadsheet not found');
 }
#echo $number_of_rows;
#echo $number_of_columns;


header('Content-type: text/csv');
header('Content-Disposition: attachment;filename="' . $title . '-' . $type_name . '.csv"');

for ($r=0; $r < $number_of_rows; $r++) {
  if ($r > 0) echo "\n";
  for ($c=0; $c < $number_of_columns; $c++) {
    if ($c > 0) echo ',';
    $query = sprintf('SELECT value FROM module_data WHERE sheet_id=%s AND row_id=%s AND column_id=%s',$sheet_id,$r,$c);
    if ($result = mysql_query($query)) {
      $row = mysql_fetch_assoc($result);
      echo '"' . addslashes($row['value']) . '"';
    }
  }
 }
?>