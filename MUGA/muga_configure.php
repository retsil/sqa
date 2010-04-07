<?php

require_once('../settings.php');

$link = mysql_connect($db_server,$db_username,$db_password);
if (! mysql_select_db($db_database)) {
  die(mysql_error());
 }

$drop_tables = array('drop table muga_session',
		     'drop table muga_data'
		     );

$create_tables = array('create table muga_session ( session_id INTEGER PRIMARY KEY, 
		       manufacturer TEXT, 
		       make_and_model TEXT,
		       name_of_software TEXT,
		       software_version TEXT,
		       type_of_software TEXT,
		       operator_experience_in_months INTEGER,
		       operator_frequency_per_month INTEGER,
		       normal_range_minimum REAL,
		       normal_range_maximum REAL,
		       number_of_frames_actually_analysed INTEGER,
		       region_of_interest_method INTEGER,
		       phase_images_used_for_ROI_definition INTEGER,
		       separate_systole_and_diastole_ROIs_used INTEGER,
		       background_subtraction_used INTEGER,
		       description_of_regions TEXT,
		       description_of_background_subtraction TEXT,
		       smoothing_type INTEGER,
		       smoothing_cycles INTEGER,
		       description_of_ejection_fraction_calculation TEXT )',
		       'create table muga_data ( data_id INTEGER PRIMARY KEY AUTO_INCREMENT, 
		       session_id INTEGER,
		       study_id INTEGER,
		       lvef REAL,
		       end_diastolic_frame_number INTEGER,
		       end_systolic_frame_number INTEGER,
		       time_per_frame_ms INTEGER, 
		       first_point_on_lv_curve REAL,
		       is_valid BOOL
		       )');


$insert_data = array('insert into module (title, description, info_url, data_url, report_url, register_module) values ("MUGA", "GHPS", "MUGA/muga_info.php", "MUGA/muga_data.php", "MUGA/muga_report.php","MUGA/muga_insert.php")',
		     'insert into collection (scope, start_date, end_date, module_id, password, institution_id) values ("NSW", "2008-03-13", "2008-05-14", (SELECT module_id FROM module WHERE module.title="MUGA"), "test", (SELECT  institution_id FROM institution)  )'
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