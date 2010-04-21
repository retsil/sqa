<?php

require_once('settings.php');

$link = mysql_connect($db_server,$db_username,$db_password);
if (! mysql_select_db($db_database)) {
  die(mysql_error());
}

if (empty( $operator_id)) {
  if (preg_match('/^\w\w\w\w\w\w$/', $_GET['operator_code'])) {
    setcookie ( 'operator_code',  $_GET['operator_code'], time()+60*60*24*30);
    $operator_code =  $_GET['operator_code'];
  } else {
    if (preg_match('/^\w\w\w\w\w\w$/', $_COOKIE['operator_code'])) {
      $operator_code =  $_COOKIE['operator_code'];
    } else {
      $operator_code_error = 'Invalid operator code';
      require('checked_out.php');
      return;
    }
  }
 

  $query = 'SELECT operator_id FROM operator WHERE operator_code="' . $operator_code . '"';

  $result = mysql_query($query);
  if (!$result) {
    $message  = 'Invalid query: ' . mysql_error() . "\n";
    $message .= 'Whole query: ' . $query;
    die($message);
  }

  if ($row = mysql_fetch_assoc($result)) {
  $operator_id = $row['operator_id'];
  } else {
    $operator_code_error = 'Invalid operator code';
    require('checked_out.php');
    return;
  }
 }
?><!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
    "http://www.w3.org/TR/html4/loose.dtd">

<html>
<head>
    	<style type="text/css" title="currentStyle" media="screen">
		@import "official_blue.css";
	</style>
  <title>Checked in</title>
</head>
<body>

<div class="sqamenu"><span class="sqamenu"><a href="index.php">Welcome</a></span> <span class="sqamenusel">Participate</span> <span class="sqamenu"><a href="admin_audits.php">Administrate</a></span></div>

<h1 class="sqa">Software Quality Assurance</h1>
  <div class="sqah1">&nbsp;</div>

<h2 class="sqa">Sessions</h2>

  <form action="checked_out.php"><span class="sqaopf">Operator code: <span class="sqaop"><?php echo $operator_code; ?></span>
	  <button type="submit">Check out</button></span></form>

<p>On first using this online audit tool, register below for one of the available audits.  This will open a Session, which you can come back to at a later date (without having to re-register).</p>

<p>A list of currently active sessions for entering audit data</p>

<table class="list" width="80%">
<tr>
<th class="list">Scope</th>
<th class="list">Institution</th>
<th class="list">Dataset</th>
<th class="list">Opened On</th>
<th class="list">Verify By</th> <!-- or verified -->
<th class="list">Comments</th>
<th class="list" colspan=2></th>
</tr>

<?php
  $query = sprintf('SELECT session.session_code, session.comments, session.is_verified, collection.scope, DATE_FORMAT(session.registered,"%%e/%%c/%%Y") AS registered_date, DATE_FORMAT(collection.end_date,"%%e/%%c/%%Y") AS end_date, institution.title AS institution, module.title AS module, module.data_url, module.report_url FROM collection, institution, module, session WHERE collection.module_id = module.module_id AND collection.institution_id = institution.institution_id AND session.collection_id=collection.collection_id AND session.operator_id=%d',$operator_id);
$result = mysql_query($query);
if (!$result) {
    $message  = 'Invalid query: ' . mysql_error() . "\n";
    $message .= 'Whole query: ' . $query;
    die($message);
}

if (mysql_num_rows($result) == 0) {
  echo '<tr><td style="text-align: center; font-style: italic;" colspan=6>No sessions</td></tr>';
 }

while ($row = mysql_fetch_assoc($result)) {
  echo '<tr>';
  echo '<td class="list">' . $row['scope'] . '</td>';
  echo '<td class="list">' . $row['institution'] . '</td>';
  echo '<td class="list">' . $row['module'] . '</td>';
  echo '<td class="list">' . $row['registered_date'] . '</td>';
  if ( $row['is_verified'] ) {
    echo '<td class="list">Verified</td>';
  } else {
    echo '<td class="list">' . $row['end_date'] . '</td>';
  }
  echo '<td class="list">' . htmlspecialchars($row['comments']) . '</td>';
  if ( $row['is_verified'] ) {
    echo '<td class="list"><form method="get" action="' . $row['report_url'] . '" class="list"><input type="hidden" name="session_code" value="' . $row['session_code'] . '" /><button type="submit" class="list">View results</button></form></td><td></td>';
  } else {
   echo '<td class="list"><form method="get" action="' . $row['report_url'] . '" class="list"><input type="hidden" name="session_code" value="' . $row['session_code'] . '" /><button type="submit" class="list">View results</button></form></td>';
    echo '<td class="list"><form method="get" action="' . $row['data_url'] . '" class="list"><input type="hidden" name="session_code" value="' . $row['session_code'] . '" /><button type="submit" class="list">Enter data</button></form></td>';
  }
  echo '</tr>';
}

mysql_free_result($result);

?>
</table>

<h2 class="sqa">Audits</h2>

<p>A list of current web based software audits</p>


<table class="list" width="80%">
<tr>
<th class="list">Scope</th>
<th class="list">Institution</th>
<th class="list">Dataset</th>
<th class="list">Start Date</th>
<th class="list">End Date</th>
<th class="list">Comments</th>
</tr>

<?php

$query = 'SELECT collection.collection_id, collection.scope, DATE_FORMAT(collection.start_date,"%e/%c/%Y") AS start_date, DATE_FORMAT(collection.end_date,"%e/%c/%Y") AS end_date, institution.title AS institution, module.title AS module FROM collection, institution, module WHERE collection.module_id = module.module_id AND collection.institution_id = institution.institution_id AND collection.visible=1';
$result = mysql_query($query);
if (!$result) {
    $message  = 'Invalid query: ' . mysql_error() . "\n";
    $message .= 'Whole query: ' . $query;
    die($message);
}

while ($row = mysql_fetch_assoc($result)) {
  echo '<tr>';
  echo '<td class="list">' . $row['scope'] . '</td>';
  echo '<td class="list">' . $row['institution'] . '</td>';
  echo '<td class="list">' . $row['module'] . '</td>';
  echo '<td class="list">' . $row['start_date'] . '</td>';
  echo '<td class="list">' . $row['end_date'] . '</td>';
  echo '<td class="list"><form method="POST" action="register_for_audit.php" class="list"><input type="hidden" name="collection_id" value="' . $row['collection_id'] . '" /><input type="hidden" name="operator_code" value="' . $operator_code . '" /><input type="hidden" name="operator_id" value="' . $operator_id . '" /><input class="list" name="comments" value="" size=15/> <button type="submit" class="list">Register</button></form></td>';
  echo '</tr>';
}

mysql_free_result($result);

?>
</table>


</body>
</html>