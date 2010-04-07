<?php

require_once('settings.php');

$link = mysql_connect($db_server,$db_username,$db_password);
if (! mysql_select_db($db_database)) {
  die(mysql_error());
}

if (preg_match('/^\d+$/', $_POST['collection_id'])) {
  $query = 'SELECT collection_id FROM collection WHERE collection_id=' . $_POST['collection_id'] . '';
  
  $result = mysql_query($query);
  if (!$result) {
    $message  = 'Invalid query: ' . mysql_error() . "\n";
    $message .= 'Whole query: ' . $query;
    die($message);
  }
  
  if ($row = mysql_fetch_assoc($result)) {
    $collection_id = $row['collection_id'];
  } else {
    die('Invalid collection: collection_id not found');
  }
 } else {
  die('Invalid collection: No collection_id');
 }


if (preg_match('/^\d+$/', $_POST['operator_id']) and preg_match('/^\w\w\w\w\w\w$/', $_POST['operator_code'])) {
  $query = 'SELECT operator_id, operator_code FROM operator WHERE operator_id=' . $_POST['operator_id'] . ' AND operator_code="' .  $_POST['operator_code'] . '"';
  
  $result = mysql_query($query);
  if (!$result) {
    $message  = 'Invalid query: ' . mysql_error() . "\n";
    $message .= 'Whole query: ' . $query;
    die($message);
  }
  
  if ($row = mysql_fetch_assoc($result)) {
    $operator_id = $row['operator_id'];
    $operator_code = $row['operator_code'];
  } else {
    die('Invalid operator: Operator not found');
  }
 } else {
  die('Invalid operator: Operator not specified');
 }

$query = sprintf('insert into session (session_code, collection_id, operator_id, comments, registered) values (SUBSTRING(UUID(),1,6), %d, %d, "%s", NOW())',
		 $collection_id,
		 $operator_id, 
		 mysql_real_escape_string( $_POST['comments']));


$result = mysql_query($query);

if (!$result) {
    $message  = 'Invalid query: ' . mysql_error() . "\n";
    $message .= 'Whole query: ' . $query;
    die($message);
}

$session_id =  mysql_insert_id($link);

$query = sprintf('select register_module from collection, module WHERE collection.module_id=module.module_id AND collection_id=%d',$collection_id);

$result = mysql_query($query);

if ($row = mysql_fetch_assoc($result)) {
  $register_module = $row['register_module'];
  if ($register_module != '') {
    require($register_module);
  }
 } else {
  die('Module not found');
 }


require('checked_in.php');
return;



