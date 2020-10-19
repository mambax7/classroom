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

$div = isset($_GET['d']) ? (int)$_GET['d'] : redirect_header('index.php', 2, _CR_ER_NODIVSELECTED);

$GLOBALS['xoopsOption']['template_main'] = 'cr_division.tpl';
$xoopsOption['permission']               = 'division';
$xoopsOption['itemid']                   = $div;
require __DIR__ . '/header.php';

$helper = Helper::getInstance();
$classroomHandler = $helper->getHandler('Classroom');
$divisionHandler       = $helper->getHandler('Division');

$div_criteria = new \Criteria('divisionid', $div);
$xoopsTpl->assign('classrooms', $classroomHandler->getObjects($div_criteria, false));
$division = $divisionHandler->getObjects($div_criteria, true, false);
$div_arr  = [
    'divisionid'   => $div,
    'name'         => $division[0]->getVar('name'),
    'directorname' => $division[0]->getVar('directorname'),
    'location'     => $division[0]->getVar('location'),
    'description'  => $division[0]->getVar('description'),
];
$xoopsTpl->assign('division', $div_arr);

$schoolHandler   = $helper->getHandler('School');
$school_criteria = new \Criteria('schoolid', $division[0]->getVar('schoolid'));
$xoopsTpl->assign('school', $schoolHandler->getObjects($school_criteria, false, false));

require XOOPS_ROOT_PATH . '/footer.php';
