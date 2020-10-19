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
$myts  = \MyTextSanitizer::getInstance();
$sform = new \XoopsThemeForm(_CR_MA_SCHOOLFORM, 'schoolform', $_SERVER['REQUEST_URI']);

$sform->addElement(new \XoopsFormText(_CR_MA_SCHOOLNAME, 'name', 30, 50, $edit_school->getVar('name', 'e')), true);

$sform->addElement(new \XoopsFormText(_CR_MA_LOCATION, 'location', 30, 255, $edit_school->getVar('location', 'e')));
$sform->addElement(new \XoopsFormText(_CR_MA_WEIGHT, 'weight', 10, 10, (int)$edit_school->getVar('weight')));

$membershipHandler = xoops_getHandler('membership');
$criteria          = new \Criteria('groupid', '(' . implode(',', $xoopsModuleConfig['head_group']) . ')', 'IN');
$allmemberships    = $membershipHandler->getObjects($criteria);
unset($criteria);
$userids = [];
foreach ($allmemberships as $key => $membership) {
    $userids[] = $membership->getVar('uid');
}
$memberHandler = xoops_getHandler('member');
$criteria      = new \Criteria('uid', '(' . implode(',', $userids) . ')', 'IN');
$users         = $memberHandler->getUserList($criteria);

$head_select = new \XoopsFormSelect(_CR_MA_HEADOFSCHOOL, 'head', false, $edit_school->getVar('head'));
$head_select->addOptionArray($users);
$sform->addElement($head_select, true);

$sform->addElement(new \XoopsFormDhtmlTextArea(_CR_MA_DESCRIPTION, 'description', htmlspecialchars($edit_school->getVar('description', 'n'), ENT_QUOTES | ENT_HTML5), 35, 50));

$moderators = [];

if ($edit_school->getVar('schoolid') > 0) {
    $sform->addElement(new \XoopsFormHidden('schoolid', $edit_school->getVar('schoolid')));

    $gpermHandler = xoops_getHandler('groupperm');
    $moderators   = $gpermHandler->getGroupIds('school', $edit_school->getVar('schoolid'), $xoopsModule->getVar('mid'));
}

$sform->addElement(new \XoopsFormSelectGroup(_CR_MA_MODERATORS, 'moderators', false, $moderators, 5, true), true);

$sform->addElement(new \XoopsFormHidden('op', 'school'));

$tray = new \XoopsFormElementTray('');
$tray->addElement(new \XoopsFormButton('', 'submit', _CR_MA_SUBMIT, 'submit'));
if ($edit_school->getVar('schoolid') > 0) {
    $tray->addElement(new \XoopsFormButton('', 'delete', _CR_MA_DELETE, 'submit'));
}

$sform->addElement($tray);
