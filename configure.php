<?php

require_once('settings.php');

$link = mysql_connect($db_server,$db_username,$db_password);
if (! mysql_select_db($db_database)) {
  die(mysql_error());
 }

$drop_tables = array('drop table collection',
		     'drop table module',
		     'drop table operator',
		     'drop table session',
		     'drop table institution',
		     'drop table request');

$create_tables = array('create table collection ( collection_id INTEGER PRIMARY KEY AUTO_INCREMENT, scope TEXT, start_date DATE, end_date DATE, module_id INTEGER, password TEXT, institution_id INTEGER )',
		       'create table module ( module_id INTEGER PRIMARY KEY AUTO_INCREMENT, title TEXT, description TEXT, info_url TEXT, data_url TEXT, report_url TEXT, register_module TEXT)',
		       'create table operator ( operator_id  INTEGER PRIMARY KEY AUTO_INCREMENT, operator_code TEXT, full_name TEXT, phone_number TEXT, email_address TEXT, institution_id INTEGER, registered DATETIME )',
		       'create table session ( session_id INTEGER PRIMARY KEY AUTO_INCREMENT, session_code TEXT, collection_id INTEGER, operator_id INTEGER, comments TEXT, is_verified BOOLEAN, registered DATETIME )',
		       'create table institution ( institution_id INTEGER PRIMARY KEY AUTO_INCREMENT, title TEXT, department TEXT, contact_name TEXT, phone_number TEXT, email_address TEXT )',
		       'create table request ( request_id INTEGER PRIMARY KEY AUTO_INCREMENT,institution_title TEXT, department TEXT, contact_name TEXT, phone_number TEXT, email_address TEXT, scope TEXT, start_date DATE, end_date DATE, module_id INTEGER, password TEXT, lodged DATETIME)'); 

$insert_data = array('insert into institution (title, department, contact_name, phone_number, email_address) values ("Westmead Hospital","Medical Physics","Robert Barnett","0298456533","Robert.Barnett@swahs.nsw.health") ',
		     'insert into module (title, description, info_url, data_url, report_url, register_module) values ("My Module", "A test module", "MUGA/muga_info.php", "MUGA/muga_data.php", "MUGA/muga_report.php","")',
		     'insert into collection (scope, start_date, end_date, module_id, password, institution_id) values ("NSW", "2008-03-13", "2008-05-14", (SELECT module_id FROM module), "test", (SELECT  institution_id FROM institution)  )',
		     'insert into operator (operator_code, full_name, phone_number, email_address, institution_id, registered) values (SUBSTRING(UUID(),1,6), "Robert Barnett", "0290299410", "robert@barnett.id.au", (SELECT institution_id FROM institution), NOW())',
		     'insert into session (session_code, collection_id, operator_id, comments, is_verified, registered) values (SUBSTRING(UUID(),1,6), ( SELECT  collection_id FROM  collection), (SELECT operator_id FROM operator), "My first entry", TRUE, NOW())',
		     'insert into session (session_code, collection_id, operator_id, comments, is_verified, registered) values (SUBSTRING(UUID(),1,6), ( SELECT  collection_id FROM  collection), (SELECT operator_id FROM operator), "My second entry", FALSE, NOW())',
		     'insert into request (institution_title, department, contact_name, phone_number, email_address, scope, start_date, end_date, module_id, password, lodged) values ("Ministry of Silly Walks", "123456", "Edward Legless", "123456", "walk@govt.uk", "Very important", "2003-03-05", "2007-05-06", (SELECT module_id FROM module), "ha", NOW())'
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