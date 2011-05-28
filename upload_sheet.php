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

if (empty( $type_id)) {
  if (preg_match('/^\d+$/', $_POST['type_id'])) {
    
    $query = sprintf('SELECT type_id, number_of_columns FROM module_sheet_type WHERE type_id=%s',$_POST['type_id']);
    $result = mysql_query($query);
    if (!$result) {
      $message  = 'Invalid query: ' . mysql_error() . "\n";
      $message .= 'Whole query: ' . $query;
      die($message);
    }
    if ($row = mysql_fetch_assoc($result)) {
      $type_id =  $row['type_id'];
      $number_of_columns = $row['number_of_columns'];
    } else {
      $message = 'Unknown type id ' . $_POST['type_id'];
      die($message);
    }      
  } else {
    $message = 'Invalid type id';
    die($message);
    return;
  }
 }

$name = mysql_real_escape_string( $_POST['name']);
if ($name == '') die("Sheet name must be specified");

$query = sprintf('insert into module_sheets (type_id, title, number_of_rows, moodle_id) values (%d,"%s",0,%d);',$type_id, $name, $moodle_id);

if (!  mysql_query($query)) die("Could not create sheet");

$sheet_id = mysql_insert_id();

$r = 0;
if (($handle = fopen($_FILES['sheet_data']['tmp_name'], "r")) !== FALSE) {
  while (($data = fgetcsv($handle,0, ",")) !== FALSE) {
    $num = count($data);
    if ($num > $number_of_columns) $num = $number_of_columns;
    for ($c=0; $c < $num; $c++) {
      $query = sprintf('insert into module_data (sheet_id, row_id, column_id, value) values (%s,%s,%s,"%s");',
		       $sheet_id, $r, $c, mysql_real_escape_string($data[$c]));
      if (!  mysql_query($query)) die("Could not insert data");
    }
    $r++;
  }
  fclose($handle);
}

$query = sprintf('update  module_sheets set number_of_rows=%s where sheet_id=%s',$r,$sheet_id);

if (!  mysql_query($query)) die("Could not update number of rows");

header("Location: " . $sqa_www_root . "/admin_audits.php");

?>