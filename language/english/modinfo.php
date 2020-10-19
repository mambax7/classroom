<?php

define('_CR_MI_NAME', 'ClassRoom');
define('_CR_MI_DESC', 'Module to manage schools with divisions and classrooms and present content for each classroom');

define('_CR_MI_BL_EDITMODE', 'Edit Mode');

define('_MI_HEADGROUP', 'Head of School');
define('_MI_HEADGROUPDESC', 'These usergroups will be selectable for head of school');
define('_MI_DIRECTORGROUP', 'Division Director');
define('_MI_DIRECTORGROUPDESC', 'These usergroups will be selectable for division directors');
define('_MI_TEACHERGROUP', 'Teachers');
define('_MI_TEACHERGROUPDESC', 'These usergroups will be selectable as teachers for classrooms');
define('_MI_FRONTPAGETEXT', 'Front Page Text');
define('_MI_FRONTPAGETEXT_DESC', 'This text will be displayed on the front page above the list of schools');
define('_MI_MAXFILESIZE', 'Maximum file size');
define('_MI_MAXFILESIZE_DESC', 'Set maximum file size in KiloBytes for Downloads block');

// The name of this module
define('_MI_CLASSROOM_NAME', _CR_MI_NAME);
define('_MI_CLASSROOM_DESC', _CR_MI_DESC);

//Menu
define('_MI_CLASSROOM_MENU_HOME', 'Home');
define('_MI_CLASSROOM_MENU_01', 'Admin');
define('_MI_CLASSROOM_MENU_ABOUT', 'About');

//Config
define('_MI_CLASSROOM_EDITOR_ADMIN', 'Editor: Admin');
define('_MI_CLASSROOM_EDITOR_ADMIN_DESC', 'Select the Editor to use by the Admin');
define('_MI_CLASSROOM_EDITOR_USER', 'Editor: User');
define('_MI_CLASSROOM_EDITOR_USER_DESC', 'Select the Editor to use by the User');

//Help
define('_MI_CLASSROOM_DIRNAME', basename(dirname(__DIR__, 2)));
define('_MI_CLASSROOM_HELP_HEADER', __DIR__ . '/help/helpheader.tpl');
define('_MI_CLASSROOM_BACK_2_ADMIN', 'Back to Administration of ');
define('_MI_CLASSROOM_OVERVIEW', 'Overview');

//define('_MI_CLASSROOM_HELP_DIR', __DIR__);

//help multi-page
define('_MI_CLASSROOM_DISCLAIMER', 'Disclaimer');
define('_MI_CLASSROOM_LICENSE', 'License');
define('_MI_CLASSROOM_SUPPORT', 'Support');
