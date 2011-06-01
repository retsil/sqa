<?php

require_once('../settings.php');

$link = mysql_connect($db_server,$db_username,$db_password);
if (! mysql_select_db($db_database)) {
  die(mysql_error());
}

require_once('../session.inc.php');

$session_id = check_session(-1);


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
    "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
   	<style type="text/css" title="currentStyle" media="screen">
		@import "../official_blue.css";
	</style>
	<style>
	input#invalid {
		background-color: rgb(255,230,230);
	}
	span#invalid {
		background-color: rgb(255,230,230);
	}
	</style>
	<script type="text/javascript" language="javascript" src="script.js"></script>
	<link rel="stylesheet" type="text/css" href="style.css" />
<title>Data</title>
</head>
<body>


<div class="sqamenu" style="text-align: right"><span class="sqamenu"><a href="../index.php">Welcome</a></span> <span class="sqamenu"><a href="../checked_in.php?session_code=<?php echo session_code(); ?>">Participate</a></span> <span class="sqamenu"><a href="../admin_audits.php">Administrate</a></span> <p>
<span class="sqamenusel">MUGA</span></div>


<h1 class="sqa">Software Quality Assurance</h1>
  <div class="sqah1">&nbsp;</div>
  <h2 class="sqa">MUGA data entry [ <span class="sqasel">Part 1</span> | <a href="muga_data.php?session_code=<?php echo session_code(); ?>">Part 2</a> |
<A href="../checked_in.php?session_code=<?php echo session_code(); ?>">Exit</a> ]</h2>


<?php
if ($_POST['rm'] == 'CSV') {
    echo "<SPAN class='sqa'>All studies are combined below into comma delimited text. Enter all data (including Study ID) and convert into a table.</span>"; 
} else {
    echo "<SPAN class='sqa'>Enter data and click save below.</span>"; 
}
?>



<form action="muga_study.php?session_code=<?php echo session_code(); ?>" method="POST">


<FIELDSET class="sqaf" style="width: 90%">
  <LEGEND class="sqaf">Results of Analysis</LEGEND>


<table cellspacing="0" cellpadding="0">
<tr>
<th>Study,</th>
<th>LVEF (%),</th>
<th>ED (frame),</th>
<th>ES (frame)</th>
<th>Duration (ms),</th>
<th>Counts</th>
</tr>

<?php

$error_messages = array();

$table_name = 'muga_data';

$field_names = array('lvef','end_diastolic_frame_number','end_systolic_frame_number','time_per_frame_ms','first_point_on_lv_curve');
$hidden_field_names = array('study_id');
$all_field_names = array_merge($hidden_field_names,$field_names);

if ($_POST['rm'] == 'CSV') {
    // Create a CVS text area
    echo "<tr><td colspan='" . count($all_field_names) . "'>";
    echo implodeintotextarea($all_field_names);
    echo "</td></tr>";
} else if ($_POST['rm'] == 'Table') {
    // Create a Table (which needs to be saved)
    echo explodeintotexttable($all_field_names);
    echo "<tr><td colspan='" . count($all_field_names) . "'><span id='invalid'>Table is not saved</span><td></tr>";
} else {
    // Create a table based on SQL query

     $error_messages = updatetextinputs($table_name,'lvef','/^\d+$/',false,'study_id',$session_id,$error_messages,"Must be an integer");
    $error_messages = updatetextinputs($table_name,'end_diastolic_frame_number','/^\d+$/',false,'study_id',$session_id,$error_messages,"Must be an integer");
    $error_messages = updatetextinputs($table_name,'end_systolic_frame_number','/^\d+$/',false,'study_id',$session_id,$error_messages,"Must be an integer");
    $error_messages = updatetextinputs($table_name,'time_per_frame_ms','/^\d+$/',false,'study_id',$session_id,$error_messages,"Must be an integer");
    $error_messages = updatetextinputs($table_name,'first_point_on_lv_curve','/^\d+$/',false,'study_id',$session_id,$error_messages,"Must be an integer");
    $query = sprintf('SELECT study_id, lvef, end_diastolic_frame_number, end_systolic_frame_number, time_per_frame_ms, first_point_on_lv_curve FROM muga_data WHERE session_id=%d',$session_id);
    $select_result = my_query($query);
    echo explodeintotextinputs($field_names,$hidden_field_names,$select_result,$error_messages);
    $count_errors = count($error_messages);
}

?>

</table>

</FIELDSET>



<?php


if ($count_errors > 0) {
    echo "<span id='invalid'>" . $count_errors . " invalid fields</span>";
}
?>


<?php if ($_POST['rm'] == 'CSV') {
    echo "<button name='rm' type='submit' value='Table'>Convert to table</button>"; 
} else {
    echo "<button name='rm' type='submit' value='CSV'>Convert to CSV</button>"; 
    echo "<button name='rm' type='submit' value='Save'>Save</button>";
    echo "<div>Click on save and proceed to part 2 using menu bar</div>";
}
?>


</form>


</body>
</html>
