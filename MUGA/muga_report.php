<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<?php

require_once('../settings.php');

$link = mysql_connect($db_server,$db_username,$db_password);
if (! mysql_select_db($db_database)) {
  die(mysql_error());
}

require_once('../session.inc.php');

$session_id = check_session(-1);

?>

<html>
<head>
   	<style type="text/css" title="currentStyle" media="screen">
		@import "../official_blue.css";
	</style>
  <title>Data</title>
</head>
<body>

<?php

function collection_stats($table,$column,$values,$quot,$collection_id) {
	  foreach ($values as $key => $value) { 
	    if ($quot) {
	      $query = sprintf('SELECT COUNT(%s) FROM session, %s WHERE %s.session_id=session.session_id AND session.collection_id=%d AND %s.%s="%s"',$column,$table,$table,$collection_id,$table,$column,mysql_real_escape_string($key));
	    } else {
	      $query = sprintf('SELECT COUNT(%s) FROM session, %s WHERE %s.session_id=session.session_id AND session.collection_id=%d AND %s.%s=%s',$column,$table,$table,$collection_id,$table,$column,$key);
	    }
	    $result = my_query($query);
	    $row = mysql_fetch_assoc($result);
	    $stats[$key] = $row[sprintf('COUNT(%s)',$column)];
	    //echo $row[sprintf('COUNT(%s)',$column)];
	  }
	  return $stats;	 
}

function tabulate_stats($values,$stats) {


  echo '<table class="results">';
    echo '<tr>';
    echo '<td></td>';
    echo '<td class="results">Count</td>';
    echo '<td class="results">Percent</td>';
    echo '</tr>';

    $total = 0;
  foreach ($values as $key => $value) { 
    $total += $stats[$key];
  }
  foreach ($values as $key => $value) { 
    $rel_stats[$key] = 100*$stats[$key]/$total;
  }

  foreach ($values as $key => $value) { 
    echo '<tr>';
    echo '<th class="results">' . $value . '</th>';
    echo '<td class="results">' . $stats[$key] . '</td>';
    echo '<td class="results">' . sprintf("%d%%",$rel_stats[$key]) . '</td>';
    echo '</tr>';
  }
  echo '</table>';

  $chart_data = implode(',',array_values($rel_stats));
  $chart_labels = implode('|',array_values($values));

  echo '<img src="http://chart.apis.google.com/chart?cht=p3&amp;chd=t:' . $chart_data . '&amp;chs=250x100&amp;chl=' . $chart_labels . '"  alt="Google Chart" />';

}

$collection_id = collection_id($session_id);

?>

<div class="sqamenu"><span class="sqamenu"><a href="../index.php">Welcome</a></span> <span class="sqamenu"><a href="../checked_out.php">Participate</a></span> <span class="sqamenusel">MUGA</span></div>

<h1 class="sqa">Software Quality Assurance</h1>
  <div class="sqah1">&nbsp;</div>


  <FIELDSET class="sqaf">
  <LEGEND class="sqaf">How long have you been processing MUGA scans?</LEGEND>
<?php
$values = array('0' => 'less than 6 months', '6' => '6 months to 1 year', '12' => 'longer than 1 year');
$stats = collection_stats('muga_session','operator_experience_in_months',$values,false,$collection_id);
tabulate_stats($values,$stats);
?>
  </FIELDSET>

 <FIELDSET class="sqaf">
  <LEGEND class="sqaf">Type of software:</LEGEND>
<?php
  $values =array('commercial' => 'commercial', 'inhouse' => 'in-house', 'other' => 'other');
$stats = collection_stats('muga_session','type_of_software',$values,true,$collection_id);
tabulate_stats($values,$stats);
?>
  </FIELDSET>

 <FIELDSET class="sqaf">
  <LEGEND class="sqaf">How many scans do you process per month?</LEGEND>
<?php
					   $values =  array('1' => ' 1 - 10', '11' => '11 - 30', '31' => '31+');
$stats = collection_stats('muga_session','operator_frequency_per_month',$values,false,$collection_id);
tabulate_stats($values,$stats);
?>
  </FIELDSET>


 <FIELDSET class="sqaf">
  <LEGEND class="sqaf">Number of frames actually analysed:</LEGEND>
<?php
  $values = array('16' => '16', '32' => '32');
$stats = collection_stats('muga_session','number_of_frames_actually_analysed',$values,false,$collection_id);
tabulate_stats($values,$stats);
?>
  </FIELDSET>


 <FIELDSET class="sqaf">
  <LEGEND class="sqaf">Region of interest method:</LEGEND>
<?php
  $values = array('0' => 'manual', '1' => 'semiautomatic', '2' => 'fully-automatic');
$stats = collection_stats('muga_session','region_of_interest_method',$values,false,$collection_id);
tabulate_stats($values,$stats);
?>
  </FIELDSET>


 <FIELDSET class="sqaf">
  <LEGEND class="sqaf">Were separate systole and diastole ROIs used?</LEGEND>
<?php
  $values =  array('0' => 'no', '1' => 'yes', '2' => 'don&#39;t know');
$stats = collection_stats('muga_session','separate_systole_and_diastole_ROIs_used',$values,false,$collection_id);
tabulate_stats($values,$stats);
?>
  </FIELDSET>




 <FIELDSET class="sqaf">
  <LEGEND class="sqaf">Was background subtraction used?</LEGEND>
<?php
  $values =  array('0' => 'no', '1' => 'yes');
$stats = collection_stats('muga_session','background_subtraction_used',$values,false,$collection_id);
tabulate_stats($values,$stats);
?>
  </FIELDSET>


 <FIELDSET class="sqaf">
  <LEGEND class="sqaf">What type of smoothing was used (if any)?</LEGEND>
<?php
  $values = array('0' => 'No smoothing', '1' => 'Temporal', '2' => 'Spatial', '3' => 'Spatial and Temporal');
$stats = collection_stats('muga_session','smoothing_type',$values,false,$collection_id);
tabulate_stats($values,$stats);
?>
  </FIELDSET>


	</body>
  </html>
