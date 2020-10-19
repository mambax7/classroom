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
require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';

$crform = new \XoopsThemeForm(_CR_MA_CLASSROOMFORM, 'classroomform', $_SERVER['REQUEST_URI']);

$crform->addElement(new \XoopsFormText(_CR_MA_CLASSROOMNAME, 'name', 30, 50, $edit_classroom->getVar('name')), true);

$membershipHandler = xoops_getHandler('membership');
$criteria          = new \Criteria('groupid', '(' . implode(',', $xoopsModuleConfig['teacher_group']) . ')', 'IN');
$allmemberships    = $membershipHandler->getObjects($criteria);
unset($criteria);
foreach ($allmemberships as $key => $membership) {
    $userids[] = $membership->getVar('uid');
}
$memberHandler = xoops_getHandler('member');
$criteria      = new \Criteria('uid', '(' . implode(',', $userids) . ')', 'IN');
$users         = $memberHandler->getUserList($criteria);

$teacher_select = new \XoopsFormSelect(_CR_MA_TEACHER, 'owner', false, $edit_classroom->getVar('owner'));
$teacher_select->addOptionArray($users);
$crform->addElement($teacher_select, true);

$crform->addElement(new \XoopsFormText(_CR_MA_LOCATION, 'location', 30, 255, $edit_classroom->getVar('location')));
$crform->addElement(new \XoopsFormText(_CR_MA_WEIGHT, 'weight', 10, 10, (int)$edit_classroom->getVar('weight')));
$crform->addElement(new \XoopsFormDhtmlTextArea(_CR_MA_DESCRIPTION, 'description', $edit_classroom->getVar('description', 'E'), 40, 50));

$crform->addElement(new \XoopsFormHidden('divisionid', $edit_classroom->getVar('divisionid')));

$moderators = [];

if ($edit_classroom->getVar('classroomid') > 0) {
    $crform->addElement(new \XoopsFormHidden('classroomid', $edit_classroom->getVar('classroomid')));

    $gpermHandler = xoops_getHandler('groupperm');
    $moderators   = $gpermHandler->getGroupIds('classroom', $edit_classroom->getVar('classroomid'), $xoopsModule->getVar('mid'));
}

$crform->addElement(new \XoopsFormSelectGroup(_CR_MA_MODERATORS, 'moderators', false, $moderators, 5, true));

$crform->addElement(new \XoopsFormHidden('op', 'classroom'));

$tray = new \XoopsFormElementTray('');
$tray->addElement(new \XoopsFormButton('', 'submit', _CR_MA_SUBMIT, 'submit'));
if ($edit_classroom->getVar('classroomid') > 0 && $classroomadmin) {
    $tray->addElement(new \XoopsFormButton('', 'delete', _CR_MA_DELETE, 'submit'));
}

$crform->addElement($tray);
