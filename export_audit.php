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


if (preg_match('/^\d+$/', $_POST['collection_id'])) {
  $query = 'SELECT module_id, collection_id, institution_id FROM collection WHERE collection_id=' . $_POST['collection_id'] . '';
  
  $result = mysql_query($query);
  if (!$result) {
    $message  = 'Invalid query: ' . mysql_error() . "\n";
    $message .= 'Whole query: ' . $query;
    die($message);
  }
  
  if ($row = mysql_fetch_assoc($result)) {
    $collection_id = $row['collection_id'];
    $institution_id = $row['institution_id'];
    $module_id = $row['module_id'];
  } else {
    die('Invalid collection: collection_id not found');
  }
 } else {
  die('Invalid collection: No collection_id');
 }


$query = 'SELECT manage_priv FROM institution LEFT JOIN institution_auth ON institution_auth.institution_id = institution.institution_id WHERE institution_auth.moodle_id = ' . $moodle_id;

$result = mysql_query($query);
if (!$result) {
    $message  = 'Invalid query: ' . mysql_error() . "\n";
    $message .= 'Whole query: ' . $query;
    die($message);
}

if (! $row = mysql_fetch_assoc($result)) die("No auth for institution");

if (! $row['manage_priv']) die("Not allowed to manage this collection");

$sheet_name = $_POST['sheet_name'];
if ($sheet_name == '') die("Sheet name must not be empty");



$query = sprintf('SELECT import_module, export_module FROM module WHERE module_id=%d', $module_id);

$result = mysql_query($query);
if (!$result) {
    $message  = 'Invalid query: ' . mysql_error() . "\n";
    $message .= 'Whole query: ' . $query;
    die($message);
}

if (! $row = mysql_fetch_assoc($result)) die("No such module " . $module_id);

if (! require_once($row['export_module'])) die("Export module not loaded " . $row['export_module']);

# header("Location: " . $sqa_www_root . "/admin_audits.php");

?>test
