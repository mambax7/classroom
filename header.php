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

use XoopsModules\Classroom\{Helper
};

/** @var Helper $helper
 * {@internal $helper defined in ./include/common.php }}
 */

require dirname(__DIR__, 2) . '/mainfile.php';

require __DIR__ . '/preloads/autoloader.php';

$gpermHandler = xoops_getHandler('groupperm');
$groups       = $xoopsUser ? $xoopsUser->getGroups() : XOOPS_GROUP_ANONYMOUS;
$edit_mode    = 0;
if (isset($_SESSION['cr_edit_mode']) && 1 == $_SESSION['cr_edit_mode']) {
    if ($gpermHandler->checkRight($xoopsOption['permission'], $xoopsOption['itemid'], $groups, $xoopsModule->getVar('mid'))) {
        $xoopsConfig['module_cache'][$xoopsModule->getVar('mid')] = 0;
        $edit_mode                                                = 1;
    }
}

require XOOPS_ROOT_PATH . '/header.php';
$xoopsTpl->assign('edit_mode', $edit_mode);

$moduleDirName = basename(__DIR__);

$helper = Helper::getInstance();

$myts = \MyTextSanitizer::getInstance();

if (!isset($GLOBALS['xoTheme']) || !is_object($GLOBALS['xoTheme'])) {
    require $GLOBALS['xoops']->path('class/theme.php');
    $GLOBALS['xoTheme'] = new \xos_opal_Theme();
}

// Load language files
$helper->loadLanguage('main');

if (!isset($GLOBALS['xoopsTpl']) || !($GLOBALS['xoopsTpl'] instanceof XoopsTpl)) {
    require $GLOBALS['xoops']->path('class/template.php');
    $xoopsTpl = new \XoopsTpl();
}
