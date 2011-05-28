<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
    "http://www.w3.org/TR/html4/loose.dtd">
<?php

require_once('../settings.php');

$link = mysql_connect($db_server,$db_username,$db_password);
if (! mysql_select_db($db_database)) {
  die(mysql_error());
}

require_once('../session.inc.php');

$session_id = check_session(-1);

?>

<html>
<head>
   	<style type="text/css" title="currentStyle" media="screen">
		@import "../official_blue.css";
	</style>
  <title>Data</title>
</head>
<body>

<form action="wwsf_data.php?session_code=<?php echo session_code(); ?>" method="POST">


<?php 



  $query = sprintf('SELECT  workstation,
 workstation_other,
 software,
 version,
 tracer_used, 
 tracer_used_other, 
 background_roi, 
 background_other, 
 calculation_method, 
 calculation_method_other FROM wwsf_session WHERE session_id=%d',$session_id);

$result = my_query($query);

$row = mysql_fetch_assoc($result);


 
?>

<div class="sqamenu"><span class="sqamenu"><a href="../index.php">Welcome</a></span> <span class="sqamenu"><a href="../checked_out.php">Participate</a></span> <span class="sqamenusel">Split Function</span></div>

<h1 class="sqa">Software Quality Assurance</h1>
  <div class="sqah1">&nbsp;</div>



 <FIELDSET class="sqaf"> 
  <LEGEND class="sqaf">Computer and software</LEGEND>

  <DIV class="sqaf">
  <SPAN class="sqaf">Workstation (Company):</SPAN>
  <div style="margin-left: 3em;">
  <?php update_radio_input('wwsf_session','workstation',array('1' => 'GE', '2' => 'Hermes', '3' => 'Mediso', '4' => 'Philips', '5' => 'Siemens', '6' => 'Toshiba', '7' => 'Other'),true,'session_id',$session_id,$row);  ?>
  </div>
  <?php $row = update_field('wwsf_session','workstation_other','/^.*$/',true,'session_id',$session_id,$row); ?>
<INPUT name="workstation_other" size="24" type="text" value="<?php echo htmlspecialchars($row['workstation_other']); ?>"  />
 </div>
  <DIV class="sqaf">&nbsp;</DIV>

<DIV class="sqaf">
  <SPAN class="sqaf">Software: </SPAN>
  <?php $row = update_field('wwsf_session','software','/^.*$/',true,'session_id',$session_id,$row); ?>
  <INPUT name="software" size="24" type="text" value="<?php echo htmlspecialchars($row['software']); ?>"  />
  </DIV>


<DIV class="sqaf">
  <SPAN class="sqaf">Version: </SPAN>
  <?php $row = update_field('wwsf_session','version','/^.*$/',true,'session_id',$session_id,$row); ?>
  <INPUT name="version" size="24" type="text" value="<?php echo htmlspecialchars($row['version']); ?>"  />
  </DIV>

 </FIELDSET>

 <FIELDSET class="sqaf"> 

  <LEGEND class="sqaf">Renography</LEGEND>

  <DIV class="sqaf">
  <SPAN class="sqaf">Tracer you routinely use for renography:</SPAN>
  <div style="margin-left: 3em;">
  <?php update_radio_input('wwsf_session','tracer_used',array('1' => 'MAG3','2' => 'EC','3' => 'HIPPURTE','4' => 'DTPA','5' => 'Other',),true,'session_id',$session_id,$row);  ?>
  </div>
  <?php $row = update_field('wwsf_session','tracer_used_other','/^.*$/',true,'session_id',$session_id,$row); ?>
<INPUT name="tracer_used_other" size="24" type="text" value="<?php echo htmlspecialchars($row['tracer_used_other']); ?>"  />
 </div>
  <DIV class="sqaf">&nbsp;</DIV>


  <DIV class="sqaf">
  <SPAN class="sqaf">Background ROI:</SPAN>
  <div style="margin-left: 3em;">
  <?php update_radio_input('wwsf_session','background_roi',array('1' => 'Subrenal','2' => 'Perirenal','3' => 'Suprarenal','3' => 'Surrounding the kidney','4' => 'None', '5' => 'Other'),true,'session_id',$session_id,$row);  ?>
  </div>
  <?php $row = update_field('wwsf_session','background_other','/^.*$/',true,'session_id',$session_id,$row); ?>
<INPUT name="background_other" size="24" type="text" value="<?php echo htmlspecialchars($row['background_other']); ?>"  />
  </DIV>
  <img src="roi_placement.png">
  <DIV class="sqaf">&nbsp;</DIV>

  <DIV class="sqaf">
  <SPAN class="sqaf">Calculation Method:</SPAN>
  <div style="margin-left: 3em;">
  <?php update_radio_input('wwsf_session','calculation_method',array('1' => 'Integral 1-2 Minute','2' => 'Integral 1-3 Minute','3' => 'Integral 2-3 Minute','4' => 'Patlak','5' => 'Other',),true,'session_id',$session_id,$row);  ?>
  </div>
  <?php $row = update_field('wwsf_session','calculation_method_other','/^.*$/',true,'session_id',$session_id,$row); ?>
<INPUT name="calculation_method_other" size="24" type="text" value="<?php echo htmlspecialchars($row['calculation_method_other']); ?>"  />
  </DIV>
  <DIV class="sqaf">&nbsp;</DIV>

 </FIELDSET>

  <?php

  ?>

  <button type="submit" name="update" value="1">Save</button> and start entering results<br>
</span></div>

 <iframe width=550 height=300 src="wwsf_study.php?session_code=<?php echo session_code(); ?>"></iframe>

  <DIV class="sqaf">
  <button type="submit" name="update" value="1">Save</button>

<span class="sqamenu"><a href="../checked_out.php">Exit</a></span>

</div>

  </form>

  </body>
  </html>
