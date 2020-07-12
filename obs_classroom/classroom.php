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
$classroom = isset($_GET['cr']) ? intval($_GET['cr']) : redirect_header('index.php', 2, _CR_ER_NOCLASSROOMSELECTED);

$class_handler =& xoops_getmodulehandler('class', 'obs_classroom');
$classroom_handler =& xoops_getmodulehandler('classroom', 'obs_classroom');
$class_handler =& xoops_getmodulehandler('class', 'obs_classroom');
$classroom_handler =& xoops_getmodulehandler('classroom', 'obs_classroom');
$div_handler =& xoops_getmodulehandler('division', 'obs_classroom');
$school_handler =& xoops_getmodulehandler('school', 'obs_classroom');

$class_criteria = new Criteria('classroomid', $classroom);
$classroom = $classroom_handler->getObjects($class_criteria, true, false);

$xoopsOption['template_main'] = "cr_classroom.html";
$xoopsOption['permission'] = "classroom";
$xoopsOption['itemid'] = $classroom[0]->getVar('classroomid');

$edit_mode = 0;
if (isset($_SESSION['cr_edit_mode']) && $_SESSION['cr_edit_mode'] == 1) {
    $gperm_handler =& xoops_gethandler('groupperm');
    $groups = $xoopsUser ? $xoopsUser->getGroups() : XOOPS_GROUP_ANONYMOUS;
    if ($xoopsUser && ($classroom[0]->getVar('owner') == $xoopsUser->getVar('uid'))) {
        $xoopsConfig['module_cache'][$xoopsModule->getVar('mid')] = 0;
        $edit_mode = 1;
    }
    elseif ($gperm_handler->checkRight($xoopsOption['permission'], $xoopsOption['itemid'], $groups, $xoopsModule->getVar('mid'))) {
        $xoopsConfig['module_cache'][$xoopsModule->getVar('mid')] = 0;
        $edit_mode = 1;
    }
}

include XOOPS_ROOT_PATH.'/header.php';
$xoopsTpl->assign('edit_mode', $edit_mode);

$xoopsTpl->assign('classes', $class_handler->getObjects($class_criteria, false));


$cr_arr = array('classroomid' => $classroom[0]->getVar('classroomid'),
                'name' => $classroom[0]->getVar('name'),
                'description' => $classroom[0]->getVar('description'),
                'ownername' => $classroom[0]->getVar('ownername'),
                'location' => $classroom[0]->getVar('location'));
$xoopsTpl->assign('classroom', $cr_arr);


$div_criteria = new Criteria('divisionid', $classroom[0]->getVar('divisionid'));
$division = $div_handler->getObjects($div_criteria, false, false);
$xoopsTpl->assign('division', $division[0]);

$school_criteria = new Criteria('schoolid', $division[0]['schoolid']);
$xoopsTpl->assign('school', $school_handler->getObjects($school_criteria, false, false));

include XOOPS_ROOT_PATH.'/footer.php';
?>
