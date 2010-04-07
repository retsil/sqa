<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
    "http://www.w3.org/TR/html4/loose.dtd">
<?php

require_once('../settings.php');

$link = mysql_connect($db_server,$db_username,$db_password);
if (! mysql_select_db($db_database)) {
  die(mysql_error());
}

?>
<html>
<head>
   	<style type="text/css" title="currentStyle" media="screen">
		@import "../official_blue.css";
	</style>
  <title>Software Quality Assurance</title>
</head>
<body>


<div class="sqamenu"><span class="sqamenu"><a href="../index.php">Welcome</a></span> <span class="sqamenu"><a href="../checked_out.php">Participate</a></span> <span class="sqamenusel">MUGA</span></div>


<h1 class="sqa">Software Quality Assurance</h1>
  <div class="sqah1">&nbsp;</div>

<h2 class="sqa">The MUGA Scan</h2>
<div class="sqap">Some information here</div>

</body>
</html>