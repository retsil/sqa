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
  <title>Administrate audits</title>
</head>
<body>


<div class="sqamenu"><span class="sqamenu"><a href="index.php">Welcome</a></span> <span class="sqamenu"><a href="checked_out.php">Participate</a></span> <span class="sqamenusel">Administrate</span></div>

<h1 class="sqa">Software Quality Assurance</h1>
  <div class="sqah1">&nbsp;</div>

  <h2 class="sqa"><?php echo "<a href=\"" . $CFG->wwwroot . "\">"; ?>Moodle</a> login</h2>


<?php 

   if (isloggedin() && $USER->username != 'guest') {
     $moodle_full_name = $USER->firstname . " " . $USER->lastname;
     $moodle_id = $USER->id;
     $moodle_email = $USER->email;
     echo "<p><span class=\"sqaopf\">You are currently logged in as:";
     echo "<span class=\"sqaop\">" . $moodle_full_name . "</span>";
     echo "</span> <a href=\"" . $CFG->wwwroot . "/login/logout.php\">Logout</a></p>";
#	echo "Your ID is: " . $USER->id . "</p>";
   } else {
     echo "<p>Please follow this <a href='" . $CFG->wwwroot . "/mod/resource/view.php?id=83'>link</a> and log into moodle</p>";
#     echo "<p>Login below and hit <a href=\"admin_audits.php\">refresh</a></p>";
#     echo "<iframe width=100% height=60% src=\"" . $CFG->wwwroot . "/login/index.php\"></iframe>";
     echo "</body></html>";
     return;
   }

$link = mysql_connect($db_server,$db_username,$db_password);
if (! mysql_select_db($db_database)) {
  die(mysql_error());
}

?>

<h2 class="sqa">Administrate audits</h2>

<div class="sqap">A list of web based software audits which you manage. Open the audit to add and
remove sessions. Export the audit as a table to access the entered data.</div>

<?php
  if (! empty($password_error)) {
  echo '<span class="sqaerr">' . $password_error . '</span>';
}
?>

<table class="list" width="80%">
<tr>
<th class="list">Scope</th>
<th class="list">Institution</th>
<th class="list">Dataset</th>
<th class="list">Start Date</th>
<th class="list">End Date</th>
<th class="list">Visible</th>
<th class="list">Open sessions</th>
<th class="list">Export as sheets</th>

<?php

$query = 'SELECT collection.collection_id, collection.scope, DATE_FORMAT(collection.start_date,"%e/%c/%Y") AS start_date, DATE_FORMAT(collection.end_date,"%e/%c/%Y") AS end_date, institution.title AS institution, institution.department AS department, module.title AS module, module.module_id, collection.visible, institution_auth.visible_priv FROM collection LEFT JOIN institution ON collection.institution_id = institution.institution_id LEFT JOIN institution_auth ON institution_auth.institution_id = institution.institution_id LEFT JOIN module ON collection.module_id = module.module_id WHERE institution_auth.manage_priv=1 AND institution_auth.moodle_id = ' . $moodle_id;
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
  echo '<td class="list">';
  if ( $row['visible_priv'] ) {
    echo '<form action="modify_audit.php" method="POST"><input type="hidden" name="collection_id" value="' .  $row['collection_id'] . '"/>';
    if ($row['visible'] == 1) { echo '<input type="hidden" name="visible" value="0"/><button type="submit" class="list">Hide</button>'; }
    else  { echo '<input type="hidden" name="visible" value="1"/><button type="submit" class="list">Show</button>'; }
    echo '</form></td>';
  } else { 
    if ($row['visible'] == 1) { echo 'Showing'; } else { echo 'Hidden'; }
  }
  echo '</td>';
  echo '<td class="list"><form action="admin_sessions.php"><input type="hidden" name="collection_id" value="' . $row['collection_id'] . '"/><button type="submit" class="list">Open</button></form></td>';
  echo '<td><form action="export_audit.php" method="POST"><input type="hidden" name="collection_id" value="'. $row['collection_id'] . '">';
  echo 'Name: <input name="sheet_name" type="text"><button type="submit" class="list">Export</button></form></td>';
  $module_id = $row['module_id'];
  
   echo '</tr>';
}

mysql_free_result($result);

?>
</table>

<?php 

$query = sprintf('SELECT institution.institution_id, title, department FROM institution_auth JOIN institution ON institution_auth.institution_id=institution.institution_id WHERE moodle_id=%d AND create_priv=1',$moodle_id);

$result = mysql_query($query);
if (!$result) {
    $message  = 'Invalid query: ' . mysql_error() . "\n";
    $message .= 'Whole query: ' . $query;
    die($message);
}

$institution_html = '';

while ($row = mysql_fetch_assoc($result)) {
  
  $institution_html .= '<option value="' . $row['institution_id'] . '" >' . $row['title'] . ' - ' .  $row['department'] . '</option>';
}

if ($institution_html == '') { echo '<!--'; }

?>

<FORM action="create_audit.php" METHOD="POST">

<FIELDSET class="sqaf">
  <LEGEND class="sqaf">Create Audit</LEGEND>
  <DIV class="sqaf">
	<SPAN class="sqaf">Scope:</SPAN>
	<INPUT name="scope" type="text" value=""  size="30" tabindex="5"/>
  </DIV>
  <DIV class="sqaf">
	<SPAN class="sqaf">Institution Name:</SPAN>
	<SELECT name="institution_id"  size="1">
  <?php echo $institution_html; ?>
        </SELECT>
  </DIV>
  <DIV class="sqaf">
	<SPAN class="sqaf">Start Date:</SPAN>
	<INPUT name="start_dateDD" type="text" maxlength=2 size=2 tabindex="6"/>/
	<INPUT name="start_dateMM" type="text" maxlength=2 size=2 tabindex="7"/>/
	<INPUT name="start_dateYYYY" type="text" maxlength=4 size=4 tabindex="8"/>
  </DIV>
  <DIV class="sqaf">
	<SPAN class="sqaf">End Date:</SPAN>
	<INPUT name="end_dateDD" type="text" maxlength=2 size=2 tabindex="9"/>/
	<INPUT name="end_dateMM" type="text" maxlength=2 size=2 tabindex="10"/>/
	<INPUT name="end_dateYYYY" type="text" maxlength=4 size=4 tabindex="11"/>
  </DIV>
  <DIV class="sqaf">
	<SPAN class="sqaf">Dataset:</SPAN>
	<SELECT name="module_id" tabindex="12" >
<?php
  $query = 'SELECT module_id, title FROM module';


$result = mysql_query($query);
if (!$result) {
  $message  = 'Invalid query: ' . mysql_error() . "\n";
  $message .= 'Whole query: ' . $query;
  die($message);
 }

while ($row = mysql_fetch_assoc($result)) {
  
  echo '<option value="' . $row['module_id'] . '" >' . $row['title'] . '</option>';
}
?>
	</SELECT>
  </DIV>

</FIELDSET>
<INPUT type="submit" value="Create Audit" tabindex="14"/>		 


</FORM>

<?php

if ($institution_html == '') { echo '-->'; }
if ($institution_html != '') { echo '<!--'; }
?>

<h2 class="sqa">Request audit</h2>

  <div class="sqap">You do not have the authority to add new software audits. 
   Please send an email to <a href="mailto:retsil@iinet.net.au">retsil@iinet.net.au</a> providing your Full Name, Email Address, Phone Number, Department Name, Institution Name.
   You can either specify the details of your intended audit or provide a reason why you want to start your own audits as opposed to adding data to existing audits.
 </div>

<?php
if ($institution_html != '') { echo '-->'; }
?>

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
    $query = sprintf('SELECT sheet_id, module_sheets.title, number_of_rows, number_of_columns, type_name FROM module_sheets LEFT JOIN module_sheet_type ON module_sheets.type_id=module_sheet_type.type_id WHERE module_sheets.moodle_id=%d',$moodle_id);

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





</body>
</html>
