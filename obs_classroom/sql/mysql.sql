#
# Table structure for table `cr_block`
#

CREATE TABLE `cr_block` (
  `blockid` int(12) unsigned NOT NULL auto_increment,
  `classroomid` int(12) unsigned NOT NULL default '0',
  `name` varchar(50) NOT NULL default '',
  `blocktypeid` int(12) unsigned NOT NULL default '0',
  PRIMARY KEY  (`blockid`)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table `cr_class`
#

CREATE TABLE `cr_class` (
  `classid` int(12) unsigned NOT NULL auto_increment,
  `classroomid` int(12) unsigned NOT NULL default '0',
  `name` varchar(100) NOT NULL default '',
  `time` varchar(255) default NULL,
  `description` text default NULL,
  `weight` tinyint(2) default 0,
  PRIMARY KEY  (`classid`)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table `cr_classblock`
#

CREATE TABLE `cr_classblock` (
  `classid` int(12) unsigned NOT NULL default '0',
  `blockid` int(12) unsigned NOT NULL default '0',
  `side` tinyint(2) unsigned NOT NULL default '0',
  `weight` mediumint(4) NOT NULL default '0',
  `visible` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`classid`,`blockid`)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table `cr_classroom`
#

CREATE TABLE `cr_classroom` (
  `classroomid` int(12) unsigned NOT NULL auto_increment,
  `divisionid` int(12) unsigned NOT NULL default '0',
  `name` varchar(100) NOT NULL default '',
  `owner` int(12) unsigned NOT NULL default '0',
  `description` text default NULL,
  `location` varchar(255) default NULL,
  `weight` tinyint(2) default 0,
  PRIMARY KEY  (`classroomid`)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table `cr_division`
#

CREATE TABLE `cr_division` (
  `divisionid` int(12) unsigned NOT NULL auto_increment,
  `schoolid` int(12) unsigned NOT NULL default '0',
  `name` varchar(100) NOT NULL default '',
  `description` text,
  `director` int(12) unsigned NOT NULL default '1',
  `location` varchar(255) default NULL,
  `weight` tinyint(2) default 0,
  PRIMARY KEY  (`divisionid`)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table `cr_school`
#

CREATE TABLE `cr_school` (
  `schoolid` int(12) unsigned NOT NULL auto_increment,
  `name` varchar(100) NOT NULL default '',
  `location` varchar(255) default NULL,
  `head` int(12) unsigned NOT NULL default '0',
  `description` text default NULL,
  `weight` tinyint(2) default 0,
  PRIMARY KEY  (`schoolid`)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table `cr_value`
#

CREATE TABLE `cr_value` (
  `fieldid` int(12) unsigned NOT NULL auto_increment,
  `blockid` int(12) unsigned NOT NULL default '0',
  `value` text NOT NULL,
  `weight` tinyint(4) unsigned NOT NULL default '0',
  `updated` int(12) unsigned default '0',
  PRIMARY KEY  (`fieldid`)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table `cr_lessonplanblock`
#

CREATE TABLE `cr_lessonplanblock` (
  `blockid` int(12) unsigned NOT NULL default '0',
  `date` int(12) unsigned NOT NULL,
  PRIMARY KEY  (`blockid`)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table `cr_lessonplan`
#

CREATE TABLE `cr_lessonplan` (
  `entryid` int(12) unsigned NOT NULL,
  `blockid` int(12) unsigned NOT NULL,
  `day` tinyint(2) unsigned NOT NULL default '1',
  PRIMARY KEY  (`entryid`)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table `cr_homework`
#

CREATE TABLE `cr_homework` (
  `fieldid` int(12) unsigned NOT NULL,
  `assigned` int(12) unsigned NOT NULL,
  `due` int(12) unsigned NOT NULL,
  PRIMARY KEY  (`fieldid`)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table `cr_rss`
#

CREATE TABLE `cr_rss` (
  `headline_id` int(12) unsigned NOT NULL auto_increment,
  `headline_name` varchar(255) NOT NULL default '',
  `headline_url` varchar(255) NOT NULL default '',
  `headline_rssurl` varchar(255) NOT NULL default '',
  `headline_encoding` varchar(15) NOT NULL default '',
  `headline_cachetime` mediumint(8) unsigned NOT NULL default '3600',
  `headline_display` tinyint(1) unsigned NOT NULL default '0',
  `headline_weight` smallint(3) unsigned NOT NULL default '0',
  `headline_blockmax` tinyint(2) unsigned NOT NULL default '10',
  `headline_xml` text NOT NULL default '',
  `headline_updated` int(10) NOT NULL default '0',
  `headline_blockid` int(12) NOT NULL,
  `headline_titlelength` int(4) NOT NULL default '25',
  PRIMARY KEY  (`headline_id`)
) TYPE=MyISAM;

CREATE TABLE `cr_question` (
  `questionid` int(12) unsigned NOT NULL,
  `optionno` varchar(15) NOT NULL default 'optiona',
  `optvalue` varchar(255) NOT NULL,
  `correct` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`questionid`, `optionno`)
) TYPE=MyISAM;