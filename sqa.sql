-- phpMyAdmin SQL Dump
-- version 3.2.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 09, 2010 at 08:05 AM
-- Server version: 5.0.90
-- PHP Version: 5.2.9

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `anzsnmo_sqa`
--

-- --------------------------------------------------------

--
-- Table structure for table `collection`
--

CREATE TABLE IF NOT EXISTS `collection` (
  `collection_id` int(11) NOT NULL auto_increment,
  `scope` text,
  `start_date` date default NULL,
  `end_date` date default NULL,
  `module_id` int(11) default NULL,
  `password` text,
  `institution_id` int(11) default NULL,
  `visible` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`collection_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

-- --------------------------------------------------------

--
-- Table structure for table `institution`
--

CREATE TABLE IF NOT EXISTS `institution` (
  `institution_id` int(11) NOT NULL auto_increment,
  `title` text,
  `department` text,
  `contact_name` text,
  `phone_number` text,
  `email_address` text,
  PRIMARY KEY  (`institution_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Table structure for table `institution_auth`
--

CREATE TABLE IF NOT EXISTS `institution_auth` (
  `id` int(11) NOT NULL auto_increment,
  `institution_id` int(11) default NULL,
  `moodle_id` int(11) default NULL,
  `manage_priv` tinyint(1) default '0',
  `create_priv` tinyint(1) default '0',
  `visible_priv` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- Table structure for table `module`
--

CREATE TABLE IF NOT EXISTS `module` (
  `module_id` int(11) NOT NULL auto_increment,
  `title` text,
  `description` text,
  `info_url` text,
  `data_url` text,
  `report_url` text,
  `register_module` text,
  `import_module` text NOT NULL,
  `export_module` text NOT NULL,
  `visible` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`module_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Table structure for table `module_columns`
--

CREATE TABLE IF NOT EXISTS `module_columns` (
  `column_id` int(11) NOT NULL auto_increment,
  `type_id` int(11) default NULL,
  `title` text,
  PRIMARY KEY  (`column_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ;

-- --------------------------------------------------------

--
-- Table structure for table `module_data`
--

CREATE TABLE IF NOT EXISTS `module_data` (
  `data_id` int(11) NOT NULL auto_increment,
  `row_id` int(11) default NULL,
  `sheet_id` int(11) default NULL,
  `column_id` int(11) default NULL,
  `value` text,
  PRIMARY KEY  (`data_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4770 ;

-- --------------------------------------------------------

--
-- Table structure for table `module_sheets`
--

CREATE TABLE IF NOT EXISTS `module_sheets` (
  `sheet_id` int(11) NOT NULL auto_increment,
  `type_id` int(11) default NULL,
  `title` text,
  `number_of_rows` int(11) default '0',
  `moodle_id` int(11) default NULL,
  PRIMARY KEY  (`sheet_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=35 ;

-- --------------------------------------------------------

--
-- Table structure for table `module_sheet_type`
--

CREATE TABLE IF NOT EXISTS `module_sheet_type` (
  `type_id` int(11) NOT NULL auto_increment,
  `module_id` int(11) default NULL,
  `type_name` text,
  `number_of_columns` int(11) NOT NULL default '0',
  PRIMARY KEY  (`type_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Table structure for table `operator`
--

CREATE TABLE IF NOT EXISTS `operator` (
  `operator_id` int(11) NOT NULL auto_increment,
  `operator_code` text,
  `full_name` text,
  `phone_number` text,
  `email_address` text,
  `institution_id` int(11) default NULL,
  `registered` datetime default NULL,
  PRIMARY KEY  (`operator_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=11 ;

-- --------------------------------------------------------

--
-- Table structure for table `request`
--

CREATE TABLE IF NOT EXISTS `request` (
  `request_id` int(11) NOT NULL auto_increment,
  `institution_title` text,
  `department` text,
  `contact_name` text,
  `phone_number` text,
  `email_address` text,
  `scope` text,
  `start_date` date default NULL,
  `end_date` date default NULL,
  `module_id` int(11) default NULL,
  `password` text,
  `lodged` datetime default NULL,
  PRIMARY KEY  (`request_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Table structure for table `session`
--

CREATE TABLE IF NOT EXISTS `session` (
  `session_id` int(11) NOT NULL auto_increment,
  `session_code` text,
  `collection_id` int(11) default NULL,
  `operator_id` int(11) default NULL,
  `comments` text,
  `is_verified` tinyint(1) default NULL,
  `registered` datetime default NULL,
  PRIMARY KEY  (`session_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;
