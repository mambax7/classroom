<?php
//  ------------------------------------------------------------------------ //
//                XOOPS - PHP Content Management System                      //
//                    Copyright (c) 2000 XOOPS.org                           //
//                       <http://www.xoops.org/>                             //
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
require_once(XOOPS_ROOT_PATH."/class/xoopsformloader.php");

$bform = new XoopsThemeForm(_CR_MA_BLOCKFORM, 'blockform', $_SERVER['REQUEST_URI']);

$bform->addElement(new XoopsFormText(_CR_MA_BLOCKNAME, 'name', 30, 50, $edit_block->getVar('name')), true);

$type_select = new XoopsFormSelect(_CR_MA_BLOCKTYPE, 'blocktypeid', $edit_block->getVar('blocktypeid'));
$blocktypes = $xoopsModule->getInfo('blocktypes');
foreach ($blocktypes as $blocktypeid => $blocktype) {
    $type_select->addOption($blocktypeid, $blocktype['name']);
}
$bform->addElement($type_select);

$bform->addElement(new XoopsFormHidden('classroomid', $edit_block->getVar('classroomid')));

if ($edit_block->getVar('blockid') > 0) {
    $bform->addElement(new XoopsFormHidden('blockid', $edit_block->getVar('blockid')));
}
$bform->addElement(new XoopsFormHidden('op', 'block'));

$bform->addElement(new XoopsFormButton('', 'submit', _CR_MA_SUBMIT, 'submit'));

?>
