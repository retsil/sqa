<?php

function session_code() {
  if (preg_match('/^\w\w\w\w\w\w$/', $_GET['session_code'])) {
    $session_code =  $_GET['session_code'];
  } else {
    if (preg_match('/^\w\w\w\w\w\w$/', $_POST['session_code'])) {
      $session_code =  $_POST['session_code'];
    } else return undef;
  }
  return $session_code;
  }

function collection_id($session_id) {
  $query = sprintf('SELECT collection_id FROM session WHERE session_id=%d',$session_id);
    $result = my_query($query);
      if ($row = mysql_fetch_assoc($result)) {
	$collection_id = $row['collection_id'];
      } else {
	$collection_id = -1;
      }
      return $collection_id;
}

function check_session($session_id) {

  if ($session_id < 0) {
    if (! $session_code=session_code()) {
	die('No session code');
      }
    
    
    
    $query = 'SELECT session_id FROM session WHERE session_code="' . $session_code . '"';
    
    $result = mysql_query($query);
    if (!$result) {
      $message  = 'Invalid query: ' . mysql_error() . "\n";
      $message .= 'Whole query: ' . $query;
      die($message);
    }
    
    if ($row = mysql_fetch_assoc($result)) {
      $session_id = $row['session_id'];
    } else {
      die('Invalid session code');
    }
  }
  return $session_id;
  }


function update_data($table,$column,$regex,$quot,$column1,$index1,$column2,$index2) {
  $is_valid = false;
  
  if (preg_match($regex, $_POST[$column])) {
    if ($quot) {
      $query = sprintf('UPDATE %s SET %s="%s" WHERE %s=%d AND %s=%d',
		       $table,$column,$_POST[$column],$column1,$index1,$column2,$index2);
    } else {
      $query = sprintf('UPDATE %s SET %s=%d WHERE %s=%d AND %s=%d',
		       $table,$column,$_POST[$column],$column1,$index1,$column2,$index2);
    }
    
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


function radio_input($name,$values,$row) {
  foreach ($values as $key => $value) { 
    if ($key == $row[$name]) {
       echo sprintf('<input type="radio" checked="checked" name="%s" value="%s">%s<br>',
		   $name,$key,$value);
    } else {
      echo sprintf('<input type="radio" name="%s" value="%s">%s<br>',
		   $name,$key,$value);
    }
  }
}

function update_field($table,$column,$regex,$quot,$column1,$index1,$row) {
  $is_valid = false;
  //  if (is_array($_POST[$column])) {
  //   $is_match = true;
  //  foreach ($_POST[$column] as $line) {
  //   echo 'line';
      // $is_match = $is_match and preg_match($regex, $line);
      //}
  // } else {
  $is_match = preg_match($regex,$_POST[$column]);
  
  $is_post = $_POST['update'] != ''; 
  if ($is_match and $is_post) {    
    if ($quot) {
      $query = sprintf('UPDATE %s SET %s="%s" WHERE %s=%d',
		       $table,$column,mysql_real_escape_string($_POST[$column]),$column1,$index1);
    } else {
      $query = sprintf('UPDATE %s SET %s=%d WHERE %s=%d',
		       $table,$column,$_POST[$column],$column1,$index1);
    }
    
    $result = mysql_query($query);
    if ($result) {
      $is_valid = true; 
      //      if (mysql_affected_rows() > 0) {
      // $row[$column] = $_POST[$column];
      // echo 'Column set';
	//}
      $row[$column] =  $_POST[$column];
    } else {
      $message  = 'Invalid query: ' . mysql_error() . "\n";
      $message .= 'Whole query: ' . $query;
      die($message);
    }
  } else {
    //  $_POST[$column] = $row[$column];

    if ($is_post) {
      echo '<span class="sqaerr">Invalid</span>';
      $row[$column] = $_POST[$column];
    }
    //  echo 'Invalid';
  }
  return($row);
  }


function update_radio_input($table,$column,$values,$quot,$column1,$index1,$row) {
  if (! empty($values[$_POST[$column]])){
    $row = update_field($table,$column,'//',$quot,$column1,$index1,$row);
    radio_input($column,$values,$row);
  } else {
    if (($_POST['update'] != '') and (! empty($_POST[$column]))) {
      die('Invalid input' . $_POST[$column]);
    } else {
      radio_input($column,$values,$row);
    }
  }
}

function my_query($query) {
  $result = mysql_query($query);
  
  if (!$result) {
    $message  = 'Invalid query: ' . mysql_error() . "\n";
    $message .= 'Whole query: ' . $query;
    die($message);
  }
  return $result;
}


// Turn a sql selection result into a set of text inputs.
// Submitted data is susbstituted into the text fields regardless of whether it is valid
// Given the table layout, error messages appear as tooltips which appear as the user hovers over each entry.
function explodeintotextinputs($field_names,$hidden_field_names,$select_result,$error_messages) {
    $html = '';
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