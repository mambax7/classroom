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

if (!$xoopsUser) {
    redirect_header('index.php', 2, _NOPERM);
}
$op                                                       = $_REQUEST['op'] ?? redirect_header('index.php', 3, _CR_ER_NOSELECTION);
$xoopsConfig['module_cache'][$xoopsModule->getVar('mid')] = 0;
$gpermHandler                                             = xoops_getHandler('groupperm');
$groups                                                   = $xoopsUser->getGroups();
$helper = Helper::getInstance();
switch ($op) {
    case 'school':
        if (!$xoopsUser->isAdmin($xoopsModule->getVar('mid'))) {
            redirect_header('index.php', 2, _NOPERM);
        }
        $schoolHandler = $helper->getHandler('School');
        require XOOPS_ROOT_PATH . '/header.php';
        if (isset($_POST['submit'])) {
            if ($schoolid = $schoolHandler->updateInsert()) {
                redirect_header('school.php?s=' . $schoolid, 3, _CR_MA_SCHOOLSAVED);
            }
            xoops_error(_CR_ER_SCHOOLNOTSAVED);
        } elseif (isset($_POST['delete'])) {
            $hiddens      = [
                'op'     => 'delete',
                'item'   => 'school',
                'itemid' => $_POST['schoolid'],
            ];
            $subitemcount = $schoolHandler->getSubitemCount($_POST['schoolid']);
            $del_school   =& $schoolHandler->get($_POST['schoolid']);
            $message      = sprintf(_CR_MA_SCHOOLITEMCOUNT, $del_school->getVar('name'), $subitemcount['divisions'], $subitemcount['classrooms'], $subitemcount['classes'], $subitemcount['blocks']);
            xoops_confirm($hiddens, 'manage.php', $message);
            break;
        }

        $id = isset($_REQUEST['s']) ? (int)$_REQUEST['s'] : 0;

        if ($id > 0) {
            $edit_school =& $schoolHandler->get($id);
        } else {
            $edit_school = $schoolHandler->create(false);
        }
        require __DIR__ . '/include/schoolform.inc.php';
        $sform->display();
        break;

    case 'division':
        $div = isset($_REQUEST['d']) ? (int)$_REQUEST['d'] : 0;
        if ($div > 0) {
            $xoopsOption['permission'] = 'division';
            $xoopsOption['itemid']     = $div;
        } else {
            $school                    = isset($_REQUEST['s']) ? (int)$_REQUEST['s'] : redirect_header('index.php', 3, _CR_ER_NOSCHOOLSELECTED);
            $xoopsOption['permission'] = 'school';
            $xoopsOption['itemid']     = $school;
        }
        if (!$gpermHandler->checkRight($xoopsOption['permission'], $xoopsOption['itemid'], $groups, $xoopsModule->getVar('mid'))) {
            redirect_header('index.php', 2, _NOPERM);
        }
        require XOOPS_ROOT_PATH . '/header.php';

        $divisionHandler = $helper->getHandler('Division');

        if (isset($_POST['submit'])) {
            if ($divisionid = $divisionHandler->updateInsert()) {
                redirect_header('division.php?d=' . $divisionid, 3, _CR_MA_DIVISIONSAVED);
            }
            xoops_error(_CR_ER_DIVISIONNOTSAVED);
        } elseif (isset($_POST['delete'])) {
            $hiddens      = [
                'op'     => 'delete',
                'item'   => 'division',
                'itemid' => $_POST['divisionid'],
            ];
            $subitemcount = $divisionHandler->getSubitemCount($_POST['divisionid']);
            $del_division =& $divisionHandler->get($_POST['divisionid']);
            $message      = sprintf(_CR_MA_DIVISIONITEMCOUNT, $del_division->getVar('name'), $subitemcount['classrooms'], $subitemcount['classes'], $subitemcount['blocks']);
            xoops_confirm($hiddens, 'manage.php', $message);
            break;
        }

        if ($div > 0) {
            $edit_division =& $divisionHandler->get($div);
        } else {
            $edit_division = $divisionHandler->create(false);
            $edit_division->setVar('schoolid', $school);
        }
        require __DIR__ . '/include/divisionform.inc.php';
        $dform->display();
        break;

    case 'classroom':
        $classroom = isset($_REQUEST['cr']) ? (int)$_REQUEST['cr'] : 0;
        if ($classroom > 0) {
            $xoopsOption['permission'] = 'classroom';
            $xoopsOption['itemid']     = $classroom;
        } else {
            $div                       = isset($_REQUEST['d']) ? (int)$_REQUEST['d'] : redirect_header('index.php', 3, _CR_ER_NODIVSELECTED);
            $xoopsOption['permission'] = 'division';
            $xoopsOption['itemid']     = $div;
        }

        $classroomHandler = $helper->getHandler('Classroom');

        if (!$gpermHandler->checkRight($xoopsOption['permission'], $xoopsOption['itemid'], $groups, $xoopsModule->getVar('mid'))) {
            if (!isset($classroom)) {
                redirect_header('index.php', 2, _NOPERM);
            }
            $thisclassroom =& $classroomHandler->get($classroom);
            if ($thisclassroom->getVar('owner') != $xoopsUser->getVar('uid')) {
                redirect_header('index.php', 2, _NOPERM);
            }
            $classroomadmin = 0;
        } else {
            $classroomadmin = 1;
        }
        require XOOPS_ROOT_PATH . '/header.php';

        if (isset($_POST['submit'])) {
            if ($classroomid = $classroomHandler->updateInsert()) {
                redirect_header('classroom.php?cr=' . $classroomid, 3, _CR_MA_CLASSROOMSAVED);
            }
            xoops_error(_CR_ER_CLASSROOMNOTSAVED);
        } elseif (isset($_POST['delete'])) {
            $hiddens       = [
                'op'     => 'delete',
                'item'   => 'classroom',
                'itemid' => $_POST['classroomid'],
            ];
            $subitemcount  = $classroomHandler->getSubitemCount($_POST['classroomid']);
            $del_classroom =& $classroomHandler->get($_POST['classroomid']);
            $message       = sprintf(_CR_MA_CLASSROOMITEMCOUNT, $del_classroom->getVar('name'), $subitemcount['classes'], $subitemcount['blocks']);
            xoops_confirm($hiddens, 'manage.php', $message);
            break;
        }

        if ($classroom > 0) {
            $edit_classroom =& $classroomHandler->get($classroom);
        } else {
            $edit_classroom = $classroomHandler->create(false);
            $edit_classroom->setVar('divisionid', $div);
        }
        require __DIR__ . '/include/classroomform.inc.php';
        $crform->display();
        break;

    case 'class':
        $class        = isset($_REQUEST['c']) ? (int)$_REQUEST['c'] : 0;
        $classHandler = $helper->getHandler('ClassroomClass');
        if ($class > 0) {
            $xoopsOption['permission'] = 'class';
            $xoopsOption['itemid']     = $class;
            $edit_class                =& $classHandler->get($class);
        } else {
            $classroom                 = isset($_REQUEST['cr']) ? (int)$_REQUEST['cr'] : redirect_header('index.php', 3, _CR_ER_NOCLASSROOMSELECTED);
            $xoopsOption['permission'] = 'classroom';
            $xoopsOption['itemid']     = $classroom;
            $edit_class                = $classHandler->create(false);
            $edit_class->setVar('classroomid', $classroom);
        }
        if (!$gpermHandler->checkRight($xoopsOption['permission'], $xoopsOption['itemid'], $groups, $xoopsModule->getVar('mid'))) {
            $classroomHandler = $helper->getHandler('Classroom');
            $thisclassroom    =& $classroomHandler->get($edit_class->getVar('classroomid'));
            if ($thisclassroom->getVar('owner') != $xoopsUser->getVar('uid')) {
                redirect_header('index.php', 2, _NOPERM);
            }
        }
        require XOOPS_ROOT_PATH . '/header.php';

        if (isset($_POST['submit'])) {
            if ($class = $classHandler->updateInsert()) {
                if (!isset($thisclassroom)) {
                    $classroomHandler = $helper->getHandler('Classroom');
                    $thisclassroom    =& $classroomHandler->get($class->getVar('classroomid'));
                }
                $thisclassroom->updateCache();
                redirect_header('class.php?c=' . $class->getVar('classid'), 3, _CR_MA_CLASSSAVED);
            }
            xoops_error(_CR_ER_CLASSNOTSAVED);
        } elseif (isset($_POST['delete'])) {
            $hiddens = [
                'op'     => 'delete',
                'item'   => 'class',
                'itemid' => $_POST['classid'],
            ];
            $message = _CR_MA_AREYOUSUREDELCLASS;
            xoops_confirm($hiddens, 'manage.php', $message);
            break;
        }

        require __DIR__ . '/include/classform.inc.php';
        $cform->display();
        break;

    case 'block':
        $block     = isset($_REQUEST['b']) ? (int)$_REQUEST['b'] : 0;
        $classroom = isset($_REQUEST['cr']) ? (int)$_REQUEST['cr'] : redirect_header('index.php', 3, _CR_ER_NOCLASSROOMSELECTED);

        $xoopsOption['permission'] = 'classroom';
        $xoopsOption['itemid']     = $classroom;
        if (!$gpermHandler->checkRight($xoopsOption['permission'], $xoopsOption['itemid'], $groups, $xoopsModule->getVar('mid'))) {
            $classroomHandler = $helper->getHandler('Classroom');
            $thisclassroom    =& $classroomHandler->get($classroom);
            if ($thisclassroom->getVar('owner') != $xoopsUser->getVar('uid')) {
                redirect_header('index.php', 2, _NOPERM);
            }
        }
        require XOOPS_ROOT_PATH . '/header.php';

        $blockHandler = $helper->getHandler('Block');
        if (isset($_POST['submit'])) {
            if ($block_obj = $blockHandler->updateInsert()) {
                if (!isset($thisclassroom)) {
                    $classroomHandler = $helper->getHandler('Classroom');
                    $thisclassroom    =& $classroomHandler->get($block_obj->getVar('classroomid'));
                }
                redirect_header('manage.php?op=classblock&amp;b=' . $block_obj->getVar('blockid'), 3, _CR_MA_BLOCKSAVED);
            }
            xoops_error(_CR_ER_BLOCKNOTSAVED);
        } elseif (isset($_POST['delete'])) {
            $hiddens = [
                'op'     => 'delete',
                'item'   => 'block',
                'itemid' => $_POST['b'],
            ];
            $message = _CR_MA_AREYOUSUREDELBLOCK;
            xoops_confirm($hiddens, 'manage.php', $message);
            break;
        }

        if ($block > 0) {
            $edit_block =& $blockHandler->get($block);
        } else {
            $edit_block = $blockHandler->create(false);
            $edit_block->setVar('classroomid', $classroom);
        }
        echo "<div><a href='classroom.php?cr=" . $classroom . "'>" . _CR_MA_BACKTOCLASSROOM . '</a></div>';

        require __DIR__ . '/include/blockform.inc.php';

        echo $blockHandler->listBlocks($classroom);

        $bform->display();
        break;

    case 'classblock':
        $blockid           = isset($_REQUEST['b']) ? (int)$_REQUEST['b'] : redirect_header('index.php', 3, _CR_ER_NOBLOCKSELECTED);
        $blockHandler      = $helper->getHandler('Block');
        $classblockHandler = $helper->getHandler('Classblock');

        $block                     =& $blockHandler->get($blockid);
        $xoopsOption['permission'] = 'classroom';
        $xoopsOption['itemid']     = $block->getVar('classroomid');

        if (!$gpermHandler->checkRight($xoopsOption['permission'], $xoopsOption['itemid'], $groups, $xoopsModule->getVar('mid'))) {
            $classroomHandler = $helper->getHandler('Classroom');
            $thisclassroom    =& $classroomHandler->get($block->getVar('classroomid'));
            if ($thisclassroom->getVar('owner') != $xoopsUser->getVar('uid')) {
                redirect_header('index.php', 2, _NOPERM);
            }
        }
        require XOOPS_ROOT_PATH . '/header.php';

        if (isset($_POST['submit'])) {
            $classblockHandler->updateInsert($block);
            if (0 == count($classblockHandler->getErrors())) {
                redirect_header('manage.php?op=classblock&amp;b=' . $block->getVar('blockid'), 3, _CR_MA_CLASSBLOCKSAVED);
            }
            xoops_error(implode('<br>' . $classblockHandler->getErrors()));
        }

        $settings = $classblockHandler->getSettings($block);
        echo "<div><a href='manage.php?op=block&amp;cr=" . $block->getVar('classroomid') . "'>" . _CR_MA_BACKTOBLOCK . '</a></div>';
        require __DIR__ . '/include/classblockform.inc.php';
        $cbform->display();
        break;

    case 'editblock':
        $blockid      = isset($_REQUEST['blockid']) ? (int)$_REQUEST['blockid'] : redirect_header('index.php', 3, _CR_ER_NOBLOCKSELECTED);
        $blockHandler = $helper->getHandler('Block');

        $thisblock =& $blockHandler->get($blockid);

        if (!$gpermHandler->checkRight('classroom', $thisblock->getVar('classroomid'), $groups, $xoopsModule->getVar('mid'))) {
            $classroomHandler = $helper->getHandler('Classroom');
            $thisclassroom    =& $classroomHandler->get($thisblock->getVar('classroomid'));
            if ($thisclassroom->getVar('owner') != $xoopsUser->getVar('uid')) {
                redirect_header('index.php', 2, _NOPERM);
            }
        }
        require XOOPS_ROOT_PATH . '/header.php';
        if (isset($_POST['submit'])) {
            if ($thisblock->update()) {
                redirect_header('manage.php?op=editblock&amp;blockid=' . $thisblock->getVar('blockid'), 3, _CR_MA_BLOCKSAVED);
            }
            echo($thisblock->getHtmlErrors());
        }
        echo "<div><a href='manage.php?op=block&amp;cr=" . $thisblock->getVar('classroomid') . "'>" . _CR_MA_BACKTOBLOCK . '</a></div>';
        //redirect_header('manage.php?op=classblock&amp;b='.$thisblock->getVar('blockid'), 3, _CR_ER_BLOCKNOTSAVED);
        $thisblock->buildForm();
        break;

    case 'delete':
        switch ($_POST['item']) {
            case 'school':
                if (!$xoopsUser->isAdmin($xoopsModule->getVar('mid'))) {
                    redirect_header('index.php', 2, _NOPERM);
                }
                $schoolHandler = $helper->getHandler('School');
                $thisschool    =& $schoolHandler->get($_POST['itemid']);
                if ($schoolHandler->delete($thisschool)) {
                    $schoolHandler->updateCache();
                    redirect_header('index.php', 2, _CR_MA_SCHOOLDELETED);
                }
                echo 'error';
                break;

            case 'division':
                $divisionHandler = $helper->getHandler('Division');
                $thisdiv    =& $divisionHandler->get($_POST['itemid']);
                if (!$gpermHandler->checkRight('school', $thisdiv->getVar('schoolid'), $groups, $xoopsModule->getVar('mid'))) {
                    redirect_header('index.php', 2, _NOPERM);
                }

                if ($divisionHandler->delete($thisdiv)) {
                    $schoolHandler = $helper->getHandler('School');
                    $thisschool    =& $schoolHandler->get($$thisdiv->getVar('schoolid'));
                    $thisschool->updateCache();
                    redirect_header('index.php', 2, _CR_MA_DIVISIONDELETED);
                }
                echo 'error';
                break;

            case 'classroom':
                $classroomHandler = $helper->getHandler('Classroom');
                $thiscr    =& $classroomHandler->get($_POST['itemid']);
                if (!$gpermHandler->checkRight('division', $thiscr->getVar('divisionid'), $groups, $xoopsModule->getVar('mid'))) {
                    redirect_header('index.php', 2, _NOPERM);
                }
                if ($classroomHandler->delete($thiscr)) {
                    $divisionHandler = $helper->getHandler('Division');
                    $thisdiv    =& $divisionHandler->get($thiscr->getVar('divisionid'));
                    $thisdiv->updateCache();
                    redirect_header('index.php', 2, _CR_MA_CLASSROOMDELETED);
                }
                echo 'error';
                break;

            case 'class':
                $classHandler     = $helper->getHandler('ClassroomClass');
                $classroomHandler = $helper->getHandler('Classroom');
                $thisclass        =& $classHandler->get($_POST['itemid']);
                $thisclassroom    =& $classroomHandler->get($thisclass->getVar('classroomid'));
                if (!$gpermHandler->checkRight('classroom', $thisclass->getVar('classroomid'), $groups, $xoopsModule->getVar('mid')) && $thisclassroom->getVar('owner' != $xoopsUser->getVar('uid'))) {
                    redirect_header('index.php', 2, _NOPERM);
                }
                if ($classHandler->delete($thisclass)) {
                    $thisclassroom->updateCache();
                    redirect_header('index.php', 2, _CR_MA_CLASSDELETED);
                }
                echo 'error';
                break;

            case 'block':
                $classroomHandler = $helper->getHandler('Classroom');
                $blockHandler     = $helper->getHandler('Block');
                $thisblock        =& $blockHandler->get($_REQUEST['itemid']);
                $thisclassroom    =& $classroomHandler->get($thisblock->getVar('classroomid'));
                if (!$gpermHandler->checkRight('classroom', $thisblock->getVar('classroomid'), $groups, $xoopsModule->getVar('mid')) && $thisclassroom->getVar('owner' != $xoopsUser->getVar('uid'))) {
                    redirect_header('index.php', 2, _NOPERM);
                }
                $message = _CR_ER_BLOCKNOTDELETED;
                if ($blockHandler->delete($thisblock)) {
                    $message = _CR_MA_BLOCKDELETED;
                }
                redirect_header('manage.php?op=block&cr=' . $thisblock->getVar('classroomid'), 2, $message);
                break;
        }
        break;

    case 'deleteitem':
        $classroomHandler = $helper->getHandler('Classroom');
        $blockHandler     = $helper->getHandler('Block');
        $thisblock        =& $blockHandler->get($_REQUEST['b']);
        $thisclassroom    =& $classroomHandler->get($thisblock->getVar('classroomid'));
        if (!$gpermHandler->checkRight('classroom', $thisblock->getVar('classroomid'), $groups, $xoopsModule->getVar('mid')) && $thisclassroom->getVar('owner' != $xoopsUser->getVar('uid'))) {
            redirect_header('index.php', 2, _NOPERM);
        }
        $message = _CR_ER_ITEMNOTDELETED;
        if ($thisblock->deleteItem()) {
            $thisblock->updateCache();
            $message = _CR_MA_ITEMDELETED;
        }
        redirect_header('manage.php?op=editblock&amp;blockid=' . $thisblock->getVar('blockid'), 2, $message);
        break;
}

require XOOPS_ROOT_PATH . '/footer.php';
