<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
    "http://www.w3.org/TR/html4/loose.dtd">
<?php

require_once('settings.php');

$link = mysql_connect($db_server,$db_username,$db_password);
if (! mysql_select_db($db_database)) {
  die(mysql_error());
}

?>
<html>
<head>
   	<style type="text/css" title="currentStyle" media="screen">
		@import "official_blue.css";
	</style>
  <title>Software Quality Assurance</title>
</head>
<body>


<div class="sqamenu"><span class="sqamenusel">Welcome</span> <span class="sqamenu"><a href="checked_out.php">Participate</a></span> <span class="sqamenu"><a href="admin_audits.php">Administrate</a></span></div>

<h1 class="sqa">Software Quality Assurance</h1>
  <div class="sqah1">&nbsp;</div>

<h2 class="sqa">Welcome</h2>
<div class="sqap">Welcome to the ANZSNM software quality assurance site.
This site allows you to participate and administrate web based software audits. Each audit requies participants to download a dataset and process it with their workstation software.</div>

<h2 class="sqa">Participate</h2>
<div class="sqap">You can participate by registering with a software audit.
You will be able to measure the performance of your software in comparison to other participants in the audit. </div>

<div class="sqap">When you participate you will receive an <em>operator code</em>. This is a code which is unique to you. You can use this code across multiple audits.</div>

<div class="sqap">You may be decide to participate in an official software audit, in which case you will need to contact the person running the audit and give them your operator code so that your results are validated and included.</div>

	  <div class="sqap">Each audit is identified by its <em>scope</em>, <em>insitution</em> and <em>dataset</em>. In some cases the scope may be restricted to certain countries, regions, states or even individual hospitals. You may participate in any audit, regardless of the scope. However, the person running the audit has the authority to either include or exclude your results.</div>

<h2 class="sqa">Administrate</h2>

<div class="sqap">You can initiate a completely new software audit by lodging a request for a new audit. You will need to choose from a list datasets below.</div>

<table class="list" width="400">
<tr>
<th class="list">Dataset</th>
<th class="list">Description</th>
</tr>


<?php

$query = 'SELECT title, description, info_url FROM module WHERE visible=1';
$result = mysql_query($query);
if (!$result) {
    $message  = 'Invalid query: ' . mysql_error() . "\n";
    $message .= 'Whole query: ' . $query;
    die($message);
}

while ($row = mysql_fetch_assoc($result)) {
  echo '<tr>';
  if ( $row['info_url'] != '') {
    echo '<td class="list"><a href="' . $row['info_url'] . '">' . $row['title'] . '</a></td>';
  } else {
    echo '<td class="list">' . $row['title'] . '</td>';
  }
  echo '<td class="list">' . $row['description'] . '</td>';
  echo '</tr>';
}

mysql_free_result($result);

?>
</table>

<div class="sqap">You can choose the scope of your collection and then invite other people to participate. You may need to demonstrate that you will handle the audit data responsibly</div>

<div class="sqap">Please avoid requesting new audits which have the same scope as existing audits. Here is a list of existing web based software audits.
</div>


<table class="list" width="80%">
<tr>
<th class="list">Scope</th>
<th class="list">Institution</th>
<th class="list">Dataset</th>
<th class="list">Start Date</th>
<th class="list">End Date</th>
</tr>

<?php

$query = 'SELECT collection.collection_id, collection.scope, DATE_FORMAT(collection.start_date,"%e/%c/%Y") AS start_date, DATE_FORMAT(collection.end_date,"%e/%c/%Y") AS end_date, institution.title AS institution, institution.department AS department, module.title AS module FROM collection, institution, module WHERE collection.module_id = module.module_id AND collection.institution_id = institution.institution_id AND collection.visible=1';
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

<div class="sqap">Results and reports are not publicly available.</div>

</body>
</html>
