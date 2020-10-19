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

$cform = new \XoopsThemeForm(_CR_MA_CLASSFORM, 'classform', $_SERVER['REQUEST_URI']);

$cform->addElement(new \XoopsFormText(_CR_MA_CLASSNAME, 'name', 30, 50, $edit_class->getVar('name')), true);

$cform->addElement(new \XoopsFormText(_CR_MA_TIME, 'time', 30, 255, $edit_class->getVar('time')));
$cform->addElement(new \XoopsFormText(_CR_MA_WEIGHT, 'weight', 10, 10, (int)$edit_class->getVar('weight')));
$cform->addElement(new \XoopsFormDhtmlTextArea(_CR_MA_DESCRIPTION, 'description', $edit_class->getVar('description', 'E'), 40, 50));

$cform->addElement(new \XoopsFormHidden('classroomid', $edit_class->getVar('classroomid')));

if ($edit_class->getVar('classid') > 0) {
    $cform->addElement(new \XoopsFormHidden('classid', $edit_class->getVar('classid')));
}
$cform->addElement(new \XoopsFormHidden('op', 'class'));

$tray = new \XoopsFormElementTray('');
$tray->addElement(new \XoopsFormButton('', 'submit', _CR_MA_SUBMIT, 'submit'));
if ($edit_class->getVar('classid') > 0) {
    $tray->addElement(new \XoopsFormButton('', 'delete', _CR_MA_DELETE, 'submit'));
}

$cform->addElement($tray);
