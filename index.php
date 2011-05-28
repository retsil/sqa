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




<h2 class="sqa">Introduction</h2>
<div class="sqap">
The ANZSNM software quality assurance site allows users to:
<ul>
<li>participate in an existing software audit
<li>setup and administrate their own audit or
<li>test their results against a training dataset
</ul>
</div>

<div class="sqap">
Each audit is identified by its scope, institution and dataset. In some cases the scope may be restricted. For example, to Countries, States, regions, hospitals etc.. You may participate in any audit, regardless of the scope. However, the person running the audit has the authority to either include or exclude your results.
</div>



<h2 class="sqa">

  Datasets :
<?php

$query = 'SELECT title, description, info_url FROM module WHERE visible=1';
$result = mysql_query($query);
if (!$result) {
    $message  = 'Invalid query: ' . mysql_error() . "\n";
    $message .= 'Whole query: ' . $query;
    die($message);
}

while ($row = mysql_fetch_assoc($result)) {
  if ( $row['info_url'] != '') {
    echo '<a href="' . $row['info_url'] . '">' . $row['title'] . '</a>';
  } else {
    echo '' . $row['title'] . '';
  }
  echo ' | ';
  //  echo '<td class="list">' . $row['description'] . '</td>';
}

mysql_free_result($result);

?>
</h2>


<div class="sqap">You will need to download one of the datasets before you can participate</div>

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


<div style="width: 47%; float: left; padding-right: 1em; border-right: thin solid rgb(200,200,255);">

<h2 class="sqa">Participate</h2>
<div class="sqap">

To participate in an audit/training session, firstly download the required dataset and process it using your clinical workstation software.

Before entering the results online, register with the appropriate audit.  You will receive a unique operator code which can be used across multiple audits.
</div>

<div class="sqap">
<dl class="sqap">
<dt class="sqap">Official Audits</dt>
<dd class="sqap">
To participate in an official audit contact the person running the audit and give them your operator code so that your results are validated and included (this can be done before or after data entry). 
</dd>

<dt class="sqap">Training</dt>
<dd class="sqap">
  For the training datasets, participants are provided with instant reports comparing their data with the &quot;running average&quot; currently on the database (obtained from a previous audit). 
</dd>
</div>

</div>

<div style="width: 47%; float: right;">

<h2 class="sqa">Administrate</h2>

<div class="sqap">
You can initiate a new software audit (using the available online datasets) by lodging an online request. You define the scope of the collection and invite people to participate.

<dl class="sqap">
<dt class="sqap">NOTE:</dt>
<dd class="sqap">
<ol>
<li>avoid requesting new audits which have the same scope as existing audits
<li>Individual results in audit mode are not publicly available on this site and the dissemination of results remains the responsibility of the administrator of the audit
</ol>
</dd>
</dl>
</div>

</div>

<br clear="both">



<!--


<h2 class="sqa">Welcome</h2>
<div class="sqap">Welcome to the ANZSNM software quality assurance site. This site allows you to participate in an existing, or administrate a new web based software audits. Each audit requires participants to download a dataset and process it using their workstation software.</div>

<h2 class="sqa">Participate</h2>
<div class="sqap">You participate by registering with a software audit, when you will receive a unique <em>operator code</em>. You can use this code across multiple audits. The audits can be used for (1) official audits or (2) training. 
</div>

<dl class="sqap">
<dt class="sqap">Official Audits</dt>
<dd class="sqap">To participate in an official software audit you need to contact the person running the audit and give them your operator code so that your results are validated and included (this can be done before or after data entry).</dd>
<dt class="sqap">Training</dt>
<dd class="sqap">
You can use the training datasets to compare your results to the mean value stored in the database for each patient study (obtained from a previous audit).</dd>
</dl>

	  <div class="sqap">Each audit is identified by its <em>scope, institution and dataset</em>. In some cases the scope may be restricted to certain countries, regions, states or even individual hospitals. You may participate in any audit, regardless of the scope. However, the person running the audit has the authority to either include or exclude your results.</div>

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

<div class="sqap">You can choose the scope of your collection and then invite other people to participate. You may need to demonstrate that you will handle the audit data responsibly.</div>
<div class="sqap">Please avoid requesting new audits which have the same scope as existing audits. Here is a list of existing web based software audits. </div>


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

<div class="sqap">Individual results are not publicly available on this site and the dissemination of results remains the responsibility of the administrator of the audit.</div>

-->

</body>
</html>
