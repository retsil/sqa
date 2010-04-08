
-- --------------------------------------------------------

--
-- Table structure for table `muga_data`
--

CREATE TABLE IF NOT EXISTS `muga_data` (
  `data_id` int(11) NOT NULL auto_increment,
  `session_id` int(11) default NULL,
  `study_id` int(11) default NULL,
  `lvef` double default NULL,
  `end_diastolic_frame_number` int(11) default NULL,
  `end_systolic_frame_number` int(11) default NULL,
  `time_per_frame_ms` int(11) default NULL,
  `first_point_on_lv_curve` double default NULL,
  `is_valid` tinyint(1) default NULL,
  PRIMARY KEY  (`data_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=13 ;

-- --------------------------------------------------------

--
-- Table structure for table `muga_session`
--

CREATE TABLE IF NOT EXISTS `muga_session` (
  `session_id` int(11) NOT NULL,
  `manufacturer` text,
  `make_and_model` text,
  `name_of_software` text,
  `software_version` text,
  `type_of_software` text,
  `operator_experience_in_months` int(11) default NULL,
  `operator_frequency_per_month` int(11) default NULL,
  `normal_range_minimum` double default NULL,
  `normal_range_maximum` double default NULL,
  `number_of_frames_actually_analysed` int(11) default NULL,
  `region_of_interest_method` int(11) default NULL,
  `phase_images_used_for_ROI_definition` int(11) default NULL,
  `separate_systole_and_diastole_ROIs_used` int(11) default NULL,
  `background_subtraction_used` int(11) default NULL,
  `description_of_regions` text,
  `description_of_background_subtraction` text,
  `smoothing_type` int(11) default NULL,
  `smoothing_cycles` int(11) default NULL,
  `description_of_ejection_fraction_calculation` text,
  PRIMARY KEY  (`session_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
