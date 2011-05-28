<?php

$query = sprintf('insert into wwsf_session (session_id) VALUES (%d)',$session_id);

$result = mysql_query($query);

if (!$result) {
    $message  = 'Invalid query: ' . mysql_error() . "\n";
    $message .= 'Whole query: ' . $query;
    die($message);
}

for ($study_id=0; $study_id < 5; $study_id++) {
  $query = sprintf('insert into wwsf_data (session_id, study_id) VALUES (%d,%d)',$session_id, $study_id+1);
  $result = mysql_query($query);

 }

?>