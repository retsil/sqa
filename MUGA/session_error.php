<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    	<style type="text/css" title="currentStyle" media="screen">
		@import "official_blue.css";
	</style>
  <title>Session error</title>
</head>
<body>
<?php
if ( $session_code_error != '') {
  echo '<span class="sqaerr">' . $session_code_error . '</span>';
}
?>
</body>
</html>