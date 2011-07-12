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
    $is_valid = update_data('wwsf_data','uptake_right','/^\d+$/',false,'study_id',$study_id,'session_id',$session_id);
    $is_valid = update_data('wwsf_data','uptake_left','/^\d+$/',false,'study_id',$study_id,'session_id',$session_id) and $is_valid;
    $query = sprintf('UPDATE wwsf_data SET is_valid=%d WHERE study_id=%d AND session_id=%d',$is_valid,$study_id,$session_id);
    //    echo $study_id . " " . $is_valid;

    $result = my_query($query);
    
    if ($_POST['navigate'] == 'next') { 
      $query = sprintf('SELECT study_id FROM wwsf_data WHERE session_id=%d AND study_id>%d ORDER BY study_id',$session_id,$study_id);

      $result = my_query($query);

      if ($row = mysql_fetch_assoc($result)) {
	$study_id = $row['study_id']; 
      }
    }
    if ($_POST['navigate'] == 'previous') {
      $query = sprintf('SELECT study_id FROM wwsf_data WHERE session_id=%d AND study_id<%d ORDER BY study_id DESC',$session_id,$study_id);

      $result = my_query($query);

      if ($row = mysql_fetch_assoc($result)) {
	$study_id = $row['study_id']; 
      }
    }
    if (preg_match('/^\d+$/', $_POST['navigate'])) {
      $query = sprintf('SELECT study_id FROM wwsf_data WHERE session_id=%d AND study_id=%d',$session_id, $_POST['navigate']);
      
      $result = my_query($query);
      
      if ($row = mysql_fetch_assoc($result)) {
	$study_id = $row['study_id']; 
      }    }
  } else {
      $query = sprintf('SELECT study_id FROM wwsf_data WHERE session_id=%d ORDER BY study_id',$session_id);

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
	<style>
	input#invalid {
		background-color: rgb(255,230,230);
	}
	span#invalid {
		background-color: rgb(255,230,230);
	}
	</style>
  <title>Data</title>
</head>
<body>

<div class="sqamenu" style="text-align: right"><span class="sqamenu"><a href="../index.php">Welcome</a></span> <span class="sqamenu"><a href="../checked_in.php?session_code=<?php echo session_code(); ?>">Participate</a></span> <span class="sqamenu"><a href="../admin_audits.php">Administrate</a></span> <p>
<span class="sqamenusel">WWSF</span></div>

<h1 class="sqa">Software Quality Assurance</h1>
  <div class="sqah1">&nbsp;</div>


  <h2 class="sqa">WWSF data entry [ <a href="wwsf_data.php?session_code=<?php echo session_code(); ?>">Part 1</a> | <span class="sqasel">Part 2</span> |
<A href="../checked_in.php?session_code=<?php echo session_code(); ?>">Exit</a> ]</h2>

<p>Enter data and click save below.</p>


<form action="wwsf_study.php?session_code=<?php echo session_code(); ?>" method="POST">

<FIELDSET class="sqaf">
  <LEGEND class="sqaf">Results of Analysis</LEGEND>


<table cellspacing="0" cellpadding="0">
<tr>
<th>Study,</th>
<th>Right Uptake (%),</th>
<th>Left Uptake (%),</th>
</tr>

<?php

$error_messages = array();

$table_name = 'wwsf_data';

$field_names = array('uptake_left','uptake_right');
$hidden_field_names = array('study_id');
$all_field_names = array_merge($hidden_field_names,$field_names);


if (isset($_POST['rm_csv'])) {
    // Create a CVS text area
    echo "<tr><td colspan='" . count($all_field_names) . "'>";
    echo implodeintotextarea($all_field_names);
    echo "</td></tr>";
} else if (isset($_POST['rm_table'])) {
    // Create a Table (which needs to be saved)
    echo explodeintotexttable($all_field_names);
    echo "<tr><td colspan='" . count($all_field_names) . "'><span id='invalid'>Table is not saved</span><td></tr>";
} else {
    // Create a table based on SQL query

     $error_messages = updatetextinputs($table_name,'uptake_right','/^\d+$/',false,'study_id',$session_id,$error_messages,"Must be an integer");
     $error_messages = updatetextinputs($table_name,'uptake_left','/^\d+$/',false,'study_id',$session_id,$error_messages,"Must be an integer");

    $query = sprintf('SELECT study_id, uptake_left, uptake_right FROM wwsf_data WHERE session_id=%d',$session_id);
    $select_result = my_query($query);
    echo explodeintotextinputs($field_names,$hidden_field_names,$select_result,$error_messages);
    $count_errors = count($error_messages);
    
    if ($count_errors==0) {
        $query = sprintf('UPDATE ' . $table_name . ' SET is_valid=1 WHERE session_id=%d',$session_id);
    } else {
        $query = sprintf('UPDATE ' . $table_name . ' SET is_valid=0 WHERE session_id=%d',$session_id);
    }
    $result = my_query($query);
}

?>

</table>

</FIELDSET>


<?php


if ($count_errors > 0) {
    echo "<span id='invalid'>" . $count_errors . " invalid fields</span>";
}
?>


<?php if (isset($_POST['rm_csv'])) {
    echo "<input name='rm_table' type='submit' value='Convert to table'></input>"; 
} else {
    echo "<input name='rm_csv' type='submit' value='Convert to CSV'></input>"; 
    echo "<input name='rm_save' type='submit' value='Save'></input>";
}
?>



</form>


  <div>Click on save and then exit using menu bar</div>

</body>
</html>
