<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
    "http://www.w3.org/TR/html4/loose.dtd">
<?php

require_once('../settings.php');

$link = mysql_connect($db_server,$db_username,$db_password);
if (! mysql_select_db($db_database)) {
  die(mysql_error());
}

require_once('../session.inc.php');

$session_id = check_session(-1);


if (empty( $study_id)) {
  if (preg_match('/^\d+$/', $_POST['study_id'])) {
    $study_id =  $_POST['study_id'];
    $is_valid = update_data('muga_data','lvef','/^\d+$/',false,'study_id',$study_id,'session_id',$session_id);
    $is_valid = update_data('muga_data','end_diastolic_frame_number','/^\d+$/',false,'study_id',$study_id,'session_id',$session_id) and $is_valid;
    $is_valid = update_data('muga_data','end_systolic_frame_number','/^\d+$/',false,'study_id',$study_id,'session_id',$session_id) and $is_valid;
    $is_valid = update_data('muga_data','time_per_frame_ms','/^\d+$/',false,'study_id',$study_id,'session_id',$session_id) and $is_valid;
    $is_valid = update_data('muga_data','first_point_on_lv_curve','/^\d+$/',false,'study_id',$study_id,'session_id',$session_id) and $is_valid;
    $query = sprintf('UPDATE muga_data SET is_valid=%d WHERE study_id=%d AND session_id=%d',$is_valid,$study_id,$session_id);
    //    echo $study_id . " " . $is_valid;

    $result = my_query($query);
    
    if ($_POST['navigate'] == 'next') { 
      $query = sprintf('SELECT study_id FROM muga_data WHERE session_id=%d AND study_id>%d ORDER BY study_id',$session_id,$study_id);

      $result = my_query($query);

      if ($row = mysql_fetch_assoc($result)) {
	$study_id = $row['study_id']; 
      }
    }
    if ($_POST['navigate'] == 'previous') {
      $query = sprintf('SELECT study_id FROM muga_data WHERE session_id=%d AND study_id<%d ORDER BY study_id DESC',$session_id,$study_id);

      $result = my_query($query);

      if ($row = mysql_fetch_assoc($result)) {
	$study_id = $row['study_id']; 
      }
    }
    if (preg_match('/^\d+$/', $_POST['navigate'])) {
      $query = sprintf('SELECT study_id FROM muga_data WHERE session_id=%d AND study_id=%d',$session_id, $_POST['navigate']);
      
      $result = my_query($query);
      
      if ($row = mysql_fetch_assoc($result)) {
	$study_id = $row['study_id']; 
      }    }
  } else {
      $query = sprintf('SELECT study_id FROM muga_data WHERE session_id=%d ORDER BY study_id',$session_id);

      $result = my_query($query);
      
      if ($row = mysql_fetch_assoc($result)) {
	$study_id = $row['study_id']; 
      }
  }
 }

?>

<html>
<head>
   	<style type="text/css" title="currentStyle" media="screen">
		@import "../official_blue.css";
	</style>
  <title>Data</title>
</head>
<body>
<form action="muga_study.php?session_code=<?php echo session_code(); ?>" method="POST">

<FIELDSET class="sqaf">
  <LEGEND class="sqaf">Results of Analysis</LEGEND>

<?php 

$query = sprintf('SELECT lvef, end_diastolic_frame_number, end_systolic_frame_number, time_per_frame_ms, first_point_on_lv_curve FROM muga_data WHERE session_id=%d AND study_id=%d',$session_id,$study_id);

$result = my_query($query);

$row = mysql_fetch_assoc($result)
?>

 <DIV class="sqaf">
	<SPAN class="sqaf">Study ID:</SPAN>
  	<INPUT name="study_id" size="4" type="hidden" value="<?php echo $study_id; ?>" tabindex="0" /><?php echo $study_id; ?>
 </DIV>
 <DIV class="sqaf">
  <SPAN class="sqaf">LVEF (%):</SPAN>
  	<INPUT name="lvef" size="4" type="text" value="<?php echo $row['lvef']; ?>" tabindex="1" />
  </DIV>
 <DIV class="sqaf">
  <SPAN class="sqaf">End-Diastolic Frame Number:</SPAN>
  	<INPUT name="end_diastolic_frame_number" size="4" type="text" value="<?php echo $row['end_diastolic_frame_number']; ?>" tabindex="2" />
  </DIV>
 <DIV class="sqaf">
	<SPAN class="sqaf">End-Systolic Frame Number</SPAN>
  	<INPUT name="end_systolic_frame_number" size="4" type="text" value="<?php echo $row['end_systolic_frame_number']; ?>" tabindex="3" />
  </DIV>
 <DIV class="sqaf">
	<SPAN class="sqaf" >Time per Frame (ms)</SPAN>
  	<INPUT name="time_per_frame_ms" size="6" type="text" value="<?php echo $row['time_per_frame_ms']; ?>" tabindex="4" />
  </DIV>
 <DIV class="sqaf">
	<SPAN class="sqaf">Count value of first point  of LV curve</SPAN>
  	<INPUT name="first_point_on_lv_curve" size="6" type="text" value="<?php echo $row['first_point_on_lv_curve']; ?>" tabindex="5" />
  </DIV>
 <DIV class="sqaf">
  <input type="submit" name="navigate" value="previous" tabindex="6">
  <input type="submit" name="navigate" value="next" tabindex="7">
  </DIV>

<table class="list">
<tr>
<?php
mysql_free_result($result);

 $query = sprintf('SELECT study_id, is_valid FROM muga_data WHERE session_id=%d',$session_id);


$result = my_query($query);


while($row = mysql_fetch_assoc($result)) {
  $style = '';
  if ($row['study_id'] == $study_id) {
    $style .= "background:  #001181; color: #e0e0ff;";
  } else {
    $style .= "color:  #001181; background-color: #e0e0ff;";
  }
  if (! $row['is_valid']) {
    $style .= "border: thin solid red;";
  } else {
    $style .= "border: thin solid gray;";
  }
  echo '<td class="list">' . 
    '<input name="navigate" style="' . $style . 
    '"type="submit" value="' . $row['study_id'] . 
    '">' . '</td>';
}


?>
</tr>
</table>

</FIELDSET>


</form>

</body>
</html>
