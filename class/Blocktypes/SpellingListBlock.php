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

use XoopsModules\Classroom\{Block,
    Helper
};

/** @var Helper $helper */

/**
 * SpellingListBlock class
 *
 * @package    modules
 * @subpackage Classroom
 */
class SpellingListBlock extends Block
{
    public function __construct()
    {
        parent::__construct();
        $this->assignVar('template', 'cr_blocktype_spelling.tpl');
        $this->assignVar('blocktypename', 'Spelling List');
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

        echo $this->showItems();

        $item = isset($_GET['id']) ? $this->getItem($_GET['id']) : $item = ['word' => '', 'description' => '', 'weight' => 0];

        $form = new \XoopsThemeForm('', 'spellingform', 'manage.php');
        $form->addElement(new \XoopsFormText(_CR_MA_WORD, 'word', 40, 50, htmlspecialchars($item['word'], ENT_QUOTES | ENT_HTML5)), true);
        $form->addElement(new \XoopsFormDhtmlTextArea(_CR_MA_DESCRIPTION, 'description', htmlspecialchars($item['description'], ENT_QUOTES | ENT_HTML5), 10, 40), true);
        $form->addElement(new \XoopsFormText(_CR_MA_WEIGHT, 'weight', 10, 10, (int)$item['weight']));
        if (isset($item['fieldid'])) {
            $form->addElement(new \XoopsFormHidden('id', $item['fieldid']));
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
        $block['items'] = $this->getItems();
        $block['id']    = $this->getVar('blockid');

        return $block;
    }

    /**
     * Performs actions following buildForm submissal
     *
     */
    public function updateBlock()
    {
        $myts = \MyTextSanitizer::getInstance();
        if (isset($_POST['id']) && $_POST['id'] > 0) {
            $sql = 'UPDATE ' . $this->table . "
                    SET value='" . $myts->addSlashes($_POST['word']) . ';' . $myts->addSlashes($_POST['description']) . "',
                        weight=" . (int)$_POST['weight'] . '
                    WHERE blockid=' . $this->getVar('blockid') . ' AND fieldid=' . (int)$_POST['id'];
        } else {
            $sql = 'INSERT INTO ' . $this->table . ' (blockid, weight, value) VALUES
                    (' . $this->getVar('blockid') . ', ' . (int)$_POST['weight'] . ", '" . $myts->addSlashes($_POST['word']) . ';' . $myts->addSlashes($_POST['description']) . "')";
        }

        return $this->db->query($sql);
    }

    /**
     * Performs actions following user interaction in the displayed block
     *
     */
    public function interact()
    {
        global $xoopsTpl;
        $myts                = \MyTextSanitizer::getInstance();
        $item                = $this->getItem($_GET['id']);
        $item['word']        = htmlspecialchars($item['word'], ENT_QUOTES | ENT_HTML5);
        $item['description'] = $myts->displayTarea($item['description'], 1);
        $xoopsTpl->assign('item', $item);
        $xoopsTpl->display('db:cr_blocktype_spelling_details.tpl');
        exit();
    }

    /** Deletes an item in a block
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
     * /* Show items already present in the block
     *
     * @return string
     */
    public function showItems()
    {
        $items = $this->getItems();
        $ret   = '<table>';
        $ret   .= "<tr class='head'><td>" . _CR_MA_LINK . '</td><td>' . _CR_MA_URL . '</td><td>' . _CR_MA_WEIGHT . '</td><td>' . _CR_MA_EDIT . '</td><td>' . _CR_MA_DELETE . '</td></tr>';
        foreach ($items as $id => $item) {
            $class = isset($class) && 'odd' == $class ? 'even' : 'odd';
            $ret   .= "<tr class='" . $class . "'>";
            $ret   .= '<td>' . $item['word'] . '</td><td>' . xoops_substr($item['description'], 0, 50) . '</td><td>' . $item['weight'] . '</td>';
            $ret   .= "<td><a href='manage.php?op=editblock&amp;blockid=" . $this->getVar('blockid') . '&amp;id=' . $id . "'>" . _CR_MA_EDIT . '</a></td>';
            $ret   .= "<td><a href='manage.php?op=deleteitem&amp;b=" . $this->getVar('blockid') . '&amp;id=' . $id . "'>" . _CR_MA_DELETE . '</a></td>';
            $ret   .= '</tr>';
        }
        $ret .= '</table>';

        return $ret;
    }

    /**
     * retreive item from database
     *
     * @param int $id item id
     *
     * @return array
     */
    public function getItem($id)
    {
        $id                 = (int)$id;
        $sql                = 'SELECT * FROM ' . $this->table . ' WHERE fieldid=' . $id;
        $result             = $this->db->query($sql);
        $row                = $this->db->fetchArray($result);
        $values             = explode(';', $row['value']);
        $row['id']          = $row['fieldid'];
        $row['word']        = $values[0];
        $row['description'] = $values[1];

        return $row;
    }

    /**
     * Retrieves all items in a block
     *
     * @return array
     */
    public function getItems()
    {
        $ret    = [];
        $myts   = \MyTextSanitizer::getInstance();
        $sql    = 'SELECT * FROM ' . $this->table . ' WHERE blockid=' . $this->getVar('blockid') . ' ORDER BY weight';
        $result = $this->db->query($sql);
        while (false !== ($row = $this->db->fetchArray($result))) {
            $values               = explode(';', $row['value']);
            $row['word']          = $values[0];
            $row['description']   = $values[1];
            $ret[$row['fieldid']] = [
                'id'          => $row['fieldid'],
                'word'        => htmlspecialchars($row['word'], ENT_QUOTES | ENT_HTML5),
                'description' => $myts->displayTarea($row['description'], 1),
                'weight'      => $row['weight'],
            ];
            unset($values);
        }

        return $ret;
    }
}
