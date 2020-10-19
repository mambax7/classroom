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
 * QuoteBlock class
 *
 * @package    modules
 * @subpackage Classroom
 */
class QuoteBlock extends Block
{
    public function __construct()
    {
        parent::__construct();
        $this->assignVar('template', 'cr_blocktype_quote.tpl');
        $this->assignVar('blocktypename', 'Quote');
        //One day cache time
        $this->assignVar('bcachetime', 86400);
    }

    /**
     * Builds a form for the block
     *
     */
    public function buildForm()
    {
        $myts = \MyTextSanitizer::getInstance();
        require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';

        echo $this->showQuotes();

        if (isset($_GET['quoteid'])) {
            $quote = $this->getQuote($_GET['quoteid']);
        } else {
            $quote = ['quote' => '', 'author' => '', 'weight' => 0];
        }

        $form = new \XoopsThemeForm('', 'quoteform', 'manage.php');
        $form->addElement(new \XoopsFormText(_CR_MA_QUOTE, 'quote', 50, 255, htmlspecialchars($quote['quote'], ENT_QUOTES | ENT_HTML5)), true);
        $form->addElement(new \XoopsFormText(_CR_MA_AUTHOR, 'author', 40, 100, htmlspecialchars($quote['author'], ENT_QUOTES | ENT_HTML5)), true);
        if (isset($quote['fieldid'])) {
            $form->addElement(new \XoopsFormHidden('quoteid', $quote['fieldid']));
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
        $block['quote'] = $this->getRandomQuote();

        return $block;
    }

    /**
     * Performs actions following buildForm submissal
     *
     */
    public function updateBlock()
    {
        $myts = \MyTextSanitizer::getInstance();
        if (isset($_POST['quoteid']) && $_POST['quoteid'] > 0) {
            $sql = 'UPDATE ' . $this->table . "
                    SET value='" . $myts->addSlashes($_POST['quote']) . ';' . $myts->addSlashes($_POST['author']) . "',
                        weight=0,
                        updated=" . time() . '
                    WHERE blockid=' . $this->getVar('blockid') . ' AND fieldid=' . (int)$_POST['quoteid'];
        } else {
            $sql = 'INSERT INTO ' . $this->table . ' (blockid, weight, value, updated) VALUES
                    (' . $this->getVar('blockid') . ", 0, '" . $myts->addSlashes($_POST['quote']) . ';' . $myts->addSlashes($_POST['author']) . "', " . time() . ')';
        }

        return $this->db->query($sql);
    }

    /** Deletes a single item
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

    /**
     * /* Show quotes already present in the block
     *
     * @return string
     */
    public function showQuotes()
    {
        $quotes = $this->getQuotes();
        $ret    = '<table>';
        $ret    .= "<tr class='head'><td>" . _CR_MA_QUOTE . '</td><td>' . _CR_MA_AUTHOR . '</td><td>' . _CR_MA_EDIT . '</td><td>' . _CR_MA_DELETE . '</td></tr>';
        foreach ($quotes as $quoteid => $row) {
            $class = isset($class) && 'odd' == $class ? 'even' : 'odd';
            $ret   .= "<tr class='" . $class . "'>";
            $ret   .= '<td>' . $row['quote'] . '</td><td>' . $row['author'] . '</td>';
            $ret   .= "<td><a href='manage.php?op=editblock&amp;blockid=" . $this->getVar('blockid') . '&amp;quoteid=' . $quoteid . "'>" . _CR_MA_EDIT . '</a></td>';
            $ret   .= "<td><a href='manage.php?op=deleteitem&amp;b=" . $this->getVar('blockid') . '&amp;id=' . $quoteid . "'>" . _CR_MA_DELETE . '</a></td>';
            $ret   .= '</tr>';
        }
        $ret .= '</table>';

        return $ret;
    }

    /**
     * retrieve quote from database
     *
     * @param int $id quote id
     *
     * @return array
     */
    public function getQuote($id)
    {
        $id            = (int)$id;
        $sql           = 'SELECT * FROM ' . $this->table . ' WHERE fieldid=' . $id;
        $result        = $this->db->query($sql);
        $row           = $this->db->fetchArray($result);
        $values        = explode(';', $row['value']);
        $row['quote']  = $values[0];
        $row['author'] = $values[1];

        return $row;
    }

    /**
     * Retrieves all quotes in a block
     *
     * @return array
     */
    public function getQuotes()
    {
        $ret    = [];
        $myts   = \MyTextSanitizer::getInstance();
        $sql    = 'SELECT * FROM ' . $this->table . ' WHERE blockid=' . $this->getVar('blockid') . ' ORDER BY updated DESC';
        $result = $this->db->query($sql);
        while (false !== ($row = $this->db->fetchArray($result))) {
            $values               = explode(';', $row['value']);
            $row['quote']         = $values[0];
            $row['author']        = $values[1];
            $ret[$row['fieldid']] = [
                'quoteid' => $row['fieldid'],
                'quote'   => htmlspecialchars($row['quote'], ENT_QUOTES | ENT_HTML5),
                'author'  => htmlspecialchars($row['author'], ENT_QUOTES | ENT_HTML5),
                'weight'  => $row['weight'],
            ];
            unset($values);
        }

        return $ret;
    }

    /**
     * retrieve random quote from database
     *
     * @return array
     */
    public function getRandomQuote()
    {
        $myts     = \MyTextSanitizer::getInstance();
        $sel_time = time() - (86400 * 2);
        $sql      = 'SELECT * FROM ' . $this->table . ' WHERE blockid=' . $this->getVar('blockid') . ' AND updated < ' . $sel_time;
        $result   = $this->db->query($sql);
        $quotes   = [];
        while (false !== ($row = $this->db->fetchArray($result))) {
            $quotes[] = $row;
        }
        $num_quotes = count($quotes);
        if (0 == $num_quotes) {
            $sql      = 'SELECT * FROM ' . $this->table . ' WHERE blockid=' . $this->getVar('blockid') . ' LIMIT 0,1';
            $result   = $this->db->query($sql);
            $quotes[] = $this->db->fetchArray($result);
        }
        $random_number = rand(0, ($num_quotes - 1));
        $row           = $quotes[$random_number];
        $values        = explode(';', $row['value']);
        $row['quote']  = htmlspecialchars($values[0], ENT_QUOTES | ENT_HTML5);
        $row['author'] = htmlspecialchars($values[1], ENT_QUOTES | ENT_HTML5);
        $sql           = 'UPDATE ' . $this->table . ' SET updated=' . time() . ' WHERE fieldid=' . $row['fieldid'];
        $this->db->queryF($sql);

        return $row;
    }
}
