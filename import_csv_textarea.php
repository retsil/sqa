<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
    "http://www.w3.org/TR/html4/loose.dtd">
<?php

require_once('settings.php');

$link = mysql_connect($db_server,$db_username,$db_password);
if (! mysql_select_db($db_database)) {
  die(mysql_error());
}

$rm = $_POST{'rm'};
if (empty($rm)) $rm = 'showtextarea';

?>
<html>

<body>


<?php 
if ($rm != 'showtable') print('<!-- ');
?>

Fill out the table below and hit convert to CSV 
<p>
<form method="post">

<?php 

print("<table>\n");
$lines = explode("\n",$_POST{'csv_data'});
$l=0;
$maxf=0;
foreach ($lines as $line) {
    print("<tr>");
    $f=0;
    $cols = explode(",",$line);
    foreach ($cols as $col) {
        $name = 'row' . $l . 'column' . $f;
        print("<td><input name='$name' type='text' value='$col'></td>");
        $f++;
        if ($f > $maxf) {
            $maxf = $f;
        }
    }
    $l++;
    print("</tr>");
}
print("</table>\n");

print("<input type='hidden' name='nrows' value='$l'>\n");
print("<input type='hidden' name='ncolumns' value='$maxf'>\n");

?>
<input type="hidden" name="rm" value="showtextarea">
<p>
<input type="submit" value="Convert to text">
</form>

<?php 
if ($rm != 'showtable') print('--> ');
?>


<?php 
if ($rm != 'showtextarea') print('<!-- ');

$nrows=$_POST{'nrows'};
$ncolumns=$_POST{'ncolumns'};

$csv_data=array_fill(0,$nrows,'');
for ($r = 0; $r < $nrows; $r++) {
    $csv_line = array_fill(0,$ncolumns,'');
    for ($c = 0; $c < $ncolumns; $c++) {
        $name = 'row' . $r . 'column' . $c;        
        $csv_line[$c] = $_POST{$name};
    }
    $csv_data[$r] = implode(',',$csv_line);
}
?>

Enter comma delimited data below and then hit convert to table:
<p>
<form method="post">
<textarea name="csv_data" rows="30" cols="80">
<?php
print(implode("\n",$csv_data));
?>
</textarea>
<input type="hidden" name="rm" value="showtable">
<p>
<input type="submit" value="Convert to table">
</form>

<?php 
if ($rm != 'showtextarea') print('--> ');
?>

</html>
