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
$class = isset($_GET['c']) ? (int)$_GET['c'] : redirect_header('index.php', 2, _CR_ER_NOCLASSSELECTED);

$helper = Helper::getInstance();
$classblockHandler = $helper->getHandler('Classblock');
$classHandler      = $helper->getHandler('ClassroomClass');
$classroomHandler  = $helper->getHandler('Classroom');

$thisclass = $classHandler->get($class);

$classroomcriteria = new \Criteria('classroomid', $thisclass->getVar('classroomid'));

$thisclassroom = $classroomHandler->getObjects($classroomcriteria, false, false);

$xoopsOption['template_main']                             = 'cr_class.tpl';
$xoopsOption['permission']                                = 'classroom';
$xoopsOption['itemid']                                    = $thisclass->getVar('classroomid');
$xoopsConfig['module_cache'][$xoopsModule->getVar('mid')] = 0;
$edit_mode                                                = 0;
if (isset($_SESSION['cr_edit_mode']) && 1 == $_SESSION['cr_edit_mode']) {
    $gpermHandler = xoops_getHandler('groupperm');
    $groups       = $xoopsUser ? $xoopsUser->getGroups() : XOOPS_GROUP_ANONYMOUS;
    if ($xoopsUser && ($thisclassroom[0]['owner'] == $xoopsUser->getVar('uid'))) {
        $edit_mode = 1;
    } elseif ($gpermHandler->checkRight($xoopsOption['permission'], $xoopsOption['itemid'], $groups, $xoopsModule->getVar('mid'))) {
        $edit_mode = 1;
    }
}

require XOOPS_ROOT_PATH . '/header.php';
$xoopsTpl->assign('edit_mode', $edit_mode);

$xoopsTpl->assign('class', ['classid' => $thisclass->getVar('classid'), 'name' => $thisclass->getVar('name'), 'time' => $thisclass->getVar('time'), 'description' => $thisclass->getVar('description')]);
$xoopsTpl->assign('classroom', $thisclassroom);

$divisionHandler   = $helper->getHandler('Division');
$div_criteria = new \Criteria('divisionid', $thisclassroom[0]['divisionid']);
$division     = $divisionHandler->getObjects($div_criteria, false, false);
$xoopsTpl->assign('division', $division);

$schoolHandler   = $helper->getHandler('School');
$school_criteria = new \Criteria('schoolid', $division[0]['schoolid']);
$xoopsTpl->assign('school', $schoolHandler->getObjects($school_criteria, false, false));
$class_criteria = new CriteriaCompo(new \Criteria('classid', $class));
$class_criteria->add(new \Criteria('cb.visible', 1));
$class_criteria->setSort('cb.weight');
$block_arr = $classblockHandler->getObjects($class_criteria);

foreach (array_keys($block_arr) as $i) {
    $bcachetime = $block_arr[$i]->block->getVar('bcachetime');
    if (empty($bcachetime)) {
        $xoopsTpl->caching=0;
    } else {
        $xoopsTpl->caching=2;
        $xoopsTpl->cache_lifetime = $bcachetime;
    }
    $btpl = $block_arr[$i]->block->getVar('template');
    if (empty($bcachetime) || !$xoopsTpl->is_cached('db:' . $btpl, 'blk_' . $block_arr[$i]->getVar('blockid'))) {
        $xoopsLogger->addBlock($block_arr[$i]->block->getVar('name'));
        $bresult = $block_arr[$i]->buildBlock();
        if (!$bresult) {
            continue;
        }
        $xoopsTpl->assign_by_ref('block', $bresult);
        $bcontent = $xoopsTpl->fetch('db:' . $btpl, 'blk_' . $block_arr[$i]->getVar('blockid'));
        $xoopsTpl->clear_assign('block');
    } else {
        $xoopsLogger->addBlock($block_arr[$i]->block->getVar('name'), true, $bcachetime);
        $bcontent = $xoopsTpl->fetch('db:' . $btpl, 'blk_' . $block_arr[$i]->getVar('blockid'));
    }
    $xoopsTpl->clear_assign('block');
    switch ($block_arr[$i]->getVar('side')) {
        case XOOPS_SIDEBLOCK_LEFT:
            $xoopsTpl->append('cr_tlblocks', ['name' => $block_arr[$i]->block->getVar('name'), 'content' => $bcontent]);
            break;
        case XOOPS_CENTERBLOCK_LEFT:
            $xoopsTpl->append('cr_blblocks', ['name' => $block_arr[$i]->block->getVar('name'), 'content' => $bcontent]);
            break;
        case XOOPS_CENTERBLOCK_RIGHT:
            $xoopsTpl->append('cr_brblocks', ['name' => $block_arr[$i]->block->getVar('name'), 'content' => $bcontent]);
            break;
        case XOOPS_CENTERBLOCK_CENTER:
            $xoopsTpl->append('cr_ccblocks', ['name' => $block_arr[$i]->block->getVar('name'), 'content' => $bcontent]);
            break;
        case XOOPS_SIDEBLOCK_RIGHT:
            $xoopsTpl->append('cr_trblocks', ['name' => $block_arr[$i]->block->getVar('name'), 'content' => $bcontent]);
            break;
    }
    unset($bcontent);
}

$xoopsTpl->caching=0;
require XOOPS_ROOT_PATH . '/footer.php';
