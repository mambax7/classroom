<?php

define('_CR_MA_SUBMIT', 'Submit');
define('_CR_MA_EDIT', 'Edit');
define('_CR_MA_DELETE', 'Delete');
define('_CR_MA_HOME', 'Home');
define('_CR_MA_LOCATION', 'Location');
define('_CR_MA_MODERATORS', 'Moderators');

define('_CR_MA_ADDSCHOOL', 'Add School');
define('_CR_MA_EDITSCHOOL', 'Edit School');
define('_CR_MA_SCHOOLFORM', 'Add/Edit School');
define('_CR_MA_SCHOOLDELETED', 'School Deleted');
define('_CR_MA_SCHOOLSAVED', 'School Saved');
define('_CR_MA_SCHOOLNAME', 'School Name');
define('_CR_MA_HEADOFSCHOOL', 'Head of School');

define('_CR_MA_ADDDIVISION', 'Add Division');
define('_CR_MA_EDITDIVISION', 'Edit Division');
define('_CR_MA_DIVISIONFORM', 'Add/Edit Division');
define('_CR_MA_DIVISIONDELETED', 'Division Deleted');
define('_CR_MA_DIVISIONSAVED', 'Division Saved');
define('_CR_MA_SCHOOL', 'School');
define('_CR_MA_DESCRIPTION', 'Description');
define('_CR_MA_DIVISIONNAME', 'Division Name');
define('_CR_MA_DIRECTOR', 'Director');

define('_CR_MA_ADDCLASSROOM', 'Add Classroom');
define('_CR_MA_EDITCLASSROOM', 'Edit Classroom');
define('_CR_MA_CLASSROOMFORM', 'Add/Edit Classroom');
define('_CR_MA_CLASSROOMDELETED', 'Classroom Deleted');
define('_CR_MA_CLASSROOMSAVED', 'Classroom Saved');
define('_CR_MA_CLASSROOMNAME', 'Classroom Name');
define('_CR_MA_TEACHER', 'Teacher');

define('_CR_MA_ADDCLASS', 'Add Class');
define('_CR_MA_EDITCLASS', 'Edit Class');
define('_CR_MA_CLASSFORM', 'Add/Edit Class');
define('_CR_MA_CLASSDELETED', 'Class Deleted');
define('_CR_MA_CLASSSAVED', 'Class Saved');
define('_CR_MA_CLASSNAME', 'Class Name');
define('_CR_MA_TIME', 'Time');

define('_CR_MA_ADDBLOCK', 'Add Block');
define('_CR_MA_MANAGEBLOCKS', 'Manage Blocks');
define('_CR_MA_BLOCKFORM', 'Add Block');
define('_CR_MA_BLOCKDELETED', 'Block Deleted');
define('_CR_MA_BLOCKSAVED', 'Block Saved');
define('_CR_MA_BLOCKNAME', 'Block Name');
define('_CR_MA_BLOCKTYPE', 'Block Type');
define('_CR_MA_BACKTOCLASSROOM', 'Back to Classroom');
define('_CR_MA_EDITPOSITIONS', 'Set Block Positions');

define('_CR_MA_CLASSBLOCKFORM', 'Edit Block');
define('_CR_MA_CLASSBLOCKSAVED', 'Block Saved');
define('_CR_MA_BACKTOBLOCK', 'Back to Block Management');
define('_CR_MA_BELONGSIN', 'Belongs In');
define('_CR_MA_VISIBLEIN', 'Visible In');

define('_CR_MA_VISIBLE', 'Visible');
define('_CR_MA_POSITION', 'Position');
define('_CR_MA_WEIGHT', 'Weight');
define('_CR_MA_UPPERLEFT', 'Upper Left');
define('_CR_MA_UPPERRIGHT', 'Upper Right');
define('_CR_MA_CENTER', 'Center');
define('_CR_MA_LOWERLEFT', 'Lower Left');
define('_CR_MA_LOWERRIGHT', 'Lower Right');

define('_CR_MA_ITEMDELETED', 'Item Deleted Successfully');

// Confirmation Messages
define(
    '_CR_MA_SCHOOLITEMCOUNT',
    'Are you sure you want to IRREVERSIBLY DELETE %s? <br>
                                  This School has <br>
                                  %u Division(s)<br>
                                  %u Classroom(s)<br>
                                  %u Class(es) and<br>
                                  %u Block(s)'
);
define(
    '_CR_MA_DIVISIONITEMCOUNT',
    'Are you sure you want to IRREVERSIBLY DELETE %s? <br>
                                  This Division has <br>
                                  %u Classroom(s)<br>
                                  %u Class(es) and<br>
                                  %u Block(s)'
);
define(
    '_CR_MA_CLASSROOMITEMCOUNT',
    'Are you sure you want to IRREVERSIBLY DELETE %s? <br>
                                  This Classroom has <br>
                                  %u Class(es) and<br>
                                  %u Block(s)'
);
define('_CR_MA_AREYOUSUREDELCLASS', 'Are you sure you want to DELETE this class?');
define('_CR_MA_AREYOUSUREDELBLOCK', 'Are you sure you want to DELETE this block COMPLETELY?');

// Blocktype Messages
define('_CR_MA_LINK', 'Link Text');
define('_CR_MA_URL', 'Link URL');

define('_CR_MA_TEXT', 'Text');
define('_CR_MA_WEEKOF', 'Week of ');
define('_CR_MA_CURRENTENTRIES', 'Current Entries');

define('_CR_MA_MONDAY', 'Monday');
define('_CR_MA_TUESDAY', 'Tuesday');
define('_CR_MA_WEDNESDAY', 'Wednesday');
define('_CR_MA_THURSDAY', 'Thursday');
define('_CR_MA_FRIDAY', 'Friday');

define('_CR_MA_RULE', 'Class Rule');

define('_CR_MA_ASSIGNED', 'Assigned');
define('_CR_MA_ASSIGNMENT', 'Assignment');
define('_CR_MA_DUE', 'Due date');

define('_CR_MA_NAME', 'Name');
define('_CR_MA_FILE', 'File');
define('_CR_MA_CHOOSEFILE', 'Choose file to upload (max %u KB)');

define('_CR_MA_QUOTE', 'Quote');
define('_CR_MA_AUTHOR', 'Author');

define('_CR_MA_IMAGE', 'Image');

define('_CR_MA_FEEDNAME', 'Name');
define('_CR_MA_SITENAME', 'Name');
define('_CR_MA_URLEDFXML', 'RSS URL');
define('_CR_MA_ENCODING', 'Encoding');
define('_CR_MA_CACHETIME', 'Cache Time');
define('_CR_MA_DISPLAY', 'Display');
define('_CR_MA_DISPMAX', 'Max Items to Display');
define('_CR_MA_TITLELENGTH', 'Max Length of Title');

define('_CR_MA_QUESTION', 'Question');
define('_CR_MA_OPTIONA', 'Option A');
define('_CR_MA_OPTIONB', 'Option B');
define('_CR_MA_OPTIONC', 'Option C');
define('_CR_MA_OPTIOND', 'Option D');
define('_CR_MA_CORRECT', 'Correct Answer is:');
define('_CR_MA_GRADE', 'Grade');
define('_CR_MA_ANSWERS', 'Answers');
define('_CR_MA_YOURANSWER', 'You Answered');
define('_CR_MA_ANSWER_IS', 'The Answer is');
define('_CR_MA_ANSWER_CORRECT', 'Correct');
define('_CR_MA_ANSWER_WRONG', 'Wrong');
define('_CR_MA_QUESTIONCOUNT', 'Number of Questions');
define('_CR_MA_CORRECT_ANSWERS', 'Correct Answers');
define('_CR_MA_PERCENTAGE', 'Percentage');
define('_CR_MA_NOANSWER', 'You did not answer this question');

define('_CR_MA_WORD', 'Word');

define('_CR_MA_NOMETHODSET', 'No method is set');
define('_CR_MA_PLEASESETMETHOD', 'Please set your preferred method of receiving contact messages');
define('_CR_MA_CONTACTMETHOD', 'Contact Method');
define('_CR_MA_EMAIL', 'Email');
define('_CR_MA_MESSAGE', 'Message');
define('_CR_MA_CONTACTFORMSUBJECT', 'Contact Form');
define('_CR_MA_MESSAGESENTSUCCESSFULLY', 'Your message has been sent successfully');

// Error Messages
define('_CR_ER_NOSELECTION', 'Error - No Selection');

define('_CR_ER_SCHOOLNOTSAVED', 'Error - School Not Saved');
define('_CR_ER_NOSCHOOLSELECTED', 'Error - No School Selected');
define('_CR_ER_SCHOOLNOTDELETED', 'Error - School Not Deleted');

define('_CR_ER_NODIVSELECTED', 'Error - No Division Selected');
define('_CR_ER_DIVISIONNOTSAVED', 'Error - Division Not Saved');
define('_CR_ER_DIVISIONNOTDELETED', 'Error - Division Not Deleted');

define('_CR_ER_NOCLASSROOMSELECTED', 'Error - No Classroom Selected');
define('_CR_ER_CLASSROOMNOTSAVED', 'Error - Classroom Not Saved');
define('_CR_ER_CLASSROOMNOTDELETED', 'Error - Classroom Not Deleted');

define('_CR_ER_NOCLASSSELECTED', 'Error - No Class Selected');
define('_CR_ER_CLASSNOTSAVED', 'Error - Class Not Saved');
define('_CR_ER_CLASSNOTDELETED', 'Error - Class Not Deleted');

define('_CR_ER_NOBLOCKSELECTED', 'Error - No Block Selected');
define('_CR_ER_BLOCKNOTSAVED', 'Error - Block Not Saved');
define('_CR_ER_BLOCKNOTDELETED', 'Error - Block Not Deleted');

define('_CR_ER_ITEMNOTDELETED', 'Error - Item NOT Deleted');
define('_CR_ER_MESSAGENOTSENT', 'Error - Message NOT sent');
