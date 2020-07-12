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
if (!$xoopsUser) {
    redirect_header('index.php', 2, _NOPERM);
}
$op = isset($_REQUEST['op']) ? $_REQUEST['op'] : redirect_header('index.php', 3, _CR_ER_NOSELECTION);
$xoopsConfig['module_cache'][$xoopsModule->getVar('mid')] = 0;
$gperm_handler =& xoops_gethandler('groupperm');
$groups = $xoopsUser->getGroups();
switch($op) {
    case "school":
    if (!$xoopsUser->isAdmin($xoopsModule->getVar('mid'))) {
        redirect_header('index.php', 2, _NOPERM);
    }
    $school_handler =& xoops_getmodulehandler('school', 'obs_classroom');
    include XOOPS_ROOT_PATH.'/header.php';
    if (isset($_POST['submit'])) {
        if ($schoolid = $school_handler->updateInsert()) {
            redirect_header('school.php?s='.$schoolid, 3, _CR_MA_SCHOOLSAVED);
        }
        xoops_error(_CR_ER_SCHOOLNOTSAVED);
    }
    elseif (isset($_POST['delete'])) {
        $hiddens = array(
        'op' => 'delete',
        'item' => 'school',
        'itemid' => $_POST['schoolid']
        );
        $subitemcount = $school_handler->getSubitemCount($_POST['schoolid']);
        $del_school =& $school_handler->get($_POST['schoolid']);
        $message = sprintf(_CR_MA_SCHOOLITEMCOUNT, $del_school->getVar('name'), $subitemcount['divisions'], $subitemcount['classrooms'], $subitemcount['classes'], $subitemcount['blocks']);
        xoops_confirm($hiddens, 'manage.php', $message);
        break;
    }
    
    $id = isset($_REQUEST['s']) ? intval($_REQUEST['s']) : 0;
    
    if ($id > 0) {
        $edit_school =& $school_handler->get($id);
    }
    else {
        $edit_school =& $school_handler->create(false);
    }
    include 'include/schoolform.inc.php';
    $sform->display();
    break;
    
    case "division":
    $div = isset($_REQUEST['d']) ? intval($_REQUEST['d']) : 0;
    if ($div > 0) {
        $xoopsOption['permission'] = "division";
        $xoopsOption['itemid'] = $div;
    }
    else {
        $school = isset($_REQUEST['s']) ? intval($_REQUEST['s']) : redirect_header('index.php', 3, _CR_ER_NOSCHOOLSELECTED);
        $xoopsOption['permission'] = "school";
        $xoopsOption['itemid'] = $school;
    }
    if (!$gperm_handler->checkRight($xoopsOption['permission'], $xoopsOption['itemid'], $groups, $xoopsModule->getVar('mid'))) {
        redirect_header('index.php', 2, _NOPERM);
    }
    include XOOPS_ROOT_PATH.'/header.php';
    
    $division_handler =& xoops_getmodulehandler('division', 'obs_classroom');
    
    if (isset($_POST['submit'])) {
        if ($divisionid = $division_handler->updateInsert()) {
            redirect_header('division.php?d='.$divisionid, 3, _CR_MA_DIVISIONSAVED);
        }
        xoops_error(_CR_ER_DIVISIONNOTSAVED);
    }
    elseif (isset($_POST['delete'])) {
        $hiddens = array(
        'op' => 'delete',
        'item' => 'division',
        'itemid' => $_POST['divisionid']
        );
        $subitemcount = $division_handler->getSubitemCount($_POST['divisionid']);
        $del_division =& $division_handler->get($_POST['divisionid']);
        $message = sprintf(_CR_MA_DIVISIONITEMCOUNT, $del_division->getVar('name'), $subitemcount['classrooms'], $subitemcount['classes'], $subitemcount['blocks']);
        xoops_confirm($hiddens, 'manage.php', $message);
        break;
    }
    
    if ($div > 0) {
        $edit_division =& $division_handler->get($div);
    }
    else {
        $edit_division =& $division_handler->create(false);
        $edit_division->setVar('schoolid', $school);
    }
    include 'include/divisionform.inc.php';
    $dform->display();
    break;
    
    case "classroom":
    $classroom = isset($_REQUEST['cr']) ? intval($_REQUEST['cr']) : 0;
    if ($classroom > 0) {
        $xoopsOption['permission'] = "classroom";
        $xoopsOption['itemid'] = $classroom;
    }
    else {
        $div = isset($_REQUEST['d']) ? intval($_REQUEST['d']) : redirect_header('index.php', 3, _CR_ER_NODIVSELECTED);
        $xoopsOption['permission'] = "division";
        $xoopsOption['itemid'] = $div;
    }
    
    $classroom_handler =& xoops_getmodulehandler('classroom', 'obs_classroom');
    
    if (!$gperm_handler->checkRight($xoopsOption['permission'], $xoopsOption['itemid'], $groups, $xoopsModule->getVar('mid'))) {
        if (!isset($classroom)) {
            redirect_header('index.php', 2, _NOPERM);
        }
        $thisclassroom =& $classroom_handler->get($classroom);
        if ($thisclassroom->getVar('owner') != $xoopsUser->getVar('uid')) {
            redirect_header('index.php', 2, _NOPERM);
        }
        $classroomadmin = 0;
    }
    else {
        $classroomadmin = 1;
    }
    include XOOPS_ROOT_PATH.'/header.php';
    
    if (isset($_POST['submit'])) {
        if ($classroomid = $classroom_handler->updateInsert()) {
            redirect_header('classroom.php?cr='.$classroomid, 3, _CR_MA_CLASSROOMSAVED);
        }
        xoops_error(_CR_ER_CLASSROOMNOTSAVED);
    }
    elseif (isset($_POST['delete'])) {
        $hiddens = array(
        'op' => 'delete',
        'item' => 'classroom',
        'itemid' => $_POST['classroomid']
        );
        $subitemcount = $classroom_handler->getSubitemCount($_POST['classroomid']);
        $del_classroom =& $classroom_handler->get($_POST['classroomid']);
        $message = sprintf(_CR_MA_CLASSROOMITEMCOUNT, $del_classroom->getVar('name'), $subitemcount['classes'], $subitemcount['blocks']);
        xoops_confirm($hiddens, 'manage.php', $message);
        break;
    }
    
    if ($classroom > 0) {
        $edit_classroom =& $classroom_handler->get($classroom);
    }
    else {
        $edit_classroom =& $classroom_handler->create(false);
        $edit_classroom->setVar('divisionid', $div);
    }
    include 'include/classroomform.inc.php';
    $crform->display();
    break;
    
    case "class":
    $class = isset($_REQUEST['c']) ? intval($_REQUEST['c']) : 0;
    $class_handler =& xoops_getmodulehandler('class', 'obs_classroom');
    if ($class > 0) {
        $xoopsOption['permission'] = "class";
        $xoopsOption['itemid'] = $class;
        $edit_class =& $class_handler->get($class);
    }
    else {
        $classroom = isset($_REQUEST['cr']) ? intval($_REQUEST['cr']) : redirect_header('index.php', 3, _CR_ER_NOCLASSROOMSELECTED);
        $xoopsOption['permission'] = "classroom";
        $xoopsOption['itemid'] = $classroom;
        $edit_class =& $class_handler->create(false);
        $edit_class->setVar('classroomid', $classroom);
    }
    if (!$gperm_handler->checkRight($xoopsOption['permission'], $xoopsOption['itemid'], $groups, $xoopsModule->getVar('mid'))) {
        $classroom_handler =& xoops_getmodulehandler('classroom', 'obs_classroom');
        $thisclassroom =& $classroom_handler->get($edit_class->getVar("classroomid"));
        if ($thisclassroom->getVar('owner') != $xoopsUser->getVar('uid')) {
            redirect_header('index.php', 2, _NOPERM);
        }
    }
    include XOOPS_ROOT_PATH.'/header.php';
    
    if (isset($_POST['submit'])) {
        if ($class = $class_handler->updateInsert()) {
            if (!isset($thisclassroom)) {
                $classroom_handler =& xoops_getmodulehandler('classroom', 'obs_classroom');
                $thisclassroom =& $classroom_handler->get($class->getVar('classroomid'));
            }
            $thisclassroom->updateCache();
            redirect_header('class.php?c='.$class->getVar('classid'), 3, _CR_MA_CLASSSAVED);
        }
        xoops_error(_CR_ER_CLASSNOTSAVED);
    }
    elseif (isset($_POST['delete'])) {
        $hiddens = array(
        'op' => 'delete',
        'item' => 'class',
        'itemid' => $_POST['classid']
        );
        $message = _CR_MA_AREYOUSUREDELCLASS;
        xoops_confirm($hiddens, 'manage.php', $message);
        break;
    }
    
    include 'include/classform.inc.php';
    $cform->display();
    break;
    
    case "block":
    $block = isset($_REQUEST['b']) ? intval($_REQUEST['b']) : 0;
    $classroom = isset($_REQUEST['cr']) ? intval($_REQUEST['cr']) : redirect_header('index.php', 3, _CR_ER_NOCLASSROOMSELECTED);
    
    $xoopsOption['permission'] = "classroom";
    $xoopsOption['itemid'] = $classroom;
    if (!$gperm_handler->checkRight($xoopsOption['permission'], $xoopsOption['itemid'], $groups, $xoopsModule->getVar('mid'))) {
        $classroom_handler =& xoops_getmodulehandler('classroom', 'obs_classroom');
        $thisclassroom =& $classroom_handler->get($classroom);
        if ($thisclassroom->getVar('owner') != $xoopsUser->getVar('uid')) {
            redirect_header('index.php', 2, _NOPERM);
        }
    }
    include XOOPS_ROOT_PATH.'/header.php';
    
    $block_handler =& xoops_getmodulehandler('block', 'obs_classroom');
    if (isset($_POST['submit'])) {
        if ($block_obj = $block_handler->updateInsert()) {
            if (!isset($thisclassroom)) {
                $classroom_handler =& xoops_getmodulehandler('classroom', 'obs_classroom');
                $thisclassroom =& $classroom_handler->get($block_obj->getVar('classroomid'));
            }
            redirect_header('manage.php?op=classblock&amp;b='.$block_obj->getVar('blockid'), 3, _CR_MA_BLOCKSAVED);
        }
        xoops_error(_CR_ER_BLOCKNOTSAVED);
    }
    elseif (isset($_POST['delete'])) {
        $hiddens = array(
        'op' => 'delete',
        'item' => 'block',
        'itemid' => $_POST['b']
        );
        $message = _CR_MA_AREYOUSUREDELBLOCK;
        xoops_confirm($hiddens, 'manage.php', $message);
        break;
    }
    
    if ($block > 0) {
        $edit_block =& $block_handler->get($block);
    }
    else {
        $edit_block =& $block_handler->create(false);
        $edit_block->setVar('classroomid', $classroom);
    }
    echo "<div><a href='classroom.php?cr=".$classroom."'>"._CR_MA_BACKTOCLASSROOM."</a></div>";
    
    include 'include/blockform.inc.php';
    
    echo $block_handler->listBlocks($classroom);
    
    $bform->display();
    break;
    
    case "classblock":
    $blockid = isset($_REQUEST['b']) ? intval($_REQUEST['b']) : redirect_header('index.php', 3, _CR_ER_NOBLOCKSELECTED);
    $block_handler =& xoops_getmodulehandler('block', 'obs_classroom');
    $classblock_handler =& xoops_getmodulehandler('classblock', 'obs_classroom');
    
    $block =& $block_handler->get($blockid);
    $xoopsOption['permission'] = "classroom";
    $xoopsOption['itemid'] = $block->getVar('classroomid');
    
    if (!$gperm_handler->checkRight($xoopsOption['permission'], $xoopsOption['itemid'], $groups, $xoopsModule->getVar('mid'))) {
        $classroom_handler =& xoops_getmodulehandler('classroom', 'obs_classroom');
        $thisclassroom =& $classroom_handler->get($block->getVar('classroomid'));
        if ($thisclassroom->getVar('owner') != $xoopsUser->getVar('uid')) {
            redirect_header('index.php', 2, _NOPERM);
        }
    }
    include XOOPS_ROOT_PATH.'/header.php';
    
    if (isset($_POST['submit'])) {
        $classblock_handler->updateInsert($block);
        if (count($classblock_handler->getErrors()) == 0 ) {
            redirect_header('manage.php?op=classblock&amp;b='.$block->getVar('blockid'), 3, _CR_MA_CLASSBLOCKSAVED);
        }
        xoops_error(implode('<br />'.$classblock_handler->getErrors()));
    }
    
    $settings = $classblock_handler->getSettings($block);
    echo "<div><a href='manage.php?op=block&amp;cr=".$block->getVar('classroomid')."'>"._CR_MA_BACKTOBLOCK."</a></div>";
    include 'include/classblockform.inc.php';
    $cbform->display();
    break;
    
    case "editblock":
        $blockid = isset($_REQUEST['blockid']) ? intval($_REQUEST['blockid']) : redirect_header('index.php', 3, _CR_ER_NOBLOCKSELECTED);
        $block_handler =& xoops_getmodulehandler('block', 'obs_classroom');
        
        $thisblock =& $block_handler->get($blockid);
        
        if (!$gperm_handler->checkRight('classroom', $thisblock->getVar('classroomid'), $groups, $xoopsModule->getVar('mid'))) {
            $classroom_handler =& xoops_getmodulehandler('classroom', 'obs_classroom');
            $thisclassroom =& $classroom_handler->get($thisblock->getVar('classroomid'));
            if ($thisclassroom->getVar('owner') != $xoopsUser->getVar('uid')) {
                redirect_header('index.php', 2, _NOPERM);
            }
        }
        include XOOPS_ROOT_PATH.'/header.php';
        if (isset($_POST['submit'])) {
            if ($thisblock->update()) {
                redirect_header('manage.php?op=editblock&amp;blockid='.$thisblock->getVar('blockid'), 3, _CR_MA_BLOCKSAVED);
            }
            echo ($thisblock->getHtmlErrors());
        }
        echo "<div><a href='manage.php?op=block&amp;cr=".$thisblock->getVar('classroomid')."'>"._CR_MA_BACKTOBLOCK."</a></div>";
        //redirect_header('manage.php?op=classblock&amp;b='.$thisblock->getVar('blockid'), 3, _CR_ER_BLOCKNOTSAVED);
        $thisblock->buildForm();
    break;
    
    case "delete":
    switch ($_POST['item']) {
        case "school":
        if (!$xoopsUser->isAdmin($xoopsModule->getVar('mid'))) {
            redirect_header('index.php', 2, _NOPERM);
        }
        $school_handler =& xoops_getmodulehandler('school', 'obs_classroom');
        $thisschool =& $school_handler->get($_POST['itemid']);
        if ($school_handler->delete($thisschool)) {
            $school_handler->updateCache();
            redirect_header('index.php', 2, _CR_MA_SCHOOLDELETED);
        }
        echo "error";
        break;
        
        case "division":
        $div_handler =& xoops_getmodulehandler('division', 'obs_classroom');
        $thisdiv =& $div_handler->get($_POST['itemid']);
        if (!$gperm_handler->checkRight('school', $thisdiv->getVar('schoolid'), $groups, $xoopsModule->getVar('mid'))) {
            redirect_header('index.php', 2, _NOPERM);
        }
        
        if ($div_handler->delete($thisdiv)) {
            $school_handler =& xoops_getmodulehandler('school', 'obs_classroom');
            $thisschool =& $school_handler->get($$thisdiv->getVar('schoolid'));
            $thisschool->updateCache();
            redirect_header('index.php', 2, _CR_MA_DIVISIONDELETED);
        }
        echo "error";
        break;
        
        case "classroom":
        $cr_handler =& xoops_getmodulehandler('classroom', 'obs_classroom');
        $thiscr =& $cr_handler->get($_POST['itemid']);
        if (!$gperm_handler->checkRight('division', $thiscr->getVar('divisionid'), $groups, $xoopsModule->getVar('mid'))) {
            redirect_header('index.php', 2, _NOPERM);
        }
        if ($cr_handler->delete($thiscr)) {
            $div_handler =& xoops_getmodulehandler('division', 'obs_classroom');
            $thisdiv =& $div_handler->get($thiscr->getVar('divisionid'));
            $thisdiv->updateCache();
            redirect_header('index.php', 2, _CR_MA_CLASSROOMDELETED);
        }
        echo "error";
        break;
        
        case "class":
        $class_handler =& xoops_getmodulehandler('class', 'obs_classroom');
        $classroom_handler =& xoops_getmodulehandler('classroom', 'obs_classroom');
        $thisclass =& $class_handler->get($_POST['itemid']);
        $thisclassroom =& $classroom_handler->get($thisclass->getVar('classroomid'));
        if (!$gperm_handler->checkRight('classroom', $thisclass->getVar('classroomid'), $groups, $xoopsModule->getVar('mid')) && $thisclassroom->getVar('owner' != $xoopsUser->getVar('uid'))) {
            redirect_header('index.php', 2, _NOPERM);
        }
        if ($class_handler->delete($thisclass)) {
            $thisclassroom->updateCache();
            redirect_header('index.php', 2, _CR_MA_CLASSDELETED);
        }
        echo "error";
        break;
        
        case "block":
        $classroom_handler =& xoops_getmodulehandler('classroom', 'obs_classroom');
        $block_handler =& xoops_getmodulehandler('block', 'obs_classroom');
        $thisblock =& $block_handler->get($_REQUEST['itemid']);
        $thisclassroom =& $classroom_handler->get($thisblock->getVar('classroomid'));
        if (!$gperm_handler->checkRight('classroom', $thisblock->getVar('classroomid'), $groups, $xoopsModule->getVar('mid')) && $thisclassroom->getVar('owner' != $xoopsUser->getVar('uid'))) {
            redirect_header('index.php', 2, _NOPERM);
        }
        $message = _CR_ER_BLOCKNOTDELETED;
        if ($block_handler->delete($thisblock)) {
            $message = _CR_MA_BLOCKDELETED;
        }
        redirect_header('manage.php?op=block&cr='.$thisblock->getVar('classroomid'), 2, $message);
        break;
    }
    break;
    
    case "deleteitem":
        $classroom_handler =& xoops_getmodulehandler('classroom', 'obs_classroom');
        $block_handler =& xoops_getmodulehandler('block', 'obs_classroom');
        $thisblock =& $block_handler->get($_REQUEST['b']);
        $thisclassroom =& $classroom_handler->get($thisblock->getVar('classroomid'));
        if (!$gperm_handler->checkRight('classroom', $thisblock->getVar('classroomid'), $groups, $xoopsModule->getVar('mid')) && $thisclassroom->getVar('owner' != $xoopsUser->getVar('uid'))) {
            redirect_header('index.php', 2, _NOPERM);
        }
        $message = _CR_ER_ITEMNOTDELETED;
        if ($thisblock->deleteItem()) {
            $thisblock->updateCache();
            $message = _CR_MA_ITEMDELETED;
        }
        redirect_header('manage.php?op=editblock&amp;blockid='.$thisblock->getVar('blockid'), 2, $message);
        break;
}

include XOOPS_ROOT_PATH.'/footer.php';
?>
