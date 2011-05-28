
<?php


require_once('../settings.php');

$link = mysql_connect($db_server,$db_username,$db_password);
if (! mysql_select_db($db_database)) {
  die(mysql_error());
 }

$drop_tables = array('drop table wwsf_data',
		     'drop table wwsf_session',
		     );



$create_tables = array('create table wwsf_session ( session_id INTEGER PRIMARY KEY, 
 workstation INTEGER,
 workstation_other TEXT,
 software TEXT,
 version TEXT,
 tracer_used INTEGER, 
 tracer_used_other TEXT, 
 background_roi INTEGER, 
 background_other TEXT, 
 calculation_method INTEGER, 
 calculation_method_other TEXT)',
'create table wwsf_data ( data_id INTEGER PRIMARY KEY AUTO_INCREMENT, 
		       session_id INTEGER,
		       study_id INTEGER,
		       uptake_right REAL,
		       uptake_left REAL,
		       is_valid BOOL
		       )');



$insert_data = array('insert into module (title, description, info_url, data_url, report_url, register_module) values ("WWSF", "Renal Split Function", "WWSF/wwsf_info.php", "WWSF/wwsf_data.php", "WWSF/wwsf_report.php","WWSF/wwsf_insert.php")',
		     );

foreach ($drop_tables as &$value) {
  if (! mysql_query($value)) {
    echo('Error dropping tables: ' . mysql_error());
  }
}

foreach ($create_tables as &$value) {
  if (! mysql_query($value)) {
    die('Error creating tables: ' . mysql_error());
  }
}


foreach ($insert_data as &$value) {
  if (! mysql_query($value)) {
    die('Error inserting data: ' . mysql_error());
  }
}

mysql_close($link);



?>