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

<h2 class="sqa">The MUGA Audit</h2>
<div class="sqap">
  This data was obtained from the Institute of Physics and Engineering in Medicine (IPEM) Nuclear Medicine Software Working Party.
</div>
<div class="sqap">

The download below contains the data for the MUGA audit.  The data are in compressed form as a single file.  They have been compressed using pkzip.
</div>

<div class="sqap">
<a href="muga-interfile-data.zip">Download</a>
</div>

<div class="sqap">
The download contains 12 MUGA studies in interfile 3.3 format containing separate header and data files.  The header and data file names are p1muga.hdr, p1muga.img, p2muga.hdr etc.
the patient IDs are PATIENT_M1, PATIENT_M2 etc.  Each file consists of 32 frames of matrix size 64*64. 
As a check that the files are reading correctly the maximum count in the first frame of the first subject should be  93 .
</div>

</body>
</html>