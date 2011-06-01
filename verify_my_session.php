<?php

require_once('settings.php');

$link = mysql_connect($db_server,$db_username,$db_password);
if (! mysql_select_db($db_database)) {
  die(mysql_error());
}

require_once('session.inc.php');

$session_id = check_session(-1);

$query = sprintf('UPDATE session SET is_verified=1 WHERE session_id=%d',
     $session_id);

$result = mysql_query($query);
if (!$result) {
    $message  = 'Invalid query: ' . mysql_error() . "\n";
    $message .= 'Whole query: ' . $query;
die($message);
}

require('checked_in.php');


?>