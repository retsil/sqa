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

<h2 class="sqa">Operator Code</h2>

  <form action="checked_out.php"><span class="sqaopf">Operator code: <span class="sqaop"><?php echo $operator_code; ?></span>
	  <button type="submit">Check out</button></span></form>

<p>On first using this online audit tool, register below for one of the available audits.  This will open a session, which you can come back to at a later date (without having to re-register).</p>

<h2 class="sqa">Training/Audits</h2>


<?php

function list_collections($is_audit,$operator_id,$operator_code) {
    $collection_count = array();

//    echo "OperatorId=" . $operator_id;
    $query = sprintf('SELECT collection.collection_id AS collection_id, count(session.session_id) AS session_count FROM collection LEFT JOIN session ON collection.collection_id=session.collection_id WHERE session.operator_id=%d group by collection.collection_id',$operator_id);

    $result = mysql_query($query);
    if (!$result) {
        $message  = 'Invalid query: ' . mysql_error() . "\n";
        $message .= 'Whole query: ' . $query;
        die($message);
    }

    while ($row = mysql_fetch_assoc($result)) {
        
        $collection_count[$row['collection_id']] = $row['session_count'];
 //       echo '[ collection_id=' . $row['collection_id'] . ', session_count=' . $row['session_count'] .  ']';
    }

    $query = 'SELECT collection.collection_id, collection.scope, collection.allow_multiple_sessions, DATE_FORMAT(collection.start_date,"%e/%c/%Y") AS start_date, DATE_FORMAT(collection.end_date,"%e/%c/%Y") AS end_date, institution.title AS institution, module.title AS module FROM collection, institution, module WHERE collection.module_id = module.module_id AND collection.institution_id = institution.institution_id AND collection.visible=1 AND collection.is_audit=' . $is_audit;
    $result = mysql_query($query);
    if (!$result) {
        $message  = 'Invalid query: ' . mysql_error() . "\n";
        $message .= 'Whole query: ' . $query;
        die($message);
    }

    if (mysql_num_rows($result) == 0) {
      echo '<tr><td style="text-align: center; font-style: italic;" colspan=7>There are no collections currently open.</td></tr>';
     }
    while ($row = mysql_fetch_assoc($result)) {


      echo '<tr>';
      echo '<td class="list">' . $row['scope'] . '</td>';
      echo '<td class="list">' . $row['institution'] . '</td>';
      echo '<td class="list">' . $row['module'] . '</td>';
      echo '<td class="list">' . $row['start_date'] . '</td>';
      echo '<td class="list">' . $row['end_date'] . '</td>';
 //     echo "[ allow_multiple_sessions=" . $row['allow_multiple_sessions'] . ", collection_id=" . $row['collection_id'] . ", collection_count=" . $collection_count[$row['collection_id']] . "]";
      if (($row['allow_multiple_sessions']==1) or ($collection_count[$row['collection_id']]==0)) {

          echo '<td class="list" colspan="2" style="text-align: right;"><form method="POST" action="register_for_audit.php" class="list"><input type="hidden" name="collection_id" value="' . $row['collection_id'] . '" /><input type="hidden" name="operator_code" value="' . $operator_code . '" /><input type="hidden" name="operator_id" value="' . $operator_id . '" /><input class="list" name="comments" value="First attempt" style="width: 200px;" size="20"/> <button type="submit" class="list" style="width: 100px;">Register</button></form></td>';
        } else {
            echo '<td class="list" colspan="2">Already registered. Only one session allowed.</td>';
        }    
      echo '</tr>';
    }
    mysql_free_result($result);
 }

?>

<p>A list of current <b>training results</b>. Register to enter your own results and review it in comparison to the training results.</p>
<table class="list" width="80%">
<tr>
<th class="list">Scope</th>
<th class="list">Institution</th>
<th class="list">Dataset</th>
<th class="list">Start Date</th>
<th class="list">End Date</th>
<th class="list" style="width: 210px;">Session Comments</th>
<th class="list" style="width: 100px;">Register</th>
</tr>
<?php list_collections(0,$operator_id,$operator_code); ?>
</table>

<p>A list of current <b>offical audits</b>. Register to enter your own results, but you will not be able to review your performance until after the audit has completed.</p>
<table class="list" width="80%">
<tr>
<th class="list">Scope</th>
<th class="list">Institution</th>
<th class="list">Dataset</th>
<th class="list">Start Date</th>
<th class="list">End Date</th>
<th class="list" style="width: 210px;">Session Comments</th>
<th class="list" style="width: 100px;">Register</th>
</tr>
<?php list_collections(1,$operator_id,$operator_code); ?>
</table>


<h2 class="sqa">Sessions</h2>

<p>A list of currently active sessions for entering audit or training purposes.</p>

<table class="list" width="80%">
<tr>
<th class="list">Type</th>
<th class="list">Scope</th>
<th class="list">Institution</th>
<th class="list">Dataset</th>
<th class="list">Opened On</th>
<th class="list">End date</th> <!-- or verified -->
<th class="list">Comments</th>
<th class="list" colspan=3></th>
</tr>

<?php
  $query = sprintf('SELECT session.session_code, session.comments, session.is_verified, collection.allow_report,  collection.allow_user_lock, collection.is_audit, collection.scope, DATE_FORMAT(session.registered,"%%e/%%c/%%Y") AS registered_date, DATE_FORMAT(collection.end_date,"%%e/%%c/%%Y") AS end_date, institution.title AS institution, module.title AS module, module.data_url, module.report_url FROM collection, institution, module, session WHERE collection.module_id = module.module_id AND collection.institution_id = institution.institution_id AND session.collection_id=collection.collection_id AND session.operator_id=%d',$operator_id);
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
  if ( $row['is_audit'] ) {
    echo '<td class="list">Audit</td>';
  } else {
    echo '<td class="list">Training</td>';
  }
  echo '<td class="list">' . $row['scope'] . '</td>';
  echo '<td class="list">' . $row['institution'] . '</td>';
  echo '<td class="list">' . $row['module'] . '</td>';
  echo '<td class="list">' . $row['registered_date'] . '</td>';
  if ( $row['is_verified'] ) {
    echo '<td class="list">Locked</td>';
  } else {
    echo '<td class="list">' . $row['end_date'] . '</td>';
  }
  echo '<td class="list">' . htmlspecialchars($row['comments']) . '</td>';

    if (! $row['is_verified'] ) {
        echo '<td class="list"><form method="get" action="' . $row['data_url'] . '" class="list"><input type="hidden" name="session_code" value="' . $row['session_code'] . '" /><button type="submit" class="list">Enter data</button></form></td>';
        if ($row['allow_user_lock'] ) {
            echo '<td class="list"><form method="get"  class="list" action="verify_my_session.php"><input type="hidden" name="session_code" value="' . $row['session_code'] . '"<input type="submit" value="Complete"></form></td>';
        }
    } else {
        echo '<td></td><td></td>';
    }
    if ($row['allow_report']) {
        echo '<td class="list"><form method="get" action="' . $row['report_url'] . '" class="list"><input type="hidden" name="session_code" value="' . $row['session_code'] . '" /><button type="submit" class="list">View results</button></form></td>';
    } else {
        echo '<td></td>';
    }
  echo '</tr>';
}

mysql_free_result($result);

?>
</table>


</body>
</html>