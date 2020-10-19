<?php

namespace XoopsModules\Classroom\Blocktypes;

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

use XoopsModules\Classroom\{
    Block,
    Helper,
    XoopsHeadlineRenderer
};
/** @var Helper $helper */

/**
 * LinkBlock class
 *
 * @package    modules
 * @subpackage Classroom
 */
class RssBlock extends Block
{
    public function __construct()
    {
        parent::__construct();
        $this->assignVar('template', 'cr_blocktype_rss.tpl');
        $this->assignVar('blocktypename', 'RSS Feed');
        $this->assignVar('bcachetime', 0);
    }

    /**
     * Builds a form for the block
     *
     */
    public function buildForm()
    {
        require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';
        $helper = Helper::getInstance();

        echo $this->showFeeds();

        if (isset($_GET['feedid'])) {
            $feed = $this->getFeed($_GET['feedid']);
        } else {
            $headlineHandler = $helper->getHandler('Headline');
            $feed            = $headlineHandler->create();
        }

        $form = new \XoopsThemeForm('', 'rssform', 'manage.php');

        $form->addElement(new \XoopsFormText(_CR_MA_SITENAME, 'headline_name', 50, 255, $feed->getVar('headline_name')), true);
        $form->addElement(new \XoopsFormText(_CR_MA_URL, 'headline_url', 50, 255, $feed->getVar('headline_url')), true);
        $form->addElement(new \XoopsFormText(_CR_MA_URLEDFXML, 'headline_rssurl', 50, 255, $feed->getVar('headline_rssurl')), true);
        $form->addElement(new \XoopsFormText(_CR_MA_WEIGHT, 'headline_weight', 4, 3, $feed->getVar('headline_weight')));
        $enc_sel = new \XoopsFormSelect(_CR_MA_ENCODING, 'headline_encoding', $feed->getVar('headline_encoding'));
        $enc_sel->addOptionArray(['utf-8' => 'UTF-8', 'iso-8859-1' => 'ISO-8859-1', 'us-ascii' => 'US-ASCII']);
        $form->addElement($enc_sel);
        $cache_sel = new \XoopsFormSelect(_CR_MA_CACHETIME, 'headline_cachetime', $feed->getVar('headline_cachetime'));
        $cache_sel->addOptionArray(['3600' => _HOUR, '18000' => sprintf(_HOURS, 5), '86400' => _DAY, '259200' => sprintf(_DAYS, 3), '604800' => _WEEK, '2592000' => _MONTH]);
        $form->addElement($cache_sel);

        $form->addElement(new \XoopsFormRadioYN(_CR_MA_DISPLAY, 'headline_display', $feed->getVar('headline_display'), _YES, _NO));
        $bmax_sel = new \XoopsFormSelect(_CR_MA_DISPMAX, 'headline_blockmax', $feed->getVar('headline_blockmax'));
        $bmax_sel->addOptionArray(['1' => 1, '5' => 5, '10' => 10, '15' => 15, '20' => 20, '25' => 25, '30' => 30]);
        $form->addElement($bmax_sel);

        $form->addElement(new \XoopsFormText(_CR_MA_TITLELENGTH, 'headline_titlelength', 4, 3, $feed->getVar('headline_titlelength')), true);

        if ($feed->getVar('headline_id') > 0) {
            $form->addElement(new \XoopsFormHidden('feedid', $feed->getVar('headline_id')));
        }
        $form->addElement(new \XoopsFormHidden('op', 'editblock'));
        $form->addElement(new \XoopsFormHidden('blockid', $this->getVar('blockid')));
        $form->addElement(new \XoopsFormButton('', 'submit', _CR_MA_SUBMIT, 'submit'));
        $form->display();
    }

    /**
     * Builds a classroomblock
     *
     * @return array
     */
    public function buildBlock()
    {
        global $xoopsConfig;
        $block     = [];
        $headlines = $this->getFeeds(true);
        $count     = count($headlines);
        for ($i = 0; $i < $count; $i++) {
//            require_once XOOPS_ROOT_PATH . '/modules/classroom/class/headlinerenderer.php';
            $renderer = new XoopsHeadlineRenderer($headlines[$i]);
            if (!$renderer->renderBlock()) {
                if (2 == $xoopsConfig['debug_mode']) {
                    $block['feeds'][] = sprintf(_HL_FAILGET, $headlines[$i]->getVar('headline_name')) . '<br>' . $renderer->getErrors();
                }
                continue;
            }
            $block['feeds'][] = $renderer->getBlock();
        }

        return $block;
    }

    /**
     * Performs actions following buildForm submissal
     *
     */
    public function updateBlock()
    {
        $headlineHandler = $helper->getHandler('Headline');
        $obj   = $headlineHandler->create();
        if (isset($_POST['feedid'])) {
            $obj->setVar('headline_id', (int)$_POST['feedid']);
        }
        $obj->setVar('headline_blockid', $this->getVar('blockid'));
        $obj->setVar('headline_name', $_POST['headline_name']);
        $obj->setVar('headline_url', $_POST['headline_url']);
        $obj->setVar('headline_rssurl', $_POST['headline_rssurl']);
        $obj->setVar('headline_weight', (int)$_POST['headline_weight']);
        $obj->setVar('headline_encoding', $_POST['headline_encoding']);
        $obj->setVar('headline_cachetime', (int)$_POST['headline_cachetime']);
        $obj->setVar('headline_display', (int)$_POST['headline_display']);
        $obj->setVar('headline_blockmax', (int)$_POST['headline_blockmax']);
        $obj->setVar('headline_titlelength', (int)$_POST['headline_titlelength']);

        return $headlineHandler->insert($obj);
    }

    /** Deletes an item in a rule block
     *
     * @return bool
     */
    public function deleteItem()
    {
        $id    = (int)$_REQUEST['id'];
        $headlineHandler = $helper->getHandler('Headline');
        $obj   =& $headlineHandler->get($id);

        return $headlineHandler->delete($obj);
    }

    /**
     * Performs maintenance deletions following block delete
     *
     * @return bool
     */
    public function delete()
    {
        $sql = sprintf('DELETE FROM %s WHERE headline_blockid = %u', $this->db->prefix('classroom_rss'), $this->getVar('blockid'));
        if (!$this->db->query($sql)) {
            return false;
        }

        return true;
    }

    /**
     * /* Show rules already present in the block
     *
     * @return string
     */
    public function showFeeds()
    {
        $feeds = $this->getFeeds();
        $ret   = '<table>';
        $ret   .= "<tr class='head'><td>" . _CR_MA_FEEDNAME . '</td><td>' . _CR_MA_ENCODING . '</td><td>' . _CR_MA_DISPLAY . '</td><td>' . _CR_MA_WEIGHT . '</td><td>' . _CR_MA_EDIT . '</td><td>' . _CR_MA_DELETE . '</td></tr>';
        foreach ($feeds as $key => $feed) {
            $class   = isset($class) && 'odd' == $class ? 'even' : 'odd';
            $display = $feed->getVar('headline_display') ? _YES : _NO;
            $ret     .= "<tr class='" . $class . "'>";
            $ret     .= '<td>' . $feed->getVar('headline_name') . '</td>';
            $ret     .= '<td>' . $feed->getVar('headline_encoding') . '</td>';
            $ret     .= '<td>' . $display . '</td>';
            $ret     .= '<td>' . $feed->getVar('headline_weight') . '</td>';
            $ret     .= "<td><a href='manage.php?op=editblock&amp;blockid=" . $this->getVar('blockid') . '&amp;feedid=' . $feed->getVar('headline_id') . "'>" . _CR_MA_EDIT . '</a></td>';
            $ret     .= "<td><a href='manage.php?op=deleteitem&amp;b=" . $this->getVar('blockid') . '&amp;id=' . $feed->getVar('headline_id') . "'>" . _CR_MA_DELETE . '</a></td>';
            $ret     .= '</tr>';
        }
        $ret .= '</table>';

        return $ret;
    }

    /**
     * Retrieves single feed
     *
     * @param $id
     * @return object
     */
    public function getFeed($id)
    {
        $id              = (int)$id;
        $headlineHandler = $helper->getHandler('Headline');

        return $headlineHandler->get($id);
    }

    /**
     * Retrieves all feeds
     *
     * @param bool $visible_only whether to only fetch visible feeds
     *
     * @return object
     */
    public function getFeeds($visible_only = false)
    {
        $helper = Helper::getInstance();
        $criteria = new \CriteriaCompo(new \Criteria('headline_blockid', $this->getVar('blockid')));
        if (false !== $visible_only) {
            $criteria->add(new \Criteria('headline_display', 1));
        }
        $headlineHandler = $helper->getHandler('Headline');

        return $headlineHandler->getObjects($criteria);
    }
}
