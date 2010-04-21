<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<?php

require_once('settings.php');

$link = mysql_connect($db_server,$db_username,$db_password);
if (! mysql_select_db($db_database)) {
  die(mysql_error());
}


?>
<html>
<head>
   	<style type="text/css" title="currentStyle" media="screen">
		@import "official_blue.css";
	</style>
  <title>Checked out</title>
</head>
<body>


<div class="sqamenu"><span class="sqamenu"><a href="index.php">Welcome</a></span> <span class="sqamenusel">Participate</span> <span class="sqamenu"><a href="admin_audits.php">Administrate</a></span></div>
<h1 class="sqa">Software Quality Assurance</h1>
  <div class="sqah1">&nbsp;</div>

<h2 class="sqa">Existing operator</h2>

  <form action="checked_in.php"><span class="sqaopf">Operator code:
<?php
if (preg_match('/^\w\w\w\w\w\w$/', $_COOKIE['operator_code'])) {
  echo '<input type="text" size=6 maxlength=6 name="operator_code" value="' .  $_COOKIE['operator_code'] . '" />';
} else {
  echo '<input type="text" size=6 maxlength=6 name="operator_code" />';
    }
?>
<button type="submit">Check in</button></span> 
<?php
if ( $operator_code_error != '') {
  echo '<span class="sqaerr">' . $operator_code_error . '</span>';
}
?>

</form>

<h2 class="sqa">New operator</h2>

  <div class="sqap">Check in as a new operator. (ie. if you don&#39;t have an operator code). Remembering your operator code is important for some audits. You may enter your details below if you want to. Your details are kept for recovery purposes only.
</div>

<FORM action="new_operator.php" METHOD="POST">
<FIELDSET class="sqaf">
  <LEGEND class="sqaf">Optional Details</LEGEND>
  <DIV class="sqaf">
	<SPAN class="sqaf">Full Name:</SPAN>
	<INPUT name="full_name" size="30" type="text" value="" tabindex="0"/>
  </DIV>
  <DIV class="sqaf">
	<SPAN class="sqaf">Email Address:</SPAN>
	<INPUT name="email_address" size="30" type="text" value="" tabindex="1"/>
  </DIV>
  <DIV class="sqaf">
	<SPAN class="sqaf">Phone Number:</SPAN>
	<INPUT name="phone_number" size="20" type="text"  value="" tabindex="2"/>
  </DIV>
  <DIV class="sqaf">
	<SPAN class="sqaf">Institution Name:</SPAN>
	<SELECT name="institution_id"  size="1" tabindex="4">
               <option value="0">Not listed</option>
               <option value="1">Westmead Hospital, Medical Physics</option>
        </SELECT>
  </DIV>
  <DIV class="sqaf">
	<SPAN class="sqaf">Notify me by email of my operator code:</SPAN>
	<INPUT name="email_back" type="checkbox" value="Notify" tabindex="1"/> 
  </DIV>
</FIELDSET>
<p>
<span class="sqaopf">Get ready to record your new operator code <button type="submit">Check in</button></span>
</p>
</FORM>



</body>
</html>
