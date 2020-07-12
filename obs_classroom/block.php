<?
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
include '../../mainfile.php';
$blockid = isset($_GET['b']) ? intval($_GET['b']) : redirect_header('index.php', 2, _CR_ER_NOBLOCKSELECTED);

$classblock_handler =& xoops_getmodulehandler('classblock', 'obs_classroom');
$class_handler =& xoops_getmodulehandler('class', 'obs_classroom');
$classroom_handler =& xoops_getmodulehandler('classroom', 'obs_classroom');

$xoopsOption['template_main'] = "cr_block.html";
$xoopsConfig['module_cache'][$xoopsModule->getVar('mid')] = 0;

include XOOPS_ROOT_PATH.'/header.php';
$blockcriteria = new Criteria('cb.blockid', $blockid);
$block_arr = $classblock_handler->getObjects($blockcriteria);

if (count($block_arr) == 0) {
    redirect_header('index.php', 2, _CR_ER_NOBLOCKSELECTED);
}

foreach ($block_arr as $i => $block) {
    $classids[] = $block->getVar('classid');
}
$classcriteria = new Criteria('classid', "(".implode(',', $classids).")", 'IN');
$classes = $class_handler->getObjects($classcriteria, true, true);
foreach ($classes as $classid => $class) {
    $xoopsTpl->append('classes', array('classid' => $classid, 'name' => $class->getVar('name')));
}

$classroom =& $classroom_handler->get($block_arr[0]->block->getVar('classroomid'));
$xoopsTpl->assign('classroom', array('classroomid' => $classroom->getVar('classroomid'), 'name' => $classroom->getVar('name'), 'owner' => $classroom->getVar('ownername')));

$bcachetime = $block_arr[0]->block->getVar('bcachetime');
if (empty($bcachetime)) {
    $xoopsTpl->xoops_setCaching(0);
} else {
    $xoopsTpl->xoops_setCaching(2);
    $xoopsTpl->xoops_setCacheTime($bcachetime);
}
$btpl = $block_arr[0]->block->getVar('template');
if (empty($bcachetime) || !$xoopsTpl->is_cached('db:'.$btpl)) {
    $xoopsLogger->addBlock($block_arr[0]->block->getVar('name'));
    $bresult =& $block_arr[0]->buildBlock();
    if ($bresult) {
        $xoopsTpl->assign_by_ref('block', $bresult);
        $bcontent =& $xoopsTpl->fetch('db:'.$btpl, 'blk_'.$block_arr[0]->getVar('blockid'));
        $xoopsTpl->clear_assign('block');
    }
} else {
    $xoopsLogger->addBlock($block_arr[0]->block->getVar('name'), true, $bcachetime);
    $bcontent =& $xoopsTpl->fetch('db:'.$btpl, 'blk_'.$block_arr[0]->getVar('blockid'));
}
$xoopsTpl->clear_assign('block');
$xoopsTpl->assign('block', array('name' => $block_arr[0]->block->getVar('name'), 'content' => $bcontent));
unset($bcontent);


$xoopsTpl->xoops_setCaching(0);
include XOOPS_ROOT_PATH.'/footer.php';
?>
