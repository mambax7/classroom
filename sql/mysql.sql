#
# Table structure for table `cr_block`
#

CREATE TABLE `cr_block` (
    `blockid`     INT(12) UNSIGNED NOT NULL AUTO_INCREMENT,
    `classroomid` INT(12) UNSIGNED NOT NULL DEFAULT '0',
    `name`        VARCHAR(50)      NOT NULL DEFAULT '',
    `blocktypeid` INT(12) UNSIGNED NOT NULL DEFAULT '0',
    PRIMARY KEY (`blockid`)
)
    ENGINE = MyISAM;

# --------------------------------------------------------

#
# Table structure for table `cr_class`
#

CREATE TABLE `cr_class` (
    `classid`     INT(12) UNSIGNED NOT NULL AUTO_INCREMENT,
    `classroomid` INT(12) UNSIGNED NOT NULL DEFAULT '0',
    `name`        VARCHAR(100)     NOT NULL DEFAULT '',
    `time`        VARCHAR(255)              DEFAULT NULL,
    `description` TEXT,
    `weight`      TINYINT(2)                DEFAULT 0,
    PRIMARY KEY (`classid`)
)
    ENGINE = MyISAM;

# --------------------------------------------------------

#
# Table structure for table `cr_classblock`
#

CREATE TABLE `cr_classblock` (
    `classid` INT(12) UNSIGNED    NOT NULL DEFAULT '0',
    `blockid` INT(12) UNSIGNED    NOT NULL DEFAULT '0',
    `side`    TINYINT(2) UNSIGNED NOT NULL DEFAULT '0',
    `weight`  MEDIUMINT(4)        NOT NULL DEFAULT '0',
    `visible` TINYINT(1)          NOT NULL DEFAULT '0',
    PRIMARY KEY (`classid`, `blockid`)
)
    ENGINE = MyISAM;

# --------------------------------------------------------

#
# Table structure for table `cr_classroom`
#

CREATE TABLE `cr_classroom` (
    `classroomid` INT(12) UNSIGNED NOT NULL AUTO_INCREMENT,
    `divisionid`  INT(12) UNSIGNED NOT NULL DEFAULT '0',
    `name`        VARCHAR(100)     NOT NULL DEFAULT '',
    `owner`       INT(12) UNSIGNED NOT NULL DEFAULT '0',
    `description` TEXT,
    `location`    VARCHAR(255)              DEFAULT NULL,
    `weight`      TINYINT(2)                DEFAULT 0,
    PRIMARY KEY (`classroomid`)
)
    ENGINE = MyISAM;

# --------------------------------------------------------

#
# Table structure for table `cr_division`
#

CREATE TABLE `cr_division` (
    `divisionid`  INT(12) UNSIGNED NOT NULL AUTO_INCREMENT,
    `schoolid`    INT(12) UNSIGNED NOT NULL DEFAULT '0',
    `name`        VARCHAR(100)     NOT NULL DEFAULT '',
    `description` TEXT,
    `director`    INT(12) UNSIGNED NOT NULL DEFAULT '1',
    `location`    VARCHAR(255)              DEFAULT NULL,
    `weight`      TINYINT(2)                DEFAULT 0,
    PRIMARY KEY (`divisionid`)
)
    ENGINE = MyISAM;

# --------------------------------------------------------

#
# Table structure for table `cr_school`
#

CREATE TABLE `cr_school` (
    `schoolid`    INT(12) UNSIGNED NOT NULL AUTO_INCREMENT,
    `name`        VARCHAR(100)     NOT NULL DEFAULT '',
    `location`    VARCHAR(255)              DEFAULT NULL,
    `head`        INT(12) UNSIGNED NOT NULL DEFAULT '0',
    `description` TEXT,
    `weight`      TINYINT(2)                DEFAULT 0,
    PRIMARY KEY (`schoolid`)
)
    ENGINE = MyISAM;

# --------------------------------------------------------

#
# Table structure for table `cr_value`
#

CREATE TABLE `cr_value` (
    `fieldid` INT(12) UNSIGNED    NOT NULL AUTO_INCREMENT,
    `blockid` INT(12) UNSIGNED    NOT NULL DEFAULT '0',
    `value`   TEXT                NOT NULL,
    `weight`  TINYINT(4) UNSIGNED NOT NULL DEFAULT '0',
    `updated` INT(12) UNSIGNED             DEFAULT '0',
    PRIMARY KEY (`fieldid`)
)
    ENGINE = MyISAM;

# --------------------------------------------------------

#
# Table structure for table `cr_lessonplanblock`
#

CREATE TABLE `cr_lessonplanblock` (
    `blockid` INT(12) UNSIGNED NOT NULL DEFAULT '0',
    `date`    INT(12) UNSIGNED NOT NULL,
    PRIMARY KEY (`blockid`)
)
    ENGINE = MyISAM;

# --------------------------------------------------------

#
# Table structure for table `cr_lessonplan`
#

CREATE TABLE `cr_lessonplan` (
    `entryid` INT(12) UNSIGNED    NOT NULL,
    `blockid` INT(12) UNSIGNED    NOT NULL,
    `day`     TINYINT(2) UNSIGNED NOT NULL DEFAULT '1',
    PRIMARY KEY (`entryid`)
)
    ENGINE = MyISAM;

# --------------------------------------------------------

#
# Table structure for table `cr_homework`
#

CREATE TABLE `cr_homework` (
    `fieldid`  INT(12) UNSIGNED NOT NULL,
    `assigned` INT(12) UNSIGNED NOT NULL,
    `due`      INT(12) UNSIGNED NOT NULL,
    PRIMARY KEY (`fieldid`)
)
    ENGINE = MyISAM;

# --------------------------------------------------------

#
# Table structure for table `cr_rss`
#

CREATE TABLE `cr_rss` (
    `headline_id`          INT(12) UNSIGNED      NOT NULL AUTO_INCREMENT,
    `headline_name`        VARCHAR(255)          NOT NULL DEFAULT '',
    `headline_url`         VARCHAR(255)          NOT NULL DEFAULT '',
    `headline_rssurl`      VARCHAR(255)          NOT NULL DEFAULT '',
    `headline_encoding`    VARCHAR(15)           NOT NULL DEFAULT '',
    `headline_cachetime`   MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '3600',
    `headline_display`     TINYINT(1) UNSIGNED   NOT NULL DEFAULT '0',
    `headline_weight`      SMALLINT(3) UNSIGNED  NOT NULL DEFAULT '0',
    `headline_blockmax`    TINYINT(2) UNSIGNED   NOT NULL DEFAULT '10',
    `headline_xml`         TEXT                  NOT NULL,
    `headline_updated`     INT(10)               NOT NULL DEFAULT '0',
    `headline_blockid`     INT(12)               NOT NULL,
    `headline_titlelength` INT(4)                NOT NULL DEFAULT '25',
    PRIMARY KEY (`headline_id`)
)
    ENGINE = MyISAM;

CREATE TABLE `cr_question` (
    `questionid` INT(12) UNSIGNED NOT NULL,
    `optionno`   VARCHAR(15)      NOT NULL DEFAULT 'optiona',
    `optvalue`   VARCHAR(255)     NOT NULL,
    `correct`    TINYINT(1)       NOT NULL DEFAULT '0',
    PRIMARY KEY (`questionid`, `optionno`)
)
    ENGINE = MyISAM;
