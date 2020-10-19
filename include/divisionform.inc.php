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

require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';

$dform = new \XoopsThemeForm(_CR_MA_DIVISIONFORM, 'divisionform', $_SERVER['REQUEST_URI']);

$dform->addElement(new \XoopsFormText(_CR_MA_DIVISIONNAME, 'name', 30, 50, $edit_division->getVar('name')), true);

$helper = Helper::getInstance();
$schoolHandler = $helper->getHandler('School');
$schools       = $schoolHandler->getObjects();

$school_select = new \XoopsFormSelect(_CR_MA_SCHOOL, 'schoolid', $edit_division->getVar('schoolid'));
foreach ($schools as $id => $school) {
    $school_select->addOption($id, $school->getVar('name'));
}
$dform->addElement($school_select);

$dform->addElement(new \XoopsFormText(_CR_MA_LOCATION, 'location', 30, 255, $edit_division->getVar('location')));
$dform->addElement(new \XoopsFormText(_CR_MA_WEIGHT, 'weight', 10, 10, (int)$edit_division->getVar('weight')));
$membershipHandler = xoops_getHandler('membership');
$criteria          = new \Criteria('groupid', '(' . implode(',', $xoopsModuleConfig['director_group']) . ')', 'IN');
$allmemberships    = $membershipHandler->getObjects($criteria);
unset($criteria);
foreach ($allmemberships as $key => $membership) {
    $userids[] = $membership->getVar('uid');
}
$memberHandler = xoops_getHandler('member');
$criteria      = new \Criteria('uid', '(' . implode(',', $userids) . ')', 'IN');
$users         = $memberHandler->getUserList($criteria);

$dir_select = new \XoopsFormSelect(_CR_MA_DIRECTOR, 'director', false, $edit_division->getVar('director'));
$dir_select->addOptionArray($users);
$dform->addElement($dir_select, true);
$dform->addElement(new \XoopsFormDhtmlTextArea(_CR_MA_DESCRIPTION, 'description', $edit_division->getVar('description', 'E'), 10, 70));

$moderators = [];

if ($edit_division->getVar('divisionid') > 0) {
    $dform->addElement(new \XoopsFormHidden('divisionid', $edit_division->getVar('divisionid')));

    $gpermHandler = xoops_getHandler('groupperm');
    $moderators   = $gpermHandler->getGroupIds('division', $edit_division->getVar('divisionid'), $xoopsModule->getVar('mid'));
}

$dform->addElement(new \XoopsFormSelectGroup(_CR_MA_MODERATORS, 'moderators', false, $moderators, 5, true));

$dform->addElement(new \XoopsFormHidden('op', 'division'));

$tray = new \XoopsFormElementTray('');
$tray->addElement(new \XoopsFormButton('', 'submit', _CR_MA_SUBMIT, 'submit'));
if ($edit_division->getVar('divisionid') > 0) {
    $tray->addElement(new \XoopsFormButton('', 'delete', _CR_MA_DELETE, 'submit'));
}

$dform->addElement($tray);
