<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
    "http://www.w3.org/TR/html4/loose.dtd">

<html>
<head>
    	<style type="text/css" title="currentStyle" media="screen">
		@import "official_blue.css";
	</style>
  <title>Export log</title>
</head>
<body>

<div class="sqamenu"><span class="sqamenu"><a href="index.php">Welcome</a></span> <span class="sqamenu"><a href="checked_out.php">Participate</a></span> <span class="sqamenusel">Administrate</span></div>

<h1 class="sqa">Software Quality Assurance</h1>
  <div class="sqah1">&nbsp;</div>


<ul>
<?php


function export_audit() {

require_once('settings.php');

require_once('moodle.inc');


   if (isloggedin() && $USER->username != 'guest') {
     $moodle_id = $USER->id;
   } else {
     return("Not logged in");
   }


$link = mysql_connect($db_server,$db_username,$db_password);
if (! mysql_select_db($db_database)) {
  return(mysql_error());
}

echo "<li>Connected to database</li>";

if (preg_match('/^\d+$/', $_POST['collection_id'])) {
  $query = 'SELECT module_id, collection_id, institution_id FROM collection WHERE collection_id=' . $_POST['collection_id'] . '';
  
  $result = mysql_query($query);
  if (!$result) {
    $message  = 'Invalid query: ' . mysql_error() . "\n";
    $message .= 'Whole query: ' . $query;
    return($message);
  }
  
  if ($row = mysql_fetch_assoc($result)) {
    $collection_id = $row['collection_id'];
    $institution_id = $row['institution_id'];
    $module_id = $row['module_id'];
  } else {
    return('Invalid collection: collection_id not found');
  }
 } else {
  return('Invalid collection: No collection_id');
 }

echo "<li>Identified collection</li>";

$query = 'SELECT manage_priv FROM institution LEFT JOIN institution_auth ON institution_auth.institution_id = institution.institution_id WHERE institution_auth.moodle_id = ' . $moodle_id;

$result = mysql_query($query);
if (!$result) {
    $message  = 'Invalid query: ' . mysql_error() . "\n";
    $message .= 'Whole query: ' . $query;
    return($message);
}

if (! $row = mysql_fetch_assoc($result)) return("No auth for institution");

if (! $row['manage_priv']) return("Not allowed to manage this collection");

$sheet_name = $_POST['sheet_name'];
if ($sheet_name == '') return("Sheet name must not be empty");

echo "<li>Parsed sheet name</li>";


$query = sprintf('SELECT import_module, export_module FROM module WHERE module_id=%d', $module_id);

$result = mysql_query($query);
if (!$result) {
    $message  = 'Invalid query: ' . mysql_error() . "\n";
    $message .= 'Whole query: ' . $query;
    return($message);
}

if (! $row = mysql_fetch_assoc($result)) return("No such module " . $module_id);

echo "<li>Identified module</li>";

if (! include($row['export_module'])) return("Export module not loaded " . $row['export_module']);

echo "<li>Loaded module</li>";

return "Export complete";

}

echo '<li><b>' . export_audit() . '</b></li>';
?>

<li><a href="admin_audits.php">Continue</a></li>

</ul>

