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

if (preg_match('/^\d+$/', $_POST['collection_id']) and preg_match('/^\d+$/', $_POST['session_id'])) {
    
  $query = sprintf('SELECT collection.collection_id, password, session_id FROM collection, session, institution_auth WHERE session.collection_id=collection.collection_id AND session.session_id=%d AND collection.collection_id=%d AND collection.institution_id=institution_auth.institution_id AND moodle_id=%d', $_POST['session_id'],$_POST['collection_id'], $USER->id);
 $result = mysql_query($query);
    if (!$result) {
      $message  = 'Invalid query: ' . mysql_error() . "\n";
      $message .= 'Whole query: ' . $query;
      die($message);
    }
    if ($row = mysql_fetch_assoc($result)) {
      $collection_id =  $row['collection_id'];
      $session_id =  $row['session_id'];
   } else {
      die('Session not found: ' . $query);
    }
 } else {
  die('Session not specified');
 }

if ($_POST['verify'] == '1') {
  $is_verified=1;
 } else {
  $is_verified=0;
 }
$query = sprintf('UPDATE session SET is_verified=%d WHERE session_id=%d',
		 $is_verified, 
		 $session_id);

 $result = mysql_query($query);
if (!$result) {
  $message  = 'Invalid query: ' . mysql_error() . "\n";
  $message .= 'Whole query: ' . $query;
  die($message);
    }

require('admin_sessions.php');


?>