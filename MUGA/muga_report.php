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
  <title>MUGA Data Report</title>
</head>
<body>

<?php

function column_range($table, $column, $collection_id) { 
	  $stats = array();

	  $query = sprintf('SELECT max(%s) AS max, min(%s) AS min FROM %s, session WHERE is_valid=1 AND session.session_id=muga_data.session_id AND session.collection_id=%d', $column, $column, $table, $collection_id);
	  $result = my_query($query);
	  $row = mysql_fetch_assoc($result);
	  $stats['min'] = $row['min'];
	  $stats['max'] = $row['max'];
	  return $stats;
}

function study_stats($table, $column, $study_id, $session_id, $collection_id) {
	  $stats = array();

	  $query = sprintf('SELECT avg(%s) AS avg, stddev(%s) AS stddev, max(%s) AS max, min(%s) AS min FROM %s, session WHERE study_id=%d AND is_valid=1 AND session.session_id=muga_data.session_id AND session.collection_id=%d', $column, $column, $column, $column, $table, $study_id, $collection_id);
	  $result = my_query($query);
	  $row = mysql_fetch_assoc($result);
	  $stats['avg'] = $row['avg'];
	  $stats['stddev'] = $row['stddev'];
	  $stats['min'] = $row['min'];
	  $stats['max'] = $row['max'];
	  
	  $query = sprintf('SELECT %s AS value FROM %s, session WHERE study_id=%d AND is_valid=1 AND session.session_id=muga_data.session_id AND muga_data.session_id=%d', $column, $table, $study_id, $session_id);
	  $result = my_query($query);
	  $row = mysql_fetch_assoc($result);
	  $stats['value'] = $row['value'];
	  if ($stats['stddev'] != 0) {
	    $sd =  ($stats['value'] - $stats['avg'])/$stats['stddev'];
	  } else {
	    $sd = 0;
	  }
	  $stats['comparison'] = "<span style='color:green;'>Expected</span>";
	  if ($sd < -1)  $stats['comparison'] = "<span style='color:brown;'>Low</span>";
	  if ($sd < -2)  $stats['comparison'] = "<span style='color:red;'>Very low</span>";
	  if ($sd > 1)  $stats['comparison'] = "<span style='color:brown;'>High</span>";
	  if ($sd > 2)  $stats['comparison'] = "<span style='color:red;'>Very high</span>";
	  if ($stats['value'] == NULL) $stats['comparison'] = "No data";
	  if ($stats['avg'] == NULL) $stats['comparison'] = "No average";
	  if ($stats['stddev'] == NULL) $stats['comparison'] = "No stddev";

	  return $stats;
}



function session_stats($table, $column, $values, $quot, $session_id) {
	  foreach ($values as $key => $value) { 
	    if ($quot) {
	      $query = sprintf('SELECT COUNT(%s) FROM session, %s WHERE %s.session_id=session.session_id AND session.session_id=%d AND %s.%s="%s"',$column,$table,$table,$session_id,$table,$column,mysql_real_escape_string($key));
	    } else {
	      $query = sprintf('SELECT COUNT(%s) FROM session, %s WHERE %s.session_id=session.session_id AND session.session_id=%d AND %s.%s=%s',$column,$table,$table,$session_id,$table,$column,$key);
	    }
	    $result = my_query($query);
	    $row = mysql_fetch_assoc($result);
	    $stats[$key] = $row[sprintf('COUNT(%s)',$column)];
	    //echo $row[sprintf('COUNT(%s)',$column)];
	  }
	  return $stats;	 
}

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

function tabulate_stats($values,$stats,$session_stats) {


  echo '<table class="results">';
    echo '<tr>';
    echo '<td></td>';
    echo '<th class="results">Count</th>';
    echo '<th class="results">Percent</th>';
    echo '</tr>';

    $total = 0;
  foreach ($values as $key => $value) { 
    $total += $stats[$key];
  }

  if ($total == 0) {
    echo '<tr>';
    echo '<th class="results"></th>';
    echo '<td class="results">No data</td>';
    echo '<td class="results">No data</td>';
    echo '</tr>';
  } else {
    foreach ($values as $key => $value) { 
      $rel_stats[$key] = 100*$stats[$key]/$total;
    }

    foreach ($values as $key => $value) { 
      echo '<tr>';
      echo '<th class="results">' . $value . '</th>';
      if ($session_stats[$key] > 0) {
	echo '<td class="myresults">' . $stats[$key] . '</td>';
	echo '<td class="myresults">' . sprintf("%d%%",$rel_stats[$key]) . '</td>';
      } else {
	echo '<td class="results">' . $stats[$key] . '</td>';
	echo '<td class="results">' . sprintf("%d%%",$rel_stats[$key]) . '</td>';
      }
      echo '</tr>';
    }
  }
  echo '</table>';

  if ($total > 0) {
    $chart_data = implode(',',array_values($rel_stats));
    $chart_labels = implode('|',array_values($values));

    echo '<img src="http://chart.apis.google.com/chart?cht=p3&amp;chf=bg,s,E0E0F0&amp;chd=t:' . $chart_data . '&amp;chs=350x100&amp;chl=' . $chart_labels . '"  alt="Google Chart">';
  }
  echo '<br clear="all">';
}

$collection_id = collection_id($session_id);

?>

<div class="sqamenu"><span class="sqamenu"><a href="../index.php">Welcome</a></span> <span class="sqamenu"><a href="../checked_out.php">Participate</a></span> <span class="sqamenusel">MUGA</span></div>

<h1 class="sqa">Software Quality Assurance</h1>
  <div class="sqah1">&nbsp;</div>



<?php

 echo '<table class="results">';
    echo '<tr>';
    echo '<th class="results">Study ID</th>';

# $fields = array('lvef','end_systolic_frame_number','end_diastolic_frame_number');
#,'time_per_frame_ms','first_point_on_lv_curve');
$field_labels = array('lvef' => "Ejection Fraction", 'end_diastolic_frame_number' => "End Systole - End Diastole Frame Difference", 'end_systolic_frame_number' => "End Systole Frame");
#, 'time_per_frame_ms' => "Time Per Frame (ms)", 'first_point_on_lv_curve' => "First Point On Activity Curve");
$field_sql = array('lvef' => "lvef", 'end_diastolic_frame_number' => "MOD(end_systolic_frame_number-end_diastolic_frame_number+32,32)", 'end_systolic_frame_number' => "end_systolic_frame_number");
#, 'time_per_frame_ms' => "time_per_frame_ms", 'first_point_on_lv_curve' => "first_point_on_lv_curve");

foreach ($field_labels as $field => $label) { 
    echo '<th class="results">' . $label . '</th>';
}
    echo '</tr>';
?>

<?php

$field_max = array();
$field_min = array();
$field_avg = array();
$field_up = array(); # mean plus 1 stddev
$field_down = array(); # mean minus 1 stddev
$field_value = array(); # Your result
foreach ($field_labels as $field => $label) { 
  $field_max[$field] = array();
  $field_min[$field] = array();
  $field_avg[$field] = array();
  $field_up[$field] = array(); # mean plus 1 stddev
  $field_down[$field] = array(); # mean minus 1 stddev
  $field_value[$field] = array(); # Your result
}

$query = sprintf('SELECT study_id, is_valid FROM muga_data WHERE session_id=%d',$session_id);
$result = my_query($query);

$row_i = 0;
while($row = mysql_fetch_assoc($result)) {
  $study_id = $row['study_id'];
  echo "<tr><th>" .  $study_id . "</th>";
  foreach ($field_labels as $field => $label) { 
#  foreach ($fields as $field) {
    $stats = study_stats('muga_data', $field_sql[$field], $study_id, $session_id,$collection_id);
    echo "<td>" .  $stats['value'] . " (" . $stats['comparison'] . ")</td>";
    $field_max[$field][$row_i] = $stats['max'];
    $field_min[$field][$row_i] = $stats['min'];
    $field_avg[$field][$row_i] = $stats['avg'];
    $field_up[$field][$row_i] = $stats['avg'] + $stats['stddev'];
    $field_down[$field][$row_i] = $stats['avg'] - $stats['stddev'];
    $field_value[$field][$row_i] = $stats['value'];

  }
  $row_i++;
  echo "</tr>";
}
echo "</table>";

$nstudies = $row_i;

foreach ($field_labels as $field => $label) { 
    # foreach ($fields as $field) {
  echo "<br clear='all'><h3>" . $label . "</h3>";
  $column_range = column_range('muga_data', $field_sql[$field], $collection_id);
  $column_scale = 
    'chds=' . $column_range['min'] . ',' . $column_range['max'] . "&amp;". 
    'chxr=0,' . $column_range['min'] . ',' . $column_range['max'];

  $chart_series = array();
  $chart_series[0] = '-1,' . implode(',',$field_min[$field]) . ',-1';
  $chart_series[1] = '-1,' . implode(',',$field_down[$field]) . ',-1';
  $chart_series[2] = '-1,' . implode(',',$field_up[$field]) . ',-1';
  $chart_series[3] = '-1,' . implode(',',$field_max[$field]) . ',-1';
  $chart_series[4] = '-1,' . implode(',',$field_avg[$field]) . ',-1';
  $chart_series[5] = '-1,' . implode(',',$field_value[$field]) . ',-1';
#  $chart_data[4] = implode(',',$field_value[$field]);
  $chart_data = implode('|',$chart_series);
#  echo "<hr><img width='400' height='150' src='http://www.google.com/chart?chs=400x125&amp;cht=lc&amp;chd=t0:" . $chart_data . "&amp;chm=F,0000FF,0,1:" . $row_i . ",20' ALT='Google chart'>";
    echo "<img width='900' height='200' src='http://chart.apis.google.com/chart?chs=900x200&amp;cht=ls&amp;chd=t0:" . $chart_data . "&amp;chm=F,FF9900,0,1:" . $nstudies . ",40|H,0CBF0B,0,1:" . $nstudies . ",1:20|H,000000,4,1:" . $nstudies . ",1:40|H,0000FF,3,1:" . $nstudies . ",1:20|o,FF0000,5,1:" . $nstudies . ",5&amp;chxt=y&amp;&amp;chxt=y&amp;" . $column_scale . "&amp;chdl=Max+Value|Standard Deviation (25%-75%)|Mean|Min+Value|Your value&amp;chco=0000FF,FF9900,000000,0CBF0B,FF0000'>";

# "chm=o,FF0000,5,1:" . $nstudies . ",5&amp;"
    # chm=o,FF0000,5,,5|
  for ($i=0; $i < $nstudies; $i++) { 
    $chart_data = 
      '-1,' . $field_min[$field][$i] . ',-1|' .
      '-1,' . $field_down[$field][$i] . ',-1|' . 
      '-1,' . $field_up[$field][$i] . ',-1|' . 
      '-1,' . $field_max[$field][$i] . ',-1|' .
      '-1,' . $field_avg[$field][$i] . ',-1';
#    echo "<hr><img width='400' height='500' src='http://chart.apis.google.com/chart?chs=400x500&amp;cht=ls&amp;chd=t0:" . $chart_data . "&amp;chm=F,FF9900,0,1,40|H,0CBF0B,0,1,1:20|H,000000,4,1,1:40|H,0000FF,3,1,1:20&amp;chxt=y&amp;chxr=0,0,25&amp;chxt=y&amp;chdl=Max+Value|Standard Deviation (25%-75%)|Median|Min+Value&amp;chco=0000FF,FF9900,000000,0CBF0B'>";
  }

#  echo "<hr><img width='400' height='150' src='http://www.google.com/chart?chs=400x125&cht=lc&chd=t0:" . $chart_data . "&chm=F,0000FF,0,1:" . $row_i . ",20'>";

# http://www.google.com/chart?chs=200x125&cht=lc&chd=t0:5,5,10,7,12,6|35,25,45,47,24,46|15,40,30,27,39,54|70,55,63,59,80,60&chm=F,0000FF,0,,20
#  http://chart.apis.google.com/chart?chs=75x75&cht=lc&chd=t0:0,17,0|0,30,0|0,45,0|0,63,0|-1,40,-1|-1,5,-1|-1,80,-1&chm=F,0000FF,0,1,10&chxt=y

}

?>
<br clear="all">
<hr>



  <div class="results">
  <h3 class="results">How long have you been processing MUGA scans?</h3>
<?php
$values = array('0' => 'less than 6 months', '6' => '6 months to 1 year', '12' => 'longer than 1 year');
$stats = collection_stats('muga_session','operator_experience_in_months',$values,false,$collection_id);
$session_stats = session_stats('muga_session','operator_experience_in_months',$values,false,$session_id);
tabulate_stats($values,$stats,$session_stats);
?>
  </div>

 <div class="results">
  <h3 class="results">Type of software:</h3>
<?php
  $values =array('commercial' => 'commercial', 'inhouse' => 'in-house', 'other' => 'other');
$stats = collection_stats('muga_session','type_of_software',$values,true,$collection_id);
$session_stats = session_stats('muga_session','type_of_software',$values,true,$session_id);
tabulate_stats($values,$stats,$session_stats);
?>
  </div>

 <div class="results">
  <h3 class="results">How many scans do you process per month?</h3>
<?php
					   $values =  array('1' => ' 1 - 10', '11' => '11 - 30', '31' => '31+');
$stats = collection_stats('muga_session','operator_frequency_per_month',$values,false,$collection_id);
$session_stats = session_stats('muga_session','operator_frequency_per_month',$values,false,$session_id);
tabulate_stats($values,$stats,$session_stats);
?>
  </div>


 <div class="results">
  <h3 class="results">Number of frames actually analysed:</h3>
<?php
  $values = array('16' => '16', '32' => '32');
$stats = collection_stats('muga_session','number_of_frames_actually_analysed',$values,false,$collection_id);
$session_stats = session_stats('muga_session','number_of_frames_actually_analysed',$values,false,$session_id);
tabulate_stats($values,$stats,$session_stats);
?>
  </div>


 <div class="results">
  <h3 class="results">Region of interest method:</h3>
<?php
  $values = array('0' => 'manual', '1' => 'semiautomatic', '2' => 'fully-automatic');
$stats = collection_stats('muga_session','region_of_interest_method',$values,false,$collection_id);
$session_stats = session_stats('muga_session','region_of_interest_method',$values,false,$session_id);
tabulate_stats($values,$stats,$session_stats);
?>
  </div>


 <div class="results">
  <h3 class="results">Were separate systole and diastole ROIs used?</h3>
<?php
  $values =  array('0' => 'no', '1' => 'yes', '2' => 'don&#39;t know');
$stats = collection_stats('muga_session','separate_systole_and_diastole_ROIs_used',$values,false,$collection_id);
$session_stats = session_stats('muga_session','separate_systole_and_diastole_ROIs_used',$values,false,$session_id);
tabulate_stats($values,$stats,$session_stats);
?>
  </div>




 <div class="results">
  <h3 class="results">Was background subtraction used?</h3>
<?php
  $values =  array('0' => 'no', '1' => 'yes');
$stats = collection_stats('muga_session','background_subtraction_used',$values,false,$collection_id);
$session_stats = session_stats('muga_session','background_subtraction_used',$values,false,$session_id);
tabulate_stats($values,$stats,$session_stats);
?>
  </div>


 <div class="results">
  <h3 class="results">What type of smoothing was used (if any)?</h3>
<?php
  $values = array('0' => 'No smoothing', '1' => 'Temporal', '2' => 'Spatial', '3' => 'Spatial and Temporal');
$stats = collection_stats('muga_session','smoothing_type',$values,false,$collection_id);
$session_stats = session_stats('muga_session','smoothing_type',$values,false,$session_id);
tabulate_stats($values,$stats,$session_stats);
?>
  </div>

	</body>
  </html>
