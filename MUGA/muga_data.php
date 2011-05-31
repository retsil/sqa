<?php

require_once('../settings.php');

$link = mysql_connect($db_server,$db_username,$db_password);
if (! mysql_select_db($db_database)) {
  die(mysql_error());
}

require_once('../session.inc.php');

$session_id = check_session(-1);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
    "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
   	<style type="text/css" title="currentStyle" media="screen">
		@import "../official_blue.css";
	</style>
  <title>Data</title>
</head>
<body>






<?php 



  $query = sprintf('SELECT manufacturer, make_and_model, name_of_software, software_version, type_of_software, operator_experience_in_months, operator_frequency_per_month, normal_range_minimum, normal_range_maximum, number_of_frames_actually_analysed, region_of_interest_method, phase_images_used_for_ROI_definition, separate_systole_and_diastole_ROIs_used, background_subtraction_used, description_of_regions, description_of_background_subtraction, smoothing_type, smoothing_cycles, description_of_ejection_fraction_calculation FROM muga_session WHERE session_id=%d',$session_id);
$result = my_query($query);

$row = mysql_fetch_assoc($result);


//  update `module` set `import_module`='MUGA/muga_import.php', `export_module`='MUGA/muga_export.php', data_url='MUGA/muga_study.php' WHERE title='MUGA';

?>

<div class="sqamenu"><span class="sqamenu"><a href="../index.php">Welcome</a></span> <span class="sqamenu"><a href="../checked_out.php">Participate</a></span> <span class="sqamenusel">MUGA</span></div>

<h1 class="sqa">Software Quality Assurance</h1>
  <div class="sqah1">&nbsp;</div>


  <h2 class="sqa">MUGA data entry [ <a href="muga_study.php?session_code=<?php echo session_code(); ?>">Part 1</a> | Part 2 |
<A href="../checked_in.php?session_code=<?php echo session_code(); ?>">Exit</a> ]</h2>

<form action="muga_data.php?session_code=<?php echo session_code(); ?>" method="POST">

<FIELDSET class="sqaf">
  <LEGEND class="sqaf">Computer System and Software</LEGEND>

  <DIV class="sqaf">
  <SPAN class="sqaf">Manufacturer: </SPAN>
  <?php $row = update_field('muga_session','manufacturer','/^.*$/',true,'session_id',$session_id,$row); ?>
<INPUT name="manufacturer" size="24" type="text" value="<?php echo htmlspecialchars($row['manufacturer']); ?>" tabindex="" />
  </DIV>
  <DIV class="sqaf">
  <SPAN class="sqaf">Make and model: </SPAN>
  <?php $row = update_field('muga_session','make_and_model','/^.*$/',true,'session_id',$session_id,$row); ?>
  <INPUT name="make_and_model" size="24" type="text" value="<?php echo htmlspecialchars($row['make_and_model']); ?>" tabindex="" />
  </DIV>
  <DIV class="sqaf">
  <SPAN class="sqaf">Name of software: </SPAN>
  <?php $row = update_field('muga_session','name_of_software','/^.*$/',true,'session_id',$session_id,$row); ?>
  <INPUT name="name_of_software" size="24" type="text" value="<?php echo htmlspecialchars($row['name_of_software']); ?>" tabindex="" />
  </DIV>
  <DIV class="sqaf">
  <SPAN class="sqaf">Software version: </SPAN>
  <?php $row = update_field('muga_session','software_version','/^.*$/',true,'session_id',$session_id,$row); ?>
  <INPUT name="software_version" size="24" type="text" value="<?php echo htmlspecialchars($row['software_version']); ?>" tabindex="" />
  </DIV>
  <DIV class="sqaf">
  <SPAN class="sqaf">Type of software:</SPAN>
  <div style="margin-left: 3em;">
  <?php update_radio_input('muga_session','type_of_software',array('commercial' => 'commercial', 'inhouse' => 'in-house', 'other' => 'other'),true,'session_id',$session_id,$row);  ?>
  </div>
  </DIV>

  </FIELDSET>

  <?php

  ?>


  <FIELDSET class="sqaf">
  <LEGEND class="sqaf">Operator Experience</LEGEND>

  <?php 

  ?>
  <DIV class="sqaf">
  <SPAN class="sqaf">How long have you been processing MUGA scans?</SPAN>
  <div style="margin-left: 3em;">
  <?php update_radio_input('muga_session','operator_experience_in_months',
			   array('0' => 'less than 6 months', '6' => '6 months to 1 year', '12' => 'longer than 1 year'),false,'session_id',$session_id,$row);  ?>
  </div>

  </DIV>
  <DIV class="sqaf">

  <SPAN class="sqaf">How many scans do you process per month?</SPAN>
					 <div style="margin-left: 3em;">
 <?php update_radio_input('muga_session','operator_frequency_per_month',
			   array('1' => ' 1 - 10', '11' => '11 - 30', '31' => '31+'),true,'session_id',$session_id,$row);  ?>
  </div>

					 </DIV>
 


					 </FIELDSET>

  
  <DIV class="sqaf">
  <SPAN class="sqaf">

					 <FIELDSET class="sqaf">
					 <LEGEND class="sqaf">Normal Range</LEGEND>

					 <?php 

					 ?>
					 <DIV class="sqaf">
					 <SPAN class="sqaf">Minimum of normal range for your site (%):</SPAN>
  <?php $row = update_field('muga_session','normal_range_minimum','/^\d+$/',false,'session_id',$session_id,$row); ?>
  <INPUT name="normal_range_minimum" size="4" type="text" value="<?php echo $row['normal_range_minimum']; ?>" tabindex="" />
  </DIV>
  <DIV class="sqaf">
  <SPAN class="sqaf">Maximum of normal range for your site (%):</SPAN>
  <?php $row = update_field('muga_session','normal_range_maximum','/^\d+$/',false,'session_id',$session_id,$row); ?>
  <INPUT name="normal_range_maximum" size="4" type="text" value="<?php echo $row['normal_range_maximum']; ?>" tabindex="" />
  </DIV>



  </FIELDSET>



  <FIELDSET class="sqaf">
  <LEGEND class="sqaf">Details of analysis</LEGEND>

  <?php 

  ?>
  <DIV class="sqaf">
  <SPAN class="sqaf">Number of frames actually analysed: </SPAN>
  <div style="margin-left: 3em;">
  <?php update_radio_input('muga_session','number_of_frames_actually_analysed',
			   array('16' => '16', '32' => '32'),false,'session_id',$session_id,$row);  ?>
  </div>
  </DIV>
  <DIV class="sqaf">
  <SPAN class="sqaf">Region of interest method:</SPAN>
  <div style="margin-left: 3em;">
  <?php update_radio_input('muga_session','region_of_interest_method',
			   array('0' => 'manual', '1' => 'semiautomatic', '2' => 'fully-automatic'),false,'session_id',$session_id,$row);  ?>

  </div>
  </DIV>
  <DIV class="sqaf">
  <SPAN class="sqaf">Were phase images used for ROI definition?</SPAN>
  <div style="margin-left: 3em;">
  <?php update_radio_input('muga_session','phase_images_used_for_ROI_definition',
			   array('0' => 'no', '1' => 'yes', '2' => 'don&apos;t know'),false,'session_id',$session_id,$row);  ?>
  </div>
  </DIV>
  <DIV class="sqaf">

  <SPAN class="sqaf">Were separate systole and diastole ROIs used?</SPAN>
  <div style="margin-left: 3em;">
  <?php update_radio_input('muga_session','separate_systole_and_diastole_ROIs_used',
			   array('0' => 'no', '1' => 'yes', '2' => 'don&apos;t know'),false,'session_id',$session_id,$row);  ?>
  
  </div>
  </DIV>
  <DIV class="sqaf">
  <SPAN class="sqaf">Was background subtraction used?</SPAN>
																		   <div style="margin-left: 3em;">
 <?php update_radio_input('muga_session','background_subtraction_used',
			   array('0' => 'no', '1' => 'yes'),false,'session_id',$session_id,$row);  ?>
	
																		   </DIV>
																		   </div>
																		   <DIV class="sqaf">
																		   <SPAN class="sqaf">Please describe how background and ventricular regions are defined (if known):</SPAN>
 <?php $row = update_field('muga_session','description_of_regions','//',true,'session_id',$session_id,$row); ?>

  <TEXTAREA NAME="description_of_regions" ROWS=7 COLS=50><?php echo htmlspecialchars($row['description_of_regions']); ?></TEXTAREA>
  </DIV>

  <DIV class="sqaf">
  <SPAN class="sqaf">Please describe the form of background subtraction used:</SPAN>
 <?php $row = update_field('muga_session','description_of_background_subtraction','//',true,'session_id',$session_id,$row); ?>

  <TEXTAREA NAME="description_of_background_subtraction" ROWS=7 COLS=50><?php echo htmlspecialchars($row['description_of_background_subtraction']); ?></TEXTAREA>

  </DIV>
  <DIV class="sqaf">
  <SPAN class="sqaf">What type of smoothing was used (if any)?</SPAN>
  <div style="margin-left: 3em;">
   <?php update_radio_input('muga_session','smoothing_type',
			   array('0' => 'No smoothing', '1' => 'Temporal', '2' => 'Spatial', '3' => 'Spatial and Temporal'),false,'session_id',$session_id,$row);  ?>
  </DIV>
  </div>
  <DIV class="sqaf">
  <SPAN class="sqaf">How many smoothing cycles were performed?</SPAN>
  <?php $row = update_field('muga_session','smoothing_cycles','/^\d+$/',false,'session_id',$session_id,$row); ?>

  <INPUT name="smoothing_cycles" size="4" type="text" value="<?php echo htmlspecialchars($row['smoothing_cycles']); ?>" tabindex="" />
  </DIV>
  <DIV class="sqaf">
  <SPAN class="sqaf">Please describe briefly how the ejection fraction (LVEF) is calculated from the LVcurve.</SPAN>
 <?php $row = update_field('muga_session','description_of_ejection_fraction_calculation','//',true,'session_id',$session_id,$row); ?>
 
 <TEXTAREA NAME="description_of_ejection_fraction_calculation" ROWS=7 COLS=50><?php echo htmlspecialchars($row['description_of_ejection_fraction_calculation']); ?></TEXTAREA>
  </DIV> 
  </FIELDSET>

   <div class="sqaf"><button type="submit" name="update" value="1">Save</button></div>

  </form>

  </body>
  </html>
