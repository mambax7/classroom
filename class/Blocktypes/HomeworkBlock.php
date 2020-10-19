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
 * HomeworkBlocktype class
 *
 * @package    modules
 * @subpackage Classroom
 */
class HomeworkBlock extends Block
{
    public $datetable;

    public function __construct()
    {
        parent::__construct();
        $this->datetable = $this->db->prefix('classroom_homework');
        $this->assignVar('template', 'cr_blocktype_homework.tpl');
        $this->assignVar('blocktypename', 'Homework');
        $this->assignVar('bcachetime', 2592000);
    }

    /**
     * Builds a form for the block
     *
     */
    public function buildForm()
    {
        $assigned = $due = time();
        $text     = '';

        $myts           = \MyTextSanitizer::getInstance();
        $currententries = '<table>';
        $entries        = $this->getItems();
        if (count($entries) > 0) {
            $class          = '';
            $currententries .= '<tr><th>' . _CR_MA_ASSIGNED . '</th><th>' . _CR_MA_ASSIGNMENT . '</th><th>' . _CR_MA_DUE . '</th><th>' . _CR_MA_EDIT . '</th><th>' . _CR_MA_DELETE . '</th></tr>';
            foreach ($entries as $id => $entry) {
                $class          = isset($class) && 'odd' == $class ? 'even' : 'odd';
                $currententries .= "<tr class='" . $class . "'><td>" . formatTimestamp($entry['assigned'], 's', '0') . '
                                    <td>' . htmlspecialchars($entry['value']) . '</td>
                                    <td>' . formatTimestamp($entry['due'], 's', '0') . "</td>
                                    <td><a href='manage.php?op=editblock&amp;blockid=" . $this->getVar('blockid') . '&amp;fieldid=' . $id . "'>" . _CR_MA_EDIT . "</a></td>
                                    <td><a href='manage.php?op=deleteitem&amp;b=" . $this->getVar('blockid') . '&amp;id=' . $id . "'>" . _CR_MA_DELETE . '</a></td></tr>';
                if (isset($_GET['fieldid']) && $id == $_GET['fieldid']) {
                    $assigned = $entry['assigned'];
                    $text     = $entry['value'];
                }
            }
        }
        $currententries .= '</table>';

        require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';
        $form = new \XoopsThemeForm('', 'homeworkform', 'manage.php');
        if (count($entries) > 0) {
            $form->addElement(new \XoopsFormLabel(_CR_MA_CURRENTENTRIES, $currententries));
        }

        $form->addElement(new \XoopsFormText(_CR_MA_ASSIGNMENT, 'text', 40, 60, $text), true);
        $form->addElement(new \XoopsFormTextDateSelect(_CR_MA_ASSIGNED, 'assigned', 15, $assigned));
        $form->addElement(new \XoopsFormTextDateSelect(_CR_MA_DUE, 'due', 15, $due));
        if (isset($_GET['fieldid']) && $_GET['fieldid'] > 0) {
            $form->addElement(new \XoopsFormHidden('fieldid', $_GET['fieldid']));
        }

        $form->addElement(new \XoopsFormHidden('op', 'editblock'));
        $form->addElement(new \XoopsFormHidden('blockid', $this->getVar('blockid')));
        $form->addElement(new \XoopsFormButton('', 'submit', _CR_MA_SUBMIT, 'submit'));
        $form->display();
    }

    /**
     * Builds a block
     *
     * @return array
     */
    public function buildBlock()
    {
        $myts    = \MyTextSanitizer::getInstance();
        $block   = [];
        $entries = $this->getItems();
        foreach ($entries as $key => $entry) {
            $block['entries'][$key]['value']    = htmlspecialchars($entry['value']);
            $block['entries'][$key]['assigned'] = formatTimestamp($entry['assigned'], 's', '0');
            $block['entries'][$key]['due']      = formatTimestamp($entry['due'], 's', '0');
        }

        return $block;
    }

    /**
     * Performs actions following buildForm submissal
     *
     */
    public function updateBlock()
    {
        $myts = \MyTextSanitizer::getInstance();
        if (isset($_POST['fieldid']) && $_POST['fieldid'] > 0) {
            if ('' != $_POST['text']) {
                $sql = 'UPDATE ' . $this->table . " SET value='" . $myts->addSlashes($_POST['text']) . "' WHERE blockid=" . $this->getVar('blockid') . ' AND fieldid=' . (int)$_POST['fieldid'];
                $this->db->query($sql);
                $sql = 'UPDATE ' . $this->datetable . ' SET assigned=' . strtotime($_POST['assigned']) . ', due=' . strtotime($_POST['due']) . ' WHERE fieldid=' . (int)$_POST['fieldid'];

                return $this->db->query($sql);
            }
        } else {
            if ('' != $_POST['text']) {
                $sql = 'INSERT INTO ' . $this->table . ' (blockid, value, weight) VALUES (' . (int)$this->getVar('blockid') . ", '" . $myts->addSlashes($_POST['text']) . "', 0)";
                $this->db->query($sql);
                $fieldid = $this->db->getInsertId();
                $sql     = 'INSERT INTO ' . $this->datetable . ' (fieldid, assigned, due) VALUES (' . $fieldid . ', ' . strtotime($_POST['assigned']) . ', ' . strtotime($_POST['due']) . ')';
                $this->db->query($sql);
            }
        }

        return true;
    }

    /** Deletes an item in a homework list
     *
     * @return bool
     */
    public function deleteItem()
    {
        $id  = (int)$_REQUEST['id'];
        $sql = 'DELETE FROM ' . $this->table . ' WHERE blockid=' . $this->getVar('blockid') . ' AND fieldid=' . $id;
        if ($this->db->queryF($sql)) {
            $sql = 'DELETE FROM ' . $this->datetable . ' WHERE fieldid=' . $id;
            if ($this->db->queryF($sql)) {
                return true;
            }
        }

        return false;
    }

    /** Deletes all block-specific items prior to block deletion
     *
     * @return bool
     */
    public function delete()
    {
        $items = $this->getItems();
        $sql   = 'DELETE FROM ' . $this->datetable . ' WHERE fieldid IN (' . implode(',', array_keys($items)) . ')';

        return $this->db->query($sql);
    }

    /**
     * Retrieves all homework items for a block
     *
     * @staticvar array entries ordered by day
     * @return array
     */
    public function getItems()
    {
        $ret    = [];
        $sql    = 'SELECT * FROM ' . $this->table . ' v, ' . $this->datetable . ' d WHERE d.fieldid = v.fieldid AND v.blockid=' . $this->getVar('blockid') . ' ORDER BY d.due DESC, v.value';
        $result = $this->db->query($sql);
        while (false !== ($row = $this->db->fetchArray($result))) {
            $ret[$row['fieldid']] = $row;
        }

        return $ret;
    }
}
