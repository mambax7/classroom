<?php
// $Id: xoops_version.php
//  ------------------------------------------------------------------------ //
//                XOOPS - PHP Content Management System                      //
//                    Copyright (c) 2000 XOOPS.org                           //
//                       <https://xoops.org>                             //
//  ------------------------------------------------------------------------ //
//  This program is free software; you can redistribute it and/or modify     //
//  it under the terms of the GNU General Public License as published by     //
//  the Free Software Foundation; either version 2 of the License, or        //
//  (at your option) any later version.                                      //
//                                                                           //
//  You may not change or alter any portion of this comment or credits       //
//  of supporting developers from this source code or any supporting         //
//  source code which is considered copyrighted (c) material of the          //
//  original comment or credit authors.                                      //
//                                                                           //
//  This program is distributed in the hope that it will be useful,          //
//  but WITHOUT ANY WARRANTY; without even the implied warranty of           //
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
//  GNU General Public License for more details.                             //
//                                                                           //
//  You should have received a copy of the GNU General Public License        //
//  along with this program; if not, write to the Free Software              //
//  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA //
//  ------------------------------------------------------------------------ //
//  Author:  Mithrandir                                                      //

$moduleDirName      = basename(__DIR__);
$moduleDirNameUpper = mb_strtoupper($moduleDirName);

$modversion['version']             = 1.10;
$modversion['module_status']       = 'Beta 1';
$modversion['release_date']        = '2020/07/07';
$modversion['name']                = _CR_MI_NAME;
$modversion['description']         = _CR_MI_DESC;
$modversion['credits']             = 'The XOOPS Project';
$modversion['help']                = 'classroom.html';
$modversion['license']             = 'GPL see LICENSE';
$modversion['official']            = 0;
$modversion['image']               = 'assets/images/logoModule.png';
$modversion['help']                = 'page=help';
$modversion['license']             = 'GNU GPL 2.0 or later';
$modversion['license_url']         = 'www.gnu.org/licenses/gpl-2.0.html';
$moduleDirName                     = basename(__DIR__);
$modversion['dirname']             = $moduleDirName;
$modversion['modicons16']          = 'assets/images/icons/16';
$modversion['modicons32']          = 'assets/images/icons/32';
$modversion['module_website_url']  = 'www.xoops.org';
$modversion['module_website_name'] = 'XOOPS';
$modversion['min_php']             = '7.1';
$modversion['min_xoops']           = '2.5.10';
$modversion['min_admin']           = '1.2';
$modversion['min_db']              = ['mysql' => '5.5'];
$modversion['system_menu']         = 1;
$modversion['adminindex']          = 'admin/index.php';
$modversion['adminmenu']           = 'admin/menu.php';

// Sql file (must contain sql generated by phpMyAdmin or phpPgAdmin)
// All tables should not have any prefix!
// ------------------- Mysql ------------------- //
$modversion['sqlfile']['mysql'] = 'sql/mysql.sql';
// Tables created by sql file (without prefix!)
$modversion['tables'] = [
    $moduleDirName . '_' . 'school',
    $moduleDirName . '_' . 'division',
    $moduleDirName . '_' . 'classroom',
    $moduleDirName . '_' . 'block',
    $moduleDirName . '_' . 'class',
    $moduleDirName . '_' . 'classblock',
    $moduleDirName . '_' . 'lessonplan',
    $moduleDirName . '_' . 'lessonplanblock',
    $moduleDirName . '_' . 'homework',
    $moduleDirName . '_' . 'value',
    $moduleDirName . '_' . 'rss',
    $moduleDirName . '_' . 'question',
];

// Admin things
$modversion['hasAdmin']    = 1;
$modversion['system_menu'] = 1;
$modversion['adminindex']  = 'admin/index.php';
$modversion['adminmenu']   = 'admin/menu.php';

// ------------------- Help files ------------------- //
$modversion['help']        = 'page=help';
$modversion['helpsection'] = [
    ['name' => _MI_CLASSROOM_OVERVIEW, 'link' => 'page=help'],
    ['name' => _MI_CLASSROOM_DISCLAIMER, 'link' => 'page=disclaimer'],
    ['name' => _MI_CLASSROOM_LICENSE, 'link' => 'page=license'],
    ['name' => _MI_CLASSROOM_SUPPORT, 'link' => 'page=support'],
];

// Templates

$modversion['templates'] = [
    ['file' => 'cr_overview.tpl', 'description' => 'List Schools'],
    ['file' => 'cr_school.tpl', 'description' => 'List Divisions in School'],
    ['file' => 'cr_division.tpl', 'description' => 'List Classrooms in Division'],
    ['file' => 'cr_classroom.tpl', 'description' => 'List Classes in Classroom'],
    ['file' => 'cr_class.tpl', 'description' => 'Display Class'],
    ['file' => 'cr_block.tpl', 'description' => 'Display Block'],
    ['file' => 'cr_blocktype_textfield.tpl', 'description' => 'Show a textfield block'],
    ['file' => 'cr_blocktype_lessonplan.tpl', 'description' => 'Show a lessonplan block'],
    ['file' => 'cr_blocktype_link.tpl', 'description' => 'Show a web link block'],
    ['file' => 'cr_blocktype_classrules.tpl', 'description' => 'Show a class rules block'],
    ['file' => 'cr_blocktype_homework.tpl', 'description' => 'Show a homework block'],
    ['file' => 'cr_blocktype_downloads.tpl', 'description' => 'Show a handout downloads block'],
    ['file' => 'cr_blocktype_quote.tpl', 'description' => 'Show a random quote block'],
    ['file' => 'cr_blocktype_image.tpl', 'description' => 'Show a random image block'],
    ['file' => 'cr_blocktype_rss.tpl', 'description' => 'Show a RSS Feed block'],
    ['file' => 'cr_blocktype_quiz.tpl', 'description' => 'Show a Practice Quiz block'],
    ['file' => 'cr_interact_quiz.tpl', 'description' => 'Show results from a Practice Quiz block'],
    ['file' => 'cr_blocktype_spelling.tpl', 'description' => 'Show a Spelling List block'],
    ['file' => 'cr_blocktype_spelling_details.tpl', 'description' => 'Show a Spelling Term Explained'],
    ['file' => 'cr_blocktype_contact.tpl', 'description' => 'Show a Contact Teacher block'],
];

// Blocks
$modversion['blocks'][] = [
    'file'        => 'edit_mode.php',
    'name'        => _CR_MI_BL_EDITMODE,
    'description' => 'Sets edit mode on/off',
    'show_func'   => 'b_editmode_show',
    'template'    => 'cr_block_editmode.tpl',
];

// Classroom Blocktypes
$modversion['blocktypes'][1]['name']  = 'Textfield';
$modversion['blocktypes'][2]['name']  = 'Lessonplan';
$modversion['blocktypes'][3]['name']  = 'Link';
$modversion['blocktypes'][4]['name']  = 'Classrules';
$modversion['blocktypes'][5]['name']  = 'Homework';
$modversion['blocktypes'][6]['name']  = 'Downloads';
$modversion['blocktypes'][7]['name']  = 'Quote';
$modversion['blocktypes'][8]['name']  = 'Image';
$modversion['blocktypes'][9]['name']  = 'Rss';
$modversion['blocktypes'][10]['name'] = 'Quiz';
$modversion['blocktypes'][11]['name'] = 'SpellingList';
$modversion['blocktypes'][12]['name'] = 'Contact';

// Menu
$modversion['hasMain'] = 1;

// Search
$modversion['hasSearch']      = 1;
$modversion['search']['file'] = 'include/search.inc.php';
$modversion['search']['func'] = 'cr_search';

$modversion['config'][] = [
    'name'        => 'head_group',
    'title'       => '_MI_HEADGROUP',
    'description' => '_MI_HEADGROUPDESC',
    'formtype'    => 'group_multi',
    'valuetype'   => 'array',
    'default'     => [1],
];

$modversion['config'][] = [
    'name'        => 'director_group',
    'title'       => '_MI_DIRECTORGROUP',
    'description' => '_MI_DIRECTORGROUPDESC',
    'formtype'    => 'group_multi',
    'valuetype'   => 'array',
    'default'     => [1],
];

$modversion['config'][] = [
    'name'        => 'teacher_group',
    'title'       => '_MI_TEACHERGROUP',
    'description' => '_MI_TEACHERGROUPDESC',
    'formtype'    => 'group_multi',
    'valuetype'   => 'array',
    'default'     => [1],
];

$modversion['config'][] = [
    'name'        => 'frontpagetext',
    'title'       => '_MI_FRONTPAGETEXT',
    'description' => '_MI_FRONTPAGETEXT_DESC',
    'formtype'    => 'textarea',
    'valuetype'   => 'text',
    'default'     => '',
];

$modversion['config'][] = [
    'name'        => 'max_file_size',
    'title'       => '_MI_MAXFILESIZE',
    'description' => '_MI_MAXFILESIZE_DESC',
    'formtype'    => 'text',
    'valuetype'   => 'int',
    'default'     => '1024',
];

// default admin editor
xoops_load('XoopsEditorHandler');
$editorHandler = \XoopsEditorHandler::getInstance();
$editorList    = array_flip($editorHandler->getList());

$modversion['config'][] = [
    'name'        => 'editorAdmin',
    'title'       => '_MI_CLASSROOM_EDITOR_ADMIN',
    'description' => '_MI_CLASSROOM_EDITOR_ADMIN_DESC',
    'formtype'    => 'select',
    'valuetype'   => 'text',
    'default'     => 'dhtmltextarea',
    'options'     => $editorList,
];

$modversion['config'][] = [
    'name'        => 'editorUser',
    'title'       => '_MI_CLASSROOM_EDITOR_USER',
    'description' => '_MI_CLASSROOM_EDITOR_USER_DESC',
    'formtype'    => 'select',
    'valuetype'   => 'text',
    'default'     => 'dhtmltextarea',
    'options'     => $editorList,
];

/**
 * Make Sample button visible?
 */
$modversion['config'][] = [
    'name'        => 'displaySampleButton',
    'title'       => 'CO_' . $moduleDirNameUpper . '_' . 'SHOW_SAMPLE_BUTTON',
    'description' => 'CO_' . $moduleDirNameUpper . '_' . 'SHOW_SAMPLE_BUTTON_DESC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 1,
];

/**
 * Show Developer Tools?
 */
$modversion['config'][] = [
    'name'        => 'displayDeveloperTools',
    'title'       => 'CO_' . $moduleDirNameUpper . '_' . 'SHOW_DEV_TOOLS',
    'description' => 'CO_' . $moduleDirNameUpper . '_' . 'SHOW_DEV_TOOLS_DESC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 0,
];
