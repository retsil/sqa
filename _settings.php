<?php

$db_server = ':/opt/local/var/run/mysql5/mysqld.sock';
$db_database = 'SQA';
$db_username = 'root';
$db_password = '';

// 'Security-Plus'
$max_create_operators_per_day = 100;
$max_create_sessions_per_day = 1000;
$max_create_requests_per_day = 100;

// create table collection ( collection_id INTEGER PRIMARY KEY AUTO_INCREMENT, description TEXT, start_date DATE, end_date DATE, module_id INTEGER, password TEXT );
// create table module ( module_id INTEGER PRIMARY KEY AUTO_INCREMENT, title TEXT, description TEXT, form_url TEXT, report_url TEXT );

?>