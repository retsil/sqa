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
  $query = 'SELECT collection_id, institution_id FROM collection WHERE collection_id=' . $_POST['collection_id'] . '';
  
  $result = mysql_query($query);
  if (!$result) {
    $message  = 'Invalid query: ' . mysql_error() . "\n";
    $message .= 'Whole query: ' . $query;
    die($message);
  }
  
  if ($row = mysql_fetch_assoc($result)) {
    $collection_id = $row['collection_id'];
    $institution_id = $row['institution_id'];
  } else {
    die('Invalid collection: collection_id not found');
  }
 } else {
  die('Invalid collection: No collection_id');
 }


$query = 'SELECT visible_priv FROM institution LEFT JOIN institution_auth ON institution_auth.institution_id = institution.institution_id WHERE institution_auth.moodle_id = ' . $moodle_id;

$result = mysql_query($query);
if (!$result) {
    $message  = 'Invalid query: ' . mysql_error() . "\n";
    $message .= 'Whole query: ' . $query;
    die($message);
}

if (! $row = mysql_fetch_assoc($result)) die("No auth for institution");

if (array_key_exists('visible',$_POST)) {
    if (! $row['visible_priv']) die("Not allowed to change visibility");

    $query = sprintf('UPDATE collection SET visible=%d WHERE collection_id=%d',$_POST['visible']=='1',$collection_id);

    $result = mysql_query($query);
    if (!$result) {
        $message  = 'Invalid query: ' . mysql_error() . "\n";
        $message .= 'Whole query: ' . $query;
        die($message);
    }
}

if (array_key_exists('is_audit',$_POST)) {

    if ($_POST['is_audit'] == '1') {
        $query = sprintf('UPDATE collection SET allow_report=0, allow_user_lock=1, allow_multiple_sessions=0, is_audit=1 WHERE collection_id=%d',$collection_id);
    } else {
        $query = sprintf('UPDATE collection SET allow_report=1, allow_user_lock=0, allow_multiple_sessions=1, is_audit=0 WHERE collection_id=%d',$collection_id);

    }
    $result = mysql_query($query);
    if (!$result) {
        $message  = 'Invalid query: ' . mysql_error() . "\n";
        $message .= 'Whole query: ' . $query;
        die($message);
    }
}

header("Location: " . $sqa_www_root . "/admin_audits.php");
?>
