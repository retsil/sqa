<?php

require_once('../settings.php');

$link = mysql_connect($db_server,$db_username,$db_password);
if (! mysql_select_db($db_database)) {
  die(mysql_error());
}

require_once('../session.inc.php');

$session_id = check_session(-1);


// Turn a sql selection result into a set of text inputs.
// Submitted data is susbstituted into the text fields regardless of whether it is valid
// Given the table layout, error messages appear as tooltips which appear as the user hovers over each entry.
function explodeintotextinputs($field_names,$hidden_field_names,$select_result,$error_messages) {
    $html = '';
	//echo count($error_messages);

    $r = 0;
    $t = 2;
    while($row = mysql_fetch_assoc($select_result)) {
        $html .= '<tr>';
        foreach ($hidden_field_names as $field_name) {
            $post_name = $field_name . $r;
            $value = htmlspecialchars($row[$field_name]);
            $html .= sprintf('<td><input name="%s" type="hidden" value="%s"/>%s</td>',$post_name,$value,$value);
        }
        foreach ($field_names as $field_name) {
            $post_name = $field_name . $r;
			if (array_key_exists($post_name,$_POST)) {
                // Show the submitted data 
                $value = htmlspecialchars($_POST[$post_name]);
			} else {
				// Otherwise there is no submitted data then display the SQL result
                $value = htmlspecialchars($row[$field_name]);
            }
            $html .= '<td><input ';
			if (array_key_exists($post_name,$error_messages)) {
                $error_message = htmlspecialchars($error_messages[$post_name]);
				$html .= sprintf(' id="invalid" onmouseover="tooltip.show(\'%s\');" onmouseout="tooltip.hide();" ',$error_message);
			} else {
				$html .= ' id="valid" ';
            }
            $html .= sprintf(' name="%s" type="text" value="%s" tabindex="%s" /></td>',$post_name,$value,$t);
            $t++;
        }
        $html .= "</tr>\n";
        $r++;
    }
    $html .= sprintf('<input name="nrows" type="hidden" value="%d" />',$r);
    return($html);
}


// Turn a sql selection result into a text area.
function implodeintotextarea($field_names) {
    $nrows=$_POST{'nrows'};
    $ncolumns=count($field_names);

    $csv_data=array_fill(0,$nrows,'');
    for ($r = 0; $r < $nrows; $r++) {
        $csv_line = array_fill(0,$ncolumns,'');
        for ($c = 0; $c < $ncolumns; $c++) {
            $name = $field_names[$c] . $r;
            $csv_line[$c] = $_POST{$name};
        }
        $csv_data[$r] = implode(',',$csv_line);
    }

    $html = '<textarea name="csv_data" rows="' . $nrows . '" cols="' . $ncolumns*8 . '">';
    $html .= implode("\n",$csv_data);
    $html .= '</textarea>';
    $html .= sprintf('<input name="nrows" type="hidden" value="%d" />',$r);
    return($html);
}



function explodeintotexttable($field_names) {
    $html = '';
    $lines = explode("\n",$_POST['csv_data']);
    $l=0;
    $maxf=0;
    foreach ($lines as $line) {
        print('<tr>');
        $f=0;
        $cols = explode(',',$line);
        foreach ($cols as $col) {
            $name = $field_names[$f] . $l;
            $html .= "<td><input name='$name' type='text' value='$col'></td>";
            $f++;
            if ($f > $maxf) {
                $maxf = $f;
            }
        }
        $html .= "</tr>";
        $l++;
    }
    $html .= "<input type='hidden' name='nrows' value='" . $l . "'>\n";
    return($html);
}

// Update the single field in a table
// Reads the HTTP POST variable called $postname and checks if it is valid.
// Applied a SQL update if the variable is valid. There can be up to two indexes
function updatetextfield($table,$column,$post_name,$regex,$quot,$column1,$index1,$column2,$index2) {
  $is_valid = false;
	if (! array_key_exists($post_name,$_POST)) return($is_valid);
	
  if (preg_match($regex, $_POST[$post_name])) {
    if ($quot) {
      $query = sprintf('UPDATE %s SET %s="%s" WHERE %s=%d AND %s=%d',
		       $table,$column,$_POST[$post_name],$column1,$index1,$column2,$index2);
    } else {
      $query = sprintf('UPDATE %s SET %s=%d WHERE %s=%d AND %s=%d',
		       $table,$column,$_POST[$post_name],$column1,$index1,$column2,$index2);
    }
//	  print($query);
    $result = mysql_query($query);
    if ($result) {
      $is_valid = true; //mysql_affected_rows() > 0;
    } else {
      $message  = 'Invalid query: ' . mysql_error() . "\n";
      $message .= 'Whole query: ' . $query;
      die($message);
    }
  }
  return($is_valid);
}

// Update a whole column in a table. This iterates over all rows in a single column.
// Error messages are stored in a hashtable for future use when displaying as text fields
function updatetextinputs($table_name,$field_name,$regex,$quot,$row_id_name,$session_id,$error_messages,$error_message) {
	if (! array_key_exists('nrows',$_POST)) return($error_messages);
    $nrows = $_POST['nrows'];
	for ($r=0; $r < $nrows; $r++) {
        $post_name = $field_name . $r;
		if (array_key_exists($row_id_name . $r,$_POST)) {
			$row_id  = $_POST[$row_id_name . $r];
			if (! updatetextfield($table_name,$field_name,$post_name,$regex,$quot,$row_id_name,$row_id,'session_id',$session_id)) {
                $error_messages[$post_name] = $error_message;
            }
        }
    }
    return($error_messages);
}



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


<div class="sqamenu"><span class="sqamenu"><a href="../index.php">Welcome</a></span> <span class="sqamenu"><a href="../checked_out.php">Participate</a></span> <span class="sqamenusel">MUGA</span></div>

<h1 class="sqa">Software Quality Assurance</h1>
  <div class="sqah1">&nbsp;</div>
  <h2 class="sqa">MUGA data entry [ Part 1 | <a href="muga_data.php?session_code=<?php echo session_code(); ?>">Part 2</a> |
<A href="../checked_in.php?session_code=<?php echo session_code(); ?>">Exit</a> ]</h2>



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

$error_messages = updatetextinputs($table_name,'lvef','/^\d+$/',false,'study_id',$session_id,$error_messages,"Must be an integer");
$error_messages = updatetextinputs($table_name,'end_diastolic_frame_number','/^\d+$/',false,'study_id',$session_id,$error_messages,"Must be an integer");
$error_messages = updatetextinputs($table_name,'end_systolic_frame_number','/^\d+$/',false,'study_id',$session_id,$error_messages,"Must be an integer");
$error_messages = updatetextinputs($table_name,'time_per_frame_ms','/^\d+$/',false,'study_id',$session_id,$error_messages,"Must be an integer");
$error_messages = updatetextinputs($table_name,'first_point_on_lv_curve','/^\d+$/',false,'study_id',$session_id,$error_messages,"Must be an integer");

$field_names = array('lvef','end_diastolic_frame_number','end_systolic_frame_number','time_per_frame_ms','first_point_on_lv_curve');
$hidden_field_names = array('study_id');
$all_field_names = array_merge($hidden_field_names,$field_names);

if ($_POST['rm'] == 'CSV') {

    echo "<tr><td colspan='" . count($all_field_names) . "'>";
    echo implodeintotextarea($all_field_names);
    echo "</td></tr>";
} else if ($_POST['rm'] == 'Table') {
    echo explodeintotexttable($all_field_names);

} else {
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


<?php if ($_POST['rm'] != 'CSV') echo "<input name='rm' type='submit' value='CSV'/>"; ?>
<?php if ($_POST['rm'] == 'CSV') echo "<input name='rm' type='submit' value='Table'/>"; ?>
<?php if ($_POST['rm'] != 'CSV') echo "<input name='rm' type='submit' value='Save'/>"; ?>

</form>


</body>
</html>
