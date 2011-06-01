<?php

require_once('settings.php');

require_once('moodle.inc');

 if (isloggedin() && $USER->username != 'guest') {
     $moodle_id = $USER->id;
 } else {
   die("Not logged in");
 }

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
    "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
   	<style type="text/css" title="currentStyle" media="screen">
		@import "official_blue.css";
	</style>  
  <title>List all collections</title>
</head>
<body>
<div class="sqamenu"><span class="sqamenu"><a href="index.php">Welcome</a></span> <span class="sqamenu"><a href="checked_out.php">Participate</a></span> <span class="sqamenusel">Administrate</span></div>

<h1 class="sqa">Software Quality Assurance</h1>
  <div class="sqah1">&nbsp;</div>



  <h2 class="sqa"><a href="http://anzsnm-test.barnett.id.au/moodle/">Moodle</a> login</h2>

 
<?php 

   if (isloggedin() && $USER->username != 'guest') {
     $moodle_full_name = $USER->firstname . " " . $USER->lastname;
     $moodle_id = $USER->id;
     $moodle_email = $USER->email;
     echo "<p><span class=\"sqaopf\">You are currently logged in as:";
     echo "<span class=\"sqaop\">" . $moodle_full_name . "</span>";
     echo "</span> <a href=\"http://anzsnm-test.barnett.id.au/moodle/login/logout.php\">Logout</a></p>";
#	echo "Your ID is: " . $USER->id . "</p>";
   } else {
     echo "<p>Login below and hit <a href=\"admin_audits.php\">refresh</a></p>";
     echo "<iframe width=100% height=60% src=\"http://anzsnm-test.barnett.id.au/moodle/login/index.php\"></iframe>";
     echo "</body></html>";
     return;
   }


$link = mysql_connect($db_server,$db_username,$db_password);
if (! mysql_select_db($db_database)) {
  die(mysql_error());
}

if (empty( $collection_id)) {
  if (preg_match('/^\d+$/', $_GET['collection_id'])) {
    
    $query = sprintf('SELECT collection_id FROM collection LEFT JOIN institution_auth ON institution_auth.institution_id = collection.institution_id  WHERE collection_id=%d AND moodle_id=%s',
		     $_GET['collection_id'],
		     $moodle_id);
    $result = mysql_query($query);
    if (!$result) {
      $message  = 'Invalid query: ' . mysql_error() . "\n";
      $message .= 'Whole query: ' . $query;
      die($message);
    }
    if ($row = mysql_fetch_assoc($result)) {
      $collection_id =  $row['collection_id'];
    } else {
      $password_error = 'Invalid login';
      require('admin_audits.php');
      return;
    }      
  } else {
    $password_error = 'Invalid login';
    require('admin_audits.php');
    return;
  }
 }
?>



<h2 class="sqa">Administrate Sessions</h2>

<div class="sqap">The current audit</div>


<table class="list" width="80%">
<tr>
<th class="list">Scope</th>
<th class="list">Institution</th>
<th class="list">Dataset</th>
<th class="list">Start Date</th>
<th class="list">End Date</th>
</tr>

<?php
  $query = sprintf('SELECT collection.collection_id, collection.scope, DATE_FORMAT(collection.start_date,"%%e/%%c/%%Y") AS start_date, DATE_FORMAT(collection.end_date,"%%e/%%c/%%Y") AS end_date, institution.title AS institution, institution.department AS department, module.title AS module FROM collection, institution, module WHERE collection.module_id = module.module_id AND collection.institution_id = institution.institution_id AND collection.collection_id=%d',
		 $collection_id);

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
  echo '</tr>';
}

mysql_free_result($result);

?>
</table>

<div class="sqap"><a href="admin_audits.php">Other audits</a></div>

<div class="sqap">A list of all sessions for this audit</div>

<table class="list" width="80%">
<tr>
<th class="list">Operator Code</th>
<th class="list">Opened On</th>
<th class="list">Comments</th>
<th class="list">Locked</th>
<th class="list"></th>
</tr>

<?php
  $query = sprintf('SELECT session.comments, session.is_verified, DATE_FORMAT(session.registered,"%%e/%%c/%%Y") AS registered_date, operator.operator_code, session_id FROM operator, session WHERE session.operator_id=operator.operator_id AND session.collection_id=%d',$collection_id);
$result = mysql_query($query);
if (!$result) {
    $message  = 'Invalid query: ' . mysql_error() . "\n";
    $message .= 'Whole query: ' . $query;
    die($message);
}

while ($row = mysql_fetch_assoc($result)) {
  echo '<tr>';
  echo '<td class="list">';
  echo '<form action="checked_in.php" class="list">';
  echo '<input type="hidden" size=6 maxlength=6 name="operator_code" value="' . $row['operator_code'] . '" />';
  echo  '<button type="submit">Check in</button> ' .  $row['operator_code'] .  '</form>';
  echo '</td>';
  echo '<td class="list">' . $row['registered_date'] . '</td>';
  echo '<td class="list">' . htmlspecialchars($row['comments']) . '</td>';
  echo '<td class="list"><form method="post" action="verify_session.php" class="list"><input type="hidden" name="password" value="' . $password . '" /><input type="hidden" name="collection_id" value="' . $collection_id . '" /><input type="hidden" name=session_id value="' . $row['session_id'] . '" />';
  if ( $row['is_verified'] ) {
    echo '<input type="hidden" name="verify" value="0" />';
    echo '<button type="submit" class="list">Remove lock</button>';
  } else {
    echo '<input type="hidden" name="verify" value="1" />';
    echo '<button type="submit" class="list">Lock session</button>';
  }
  echo '</form></td>';
  echo '</tr>';
}

mysql_free_result($result);

?>
</table>