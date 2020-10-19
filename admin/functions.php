<?php

use Xmf\Module\Admin;
use XoopsModules\Classroom\{Helper
};

/** @var Admin $adminObject */
/** @var Helper $helper */

/**
 * @param int $currentoption
 * @return string
 */
function adminmenu($currentoption = 0)
{
    global $xoopsModule, $xoopsConfig;
    if (file_exists(XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->getVar('dirname') . '/language/' . $xoopsConfig['language'] . '/modinfo.php')) {
        require_once dirname(__DIR__) . '/language/' . $xoopsConfig['language'] . '/modinfo.php';
    } else {
        require_once dirname(__DIR__) . '/language/english/modinfo.php';
    }
    require __DIR__ . '/menu.php';
    $tblColors = [];
    $num_items = count($adminmenu);
    for ($i = 0; $i < $num_items; $i++) {
        $tblColors[$i] = '#DDE';
    }
    $tblColors[$currentoption] = 'white';

    $output = '<div id="navcontainer"><ul style="padding: 3px 0; margin-left: 0; font: bold 12px Verdana, sans-serif; ">';
    foreach ($adminmenu as $id => $menuitem) {
        $output .= "<li style=\"list-style: none; margin: 0; display: inline; \"><a href='"
                   . XOOPS_URL
                   . '/modules/'
                   . $xoopsModule->getVar('dirname')
                   . '/'
                   . $menuitem['link']
                   . "' style=\"padding: 3px 0.5em; margin-left: 3px; border: 1px solid #778; background: "
                   . $tblColors[$id]
                   . '; text-decoration: none; ">'
                   . $menuitem['title']
                   . '</a></li>';
    }
    $output .= '</ul></div>';

    return $output;
}
