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
    Helper
};
/** @var Helper $helper */


/**
 * LinkBlock class
 *
 * @package    modules
 * @subpackage Classroom
 */
class LinkBlock extends Block
{
    public function __construct()
    {
        parent::__construct();
        $this->assignVar('template', 'cr_blocktype_link.tpl');
        $this->assignVar('blocktypename', 'Link');
        $this->assignVar('bcachetime', 2592000);
    }

    /**
     * Builds a form for the block
     *
     */
    public function buildForm()
    {
        $myts = \MyTextSanitizer::getInstance();
        require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';

        echo $this->showLinks();

        if (isset($_GET['linkid'])) {
            $link = $this->getLink($_GET['linkid']);
        } else {
            $link = ['link' => '', 'url' => '', 'weight' => 0];
        }

        $form = new \XoopsThemeForm('', 'linkform', 'manage.php');
        $form->addElement(new \XoopsFormText(_CR_MA_LINK, 'link', 40, 100, htmlspecialchars($link['link'])), true);
        $form->addElement(new \XoopsFormText(_CR_MA_URL, 'url', 40, 100, htmlspecialchars($link['url'])), true);
        $form->addElement(new \XoopsFormText(_CR_MA_WEIGHT, 'weight', 10, 10, (int)$link['weight']));
        if (isset($link['fieldid'])) {
            $form->addElement(new \XoopsFormHidden('linkid', $link['fieldid']));
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
        $block['links'] = $this->getLinks();

        return $block;
    }

    /**
     * Performs actions following buildForm submissal
     *
     */
    public function updateBlock()
    {
        $myts = \MyTextSanitizer::getInstance();
        if (isset($_POST['linkid']) && $_POST['linkid'] > 0) {
            $sql = 'UPDATE ' . $this->table . "
                    SET value='" . $myts->addSlashes($_POST['link']) . ';' . $myts->addSlashes($_POST['url']) . "',
                        weight=" . (int)$_POST['weight'] . '
                    WHERE blockid=' . $this->getVar('blockid') . ' AND fieldid=' . (int)$_POST['linkid'];
        } else {
            $sql = 'INSERT INTO ' . $this->table . ' (blockid, weight, value) VALUES
                    (' . $this->getVar('blockid') . ', ' . (int)$_POST['weight'] . ", '" . $myts->addSlashes($_POST['link']) . ';' . $myts->addSlashes($_POST['url']) . "')";
        }

        return $this->db->query($sql);
    }

    /** Deletes an item in a link block
     *
     * @return bool
     */
    public function deleteItem()
    {
        $id  = (int)$_REQUEST['id'];
        $sql = 'DELETE FROM ' . $this->table . ' WHERE blockid=' . $this->getVar('blockid') . ' AND fieldid=' . $id;
        if ($this->db->queryF($sql)) {
            return true;
        }

        return false;
    }

    /** Deletes all block-specific items prior to block deletion
     *
     * @return bool
     */
    public function delete()
    {
        return true;
    }

    /**
     * /* Show links already present in the block
     *
     * @return string
     */
    public function showLinks()
    {
        $links = $this->getLinks();
        $ret   = '<table>';
        $ret   .= "<tr class='head'><td>" . _CR_MA_LINK . '</td><td>' . _CR_MA_URL . '</td><td>' . _CR_MA_WEIGHT . '</td><td>' . _CR_MA_EDIT . '</td><td>' . _CR_MA_DELETE . '</td></tr>';
        foreach ($links as $linkid => $row) {
            $class = isset($class) && 'odd' == $class ? 'even' : 'odd';
            $ret   .= "<tr class='" . $class . "'>";
            $ret   .= '<td>' . $row['link'] . '</td><td>' . $row['url'] . '</td><td>' . $row['weight'] . '</td>';
            $ret   .= "<td><a href='manage.php?op=editblock&amp;blockid=" . $this->getVar('blockid') . '&amp;linkid=' . $linkid . "'>" . _CR_MA_EDIT . '</a></td>';
            $ret   .= "<td><a href='manage.php?op=deleteitem&amp;b=" . $this->getVar('blockid') . '&amp;id=' . $linkid . "'>" . _CR_MA_DELETE . '</a></td>';
            $ret   .= '</tr>';
        }
        $ret .= '</table>';

        return $ret;
    }

    /**
     * retreive link from database
     *
     * @param int $id link id
     *
     * @return array
     */
    public function getLink($id)
    {
        $id          = (int)$id;
        $sql         = 'SELECT * FROM ' . $this->table . ' WHERE fieldid=' . $id;
        $result      = $this->db->query($sql);
        $row         = $this->db->fetchArray($result);
        $values      = explode(';', $row['value']);
        $row['link'] = $values[0];
        $row['url']  = $values[1];

        return $row;
    }

    /**
     * Retrieves all links in a block
     *
     * @return array
     */
    public function getLinks()
    {
        $ret    = [];
        $myts   = \MyTextSanitizer::getInstance();
        $sql    = 'SELECT * FROM ' . $this->table . ' WHERE blockid=' . $this->getVar('blockid') . ' ORDER BY weight';
        $result = $this->db->query($sql);
        while (false !== ($row = $this->db->fetchArray($result))) {
            $values               = explode(';', $row['value']);
            $row['link']          = $values[0];
            $row['url']           = $values[1];
            $ret[$row['fieldid']] = [
                'linkid' => $row['fieldid'],
                'link'   => htmlspecialchars($row['link']),
                'url'    => $row['url'],
                'weight' => $row['weight'],
            ];
            unset($values);
        }

        return $ret;
    }
}
