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

$cbform = new \XoopsThemeForm(_CR_MA_CLASSBLOCKFORM, 'classblockform', $_SERVER['REQUEST_URI']);

$cbform->addElement(new \XoopsFormText(_CR_MA_BLOCKNAME, 'blockname', 30, 50, $block->getVar('name', 'E')), true);

$helper = Helper::getInstance();
$classHandler = $helper->getHandler('ClassroomClass');

$criteria = new \Criteria('classroomid', $block->getVar('classroomid'));
$classes  = $classHandler->getObjects($criteria, true, true);
foreach ($classes as $classid => $class) {
    $visible = $settings[$classid]['visible'] ?? 0;
    $side    = $settings[$classid]['side'] ?? 0;
    $weight  = $settings[$classid]['weight'] ?? 0;

    $visible_check = new \XoopsFormCheckbox('', 'visible[' . $classid . ']', $visible);
    $visible_check->addOption(1, _CR_MA_VISIBLE);

    $position_select = new \XoopsFormSelect(_CR_MA_POSITION, 'position[' . $classid . ']', $side);
    $position_select->addOption(XOOPS_SIDEBLOCK_LEFT, _CR_MA_UPPERLEFT);
    $position_select->addOption(XOOPS_SIDEBLOCK_RIGHT, _CR_MA_UPPERRIGHT);
    $position_select->addOption(XOOPS_CENTERBLOCK_LEFT, _CR_MA_LOWERLEFT);
    $position_select->addOption(XOOPS_CENTERBLOCK_CENTER, _CR_MA_CENTER);
    $position_select->addOption(XOOPS_CENTERBLOCK_RIGHT, _CR_MA_LOWERRIGHT);

    $class_tray = new \XoopsFormElementTray($class->getVar('name'));
    $class_tray->addElement($visible_check);
    $class_tray->addElement($position_select);
    $class_tray->addElement(new \XoopsFormText(_CR_MA_WEIGHT, 'weight[' . $classid . ']', 5, 5, $weight));

    $cbform->addElement($class_tray);
    unset($class_tray);
    unset($visible_check);
    unset($position_select);
}

$cbform->addElement(new \XoopsFormHidden('b', $block->getVar('blockid')));
$cbform->addElement(new \XoopsFormHidden('classroomid', $block->getVar('classroomid')));

$cbform->addElement(new \XoopsFormHidden('op', 'classblock'));

$cbform->addElement(new \XoopsFormButton('', 'submit', _CR_MA_SUBMIT, 'submit'));
