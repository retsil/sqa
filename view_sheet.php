<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
    "http://www.w3.org/TR/html4/loose.dtd">
<?php

require_once('settings.php');

require_once('moodle.inc');

?>


<html>
<head>
    	<style type="text/css" title="currentStyle" media="screen">
		@import "official_blue.css";
	</style>
  <title>View sheet</title>
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
   die("Not logged in");
 }


$link = mysql_connect($db_server,$db_username,$db_password);
if (! mysql_select_db($db_database)) {
  die(mysql_error());
}

if (empty( $sheet_id)) {
  if (preg_match('/^\d+$/', $_POST['sheet_id'])) {
    
    $query = sprintf('SELECT sheet_id FROM module_sheets WHERE sheet_id=%s AND moodle_id=%s',
		     $_POST['sheet_id'],
		     $moodle_id);
    $result = mysql_query($query);
    if (!$result) {
      $message  = 'Invalid query: ' . mysql_error() . "\n";
      $message .= 'Whole query: ' . $query;
      die($message);
    }
    if ($row = mysql_fetch_assoc($result)) {
      $sheet_id =  $row['sheet_id'];
    } else {
      $message = 'Unknown sheet id ' . $_GET['sheet_id'] . " for user " . $moodle_id;
      die($message);
    }      
  } else {
    $message = 'Invalid sheet id';
    die($message);
    return;
  }
 }

?>

<h2 class="sqa">Administrate audits</h2>

<div class="sqap"><a href="admin_audits.php">Expand</a></div>

<h2 class="sqa">Exported spreadsheets</h2>

  <div class="sqap">The spreadsheets contain copies of the audit data in a form which is suitable for downloading and uploading as a comma delimited file (.csv).</div>

<table class="list" width="50%">
<tr>
<th class="list">Name</th>
<th class="list">Rows</th>
<th class="list">Cols</th>
<th class="list">Sheet type</th>
<th class="list">Download CSV</th>
<th class="list">View</th>
<th class="list">Delete</th>
</tr>

<?php
														  #COUNT(column_id) 
    $query = sprintf('SELECT sheet_id, module_sheets.title, number_of_rows, number_of_columns, type_name FROM module_sheets LEFT JOIN module_sheet_type ON module_sheets.type_id=module_sheet_type.type_id WHERE module_sheets.moodle_id=%s AND sheet_id=%s',$moodle_id,$sheet_id);

# $query = 'SELECT sheet_id, title, number_of_rows FROM module_sheets';


$result = mysql_query($query);
if (!$result) {
  $message  = 'Invalid query: ' . mysql_error() . "\n";
  $message .= 'Whole query: ' . $query;
  die($message);
 }

while ($row = mysql_fetch_assoc($result)) {
  echo '<tr>';
  echo '<td class="list">' . $row['title'] . '</td>';
  echo '<td class="list">' . $row['number_of_rows']  . '</td>';
  echo '<td class="list">' . $row['number_of_columns']  . '</td>';
  echo '<td class="list">' . $row['type_name']  . '</td>';
  echo '<td class="list"><form action="download_sheet.php" method="POST"><button type="submit">Download</button>';
  echo '<input type="hidden" name="sheet_id" value="' . $row['sheet_id']  . '"></form></td>';
  echo '<td class="list"><form action="view_sheet.php" method="POST"><button type="submit">View</button>';
  echo '<input type="hidden" name="sheet_id" value="' . $row['sheet_id']  . '"></form></td>';
  echo '<td class="list"><form action="delete_sheet.php" method="POST"><button type="submit">Delete</button>';
  echo '<input type="hidden" name="sheet_id" value="' . $row['sheet_id']  . '"></form></td>';
  echo '</tr>';
 
}
?>

</table>


<?php

#echo $sheet_id;

   $query = sprintf('SELECT title, module_sheets.type_id, type_name, number_of_rows, number_of_columns FROM module_sheets LEFT JOIN module_sheet_type ON module_sheets.type_id=module_sheet_type.type_id WHERE sheet_id=%s',$sheet_id);


    $result = mysql_query($query);
    if (!$result) {
      $message  = 'Invalid query: ' . mysql_error() . "\n";
      $message .= 'Whole query: ' . $query;
      die($message);
    }
 if ($row = mysql_fetch_assoc($result)) {
   $number_of_columns =  $row['number_of_columns'];
   $number_of_rows =  $row['number_of_rows'];
   $title =  $row['title'];
   $type_name =  $row['type_name'];
   $type_id =  $row['type_id'];
 } else { 
   die('Spreadsheet not found');
 }

# echo '<div class="sqap">Spreadsheet name: ' . $title . '</div>';

echo '<table class="list"><tr>';

$query = sprintf('SELECT title FROM module_columns WHERE type_id=%s',$type_id);

if ($result = mysql_query($query)) {
  for ($c=0; $c < $number_of_columns; $c++) {
    if ($row = mysql_fetch_assoc($result)) {
      $title = $row['title'];
    } else {
      $title = 'Col ' . sprintf('%d',$c);
    }
    echo '<th class="list">' . $title . '</th>';
  }
 } else {
    die('Spreadsheet columns to found');
  }
 
echo '</tr>';

for ($r=0; $r < $number_of_rows; $r++) {
  echo "<tr>";
  for ($c=0; $c < $number_of_columns; $c++) {
    $query = sprintf('SELECT value FROM module_data WHERE sheet_id=%s AND row_id=%s AND column_id=%s',$sheet_id,$r,$c);
    if ($result = mysql_query($query)) {
      $row = mysql_fetch_assoc($result);
      echo '<td class="list">' . htmlspecialchars($row['value']) . '</td>';
    }
  }
  echo "</tr>";
 }
?>