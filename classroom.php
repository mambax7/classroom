<?php
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
use XoopsModules\Classroom\Helper;

require __DIR__ . '/header.php';
$classroom = isset($_GET['cr']) ? (int)$_GET['cr'] : redirect_header('index.php', 2, _CR_ER_NOCLASSROOMSELECTED);

$helper = Helper::getInstance();
$classHandler     = $helper->getHandler('ClassroomClass');
$classroomHandler = $helper->getHandler('Classroom');
$classHandler     = $helper->getHandler('ClassroomClass');
$classroomHandler = $helper->getHandler('Classroom');
$divisionHandler       = $helper->getHandler('Division');
$schoolHandler    = $helper->getHandler('School');

$class_criteria = new \Criteria('classroomid', $classroom);
$classroom      = $classroomHandler->getObjects($class_criteria, true, false);

$GLOBALS['xoopsOption']['template_main'] = 'cr_classroom.tpl';
$xoopsOption['permission']               = 'classroom';
$xoopsOption['itemid']                   = $classroom[0]->getVar('classroomid');

$edit_mode = 0;
if (isset($_SESSION['cr_edit_mode']) && 1 == $_SESSION['cr_edit_mode']) {
    $gpermHandler = xoops_getHandler('groupperm');
    $groups       = $xoopsUser ? $xoopsUser->getGroups() : XOOPS_GROUP_ANONYMOUS;
    if ($xoopsUser && ($classroom[0]->getVar('owner') == $xoopsUser->getVar('uid'))) {
        $xoopsConfig['module_cache'][$xoopsModule->getVar('mid')] = 0;
        $edit_mode                                                = 1;
    } elseif ($gpermHandler->checkRight($xoopsOption['permission'], $xoopsOption['itemid'], $groups, $xoopsModule->getVar('mid'))) {
        $xoopsConfig['module_cache'][$xoopsModule->getVar('mid')] = 0;
        $edit_mode                                                = 1;
    }
}

require XOOPS_ROOT_PATH . '/header.php';
$xoopsTpl->assign('edit_mode', $edit_mode);

$xoopsTpl->assign('classes', $classHandler->getObjects($class_criteria, false));

$cr_arr = [
    'classroomid' => $classroom[0]->getVar('classroomid'),
    'name'        => $classroom[0]->getVar('name'),
    'description' => $classroom[0]->getVar('description'),
    'ownername'   => $classroom[0]->getVar('ownername'),
    'location'    => $classroom[0]->getVar('location'),
];
$xoopsTpl->assign('classroom', $cr_arr);

$div_criteria = new \Criteria('divisionid', $classroom[0]->getVar('divisionid'));
$division     = $divisionHandler->getObjects($div_criteria, false, false);
$xoopsTpl->assign('division', $division[0]);

$school_criteria = new \Criteria('schoolid', $division[0]['schoolid']);
$xoopsTpl->assign('school', $schoolHandler->getObjects($school_criteria, false, false));

require XOOPS_ROOT_PATH . '/footer.php';
