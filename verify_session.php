<?php

require_once('settings.php');

$link = mysql_connect($db_server,$db_username,$db_password);
if (! mysql_select_db($db_database)) {
  die(mysql_error());
}

if (preg_match('/^\d+$/', $_POST['collection_id']) and (!empty( $_POST['password'])) and preg_match('/^\d+$/', $_POST['session_id'])) {
    
  $query = sprintf('SELECT collection.collection_id, password, session_id FROM collection, session WHERE session.collection_id=collection.collection_id AND session.session_id=%d AND collection.collection_id=%d AND password="%s"', $_POST['session_id'],$_POST['collection_id'], $_POST['password']);
 $result = mysql_query($query);
    if (!$result) {
      $message  = 'Invalid query: ' . mysql_error() . "\n";
      $message .= 'Whole query: ' . $query;
      die($message);
    }
    if ($row = mysql_fetch_assoc($result)) {
      $collection_id =  $row['collection_id'];
      $password =  $row['password'];
      $session_id =  $row['session_id'];
   } else {
      die('Session not found');
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