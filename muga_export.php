
<?
$query = 'insert into module_sheets SET number_of_rows=(select count(data_id) from muga_data
  JOIN session ON muga_data.session_id=session.session_id 
  JOIN  collection ON session.collection_id=collection.collection_id 
where collection.collection_id=' . $collection_id . ', moodle_id=' . $moodle_id . ', title="' . $sheet_name . '", type_id=3';

$result = mysql_query($query);
if (!$result) {
    $message  = 'Invalid query: ' . mysql_error() . "\n";
    $message .= 'Whole query: ' . $query;
    die($message);
}

print($result);
?>