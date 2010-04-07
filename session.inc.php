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
?>