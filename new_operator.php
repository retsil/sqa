<?php

require_once('settings.php');

$link = mysql_connect($db_server,$db_username,$db_password);
if (! mysql_select_db($db_database)) {
  die(mysql_error());
}

if (preg_match('/^\d+$/', $_POST['institution_id'])) {

  $query = 'SELECT institution_id FROM institution WHERE institution_id=' . $_POST['institution_id'] . '';

  $result = mysql_query($query);
  if (!$result) {
    $message  = 'Invalid query: ' . mysql_error() . "\n";
    $message .= 'Whole query: ' . $query;
    die($message);
  }

  if ($row = mysql_fetch_assoc($result)) {
    $institution_id = $row['institution_id'];
  } else {
    $institution_id = 0;
  }
 } else {
  $institution_id = 0;
 }

$query = sprintf('insert into operator (operator_code, full_name, phone_number, email_address, institution_id, registered) values (SUBSTRING(UUID(),1,6), "%s", "%s", "%s", %d, NOW())',
		 mysql_real_escape_string( $_POST['full_name']),
		 mysql_real_escape_string( $_POST['phone_number']),
		 mysql_real_escape_string( $_POST['email_address']),
		 $institution_id);


$result = mysql_query($query);
if (!$result) {
    $message  = 'Invalid query: ' . mysql_error() . "\n";
    $message .= 'Whole query: ' . $query;
    die($message);
}

$operator_id = mysql_insert_id();

$query = 'SELECT operator_code FROM operator WHERE operator_id="' . $operator_id . '"';


$result = mysql_query($query);
if (!$result) {
  $message  = 'Invalid query: ' . mysql_error() . "\n";
  $message .= 'Whole query: ' . $query;
  die($message);
 }

if ($row = mysql_fetch_assoc($result)) {
  $operator_code = $row['operator_code'];
 } else {
  die('Operator not inserted');
 }
 
	
if ($_POST['email_back']=='Notify') {
	$headers = 'From: Robert.Barnett@swahs.health.nsw.gov.au' . "\r\n";
	$email = $_POST['email_address'];
	$text = '';
	$text .= 'An operator code has been created for you be the ANZSNM software quality assurance website. ' . "\r\n";
	$text .= 'Your operator code is  ' . $operator_code . "\r\n";
	$text .= 'You can use your code to anonymously participate in software audits at http://apps.anzsnm.org.au/sqa/' . "\r\n";
	$text .= ''. "\r\n";
	$text .= 'This is an automated alert. Inform the sender if this was sent in error.'. "\r\n";
	if (! mail($email,'ANZSNM SQA Operator Code',$text,$headers)) die("Could not send email");
}		
		
setcookie ( 'operator_code', $operator_code, time()+60*60*24*30);

require('checked_in.php');
return;

?>