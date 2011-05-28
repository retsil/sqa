
<?php

mysql_query("begin"); 

$query = 'insert into module_sheets SET number_of_rows=(select count(data_id) from muga_data
  JOIN session ON muga_data.session_id=session.session_id 
  JOIN  collection ON session.collection_id=collection.collection_id 
where collection.collection_id=' . $collection_id . '), moodle_id=' . $moodle_id . ', title="' . $sheet_name . '", type_id=3';

$result = mysql_query($query);
if (!$result) {
    $message  = 'Invalid query: ' . mysql_error() . "\n";
    $message .= 'Whole query: ' . $query;
    mysql_query("rollback"); 
    die($message);
}

$sheet_id = mysql_insert_id();
print("Sheet id: " .$sheet_id  . "\n");

$fields =array(
  0 => 'operator.operator_code',
  1 => 'muga_data.study_id',
  2 => 'muga_data.lvef',
  3 => 'muga_data.end_diastolic_frame_number',
  4 => 'muga_data.end_systolic_frame_number',
  5 => 'muga_data.time_per_frame_ms',
  6 => 'muga_data.first_point_on_lv_curve',
  7 => 'muga_data.is_valid'
	       );

foreach( $fields as $column_id => $field) {

  $result = mysql_query('SET @row_counter := 0;');
  $query = '
insert into module_data (row_id,value,column_id,sheet_id) 
select @row_counter := @row_counter + 1 AS row_id, ' . $field . ' AS value, ' . $column_id . ' AS column_id, ' . $sheet_id . ' AS sheet_id
  from muga_data
  JOIN session ON muga_data.session_id=session.session_id 
  JOIN  collection ON session.collection_id=collection.collection_id 
  JOIN operator ON session.operator_id=operator.operator_id
where collection.collection_id=' . $collection_id . ';';

  $result = mysql_query($query);
  if (!$result) {
    $message  = 'Invalid query: ' . mysql_error() . "\n";
    $message .= 'Whole query: ' . $query;
    die($message);
    mysql_query("rollback"); 
  }
}

mysql_query("commit"); 
?>

