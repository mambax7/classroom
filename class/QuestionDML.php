<?php

namespace XoopsModules\Classroom;

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
/**
 * Classes for managing Questions in a {@link QuizBlock}
 *
 *
 * @author  Jan Keller Pedersen
 * @package modules
 */

/**
 * Question database manipulation class
 *
 * @package    modules
 * @subpackage Question
 */
class QuestionDML
{
    /**
     * Instance of database class
     * @access private
     * @var    {@link Database} object
     */
    public $db;
    /**
     * Table name
     * @access private
     * @var string
     */
    public $table;
    /**
     * Value Table name
     * @access private
     * @var string
     */
    public $valuetable;

    /**
     * Constructor sets up {@link QuestionDML} object
     * @param \XoopsDatabase $db to {@link Database} object
     * @param                $table
     */
    public function __construct(\XoopsDatabase $db, $table)
    {
        $this->db         = $db;
        $this->table      = $table;
        $this->valuetable = $this->db->prefix('classroom_value');
    }

    /**
     * Insert a Question object into the database
     *
     * @param Reference $obj to {@link Question} object to save
     * @return bool
     * @return bool
     */
    public function insertQuestion($obj)
    {
        $myts = \MyTextSanitizer::getInstance();
        $sql  = 'INSERT INTO ' . $this->valuetable . '
                    (blockid, value, weight)
                VALUES (' . (int)$obj->getVar('blockid') . ", '" . $myts->addSlashes($obj->getVar('question', 'n')) . "', " . (int)$obj->getVar('weight') . ')';
        if (!$this->db->query($sql)) {
            $obj->setErrors('INSERT INTO valuetable failed');

            return false;
        }
        $obj->setVar('questionid', $this->db->getInsertId());

        $optiona = $optionb = $optionc = $optiond = 0;

        $correct_option    = $obj->getVar('correct');
        ${$correct_option} = 1;

        $sql2 = 'INSERT INTO ' . $this->table . '
                    (questionid, optionno, optvalue, correct)
                 VALUES (' . (int)$obj->getVar('questionid') . ", 'optiona', '" . $myts->addSlashes($obj->getVar('optiona', 'n')) . "', " . $optiona . ')';
        $sql3 = 'INSERT INTO ' . $this->table . '
                    (questionid, optionno, optvalue, correct)
                 VALUES (' . (int)$obj->getVar('questionid') . ", 'optionb', '" . $myts->addSlashes($obj->getVar('optionb', 'n')) . "', " . $optionb . ')';
        $sql4 = 'INSERT INTO ' . $this->table . '
                    (questionid, optionno, optvalue, correct)
                 VALUES (' . (int)$obj->getVar('questionid') . ", 'optionc', '" . $myts->addSlashes($obj->getVar('optionc', 'n')) . "', " . $optionc . ')';
        $sql5 = 'INSERT INTO ' . $this->table . '
                    (questionid, optionno, optvalue, correct)
                 VALUES (' . (int)$obj->getVar('questionid') . ", 'optiond', '" . $myts->addSlashes($obj->getVar('optiond', 'n')) . "', " . $optiond . ')';
        if (!$this->db->query($sql2)) {
            $obj->setErrors('Option A Insertion failed');
        }
        if (!$this->db->query($sql3)) {
            $obj->setErrors('Option B Insertion failed');
        }
        if (!$this->db->query($sql4)) {
            $obj->setErrors('Option C Insertion failed');
        }
        if (!$this->db->query($sql5)) {
            $obj->setErrors('Option D Insertion failed');
        }
        if (count($obj->getErrors()) > 0) {
            return false;
        }
        $obj->_isNew = false;

        return true;
    }

    /**
     * Update an existing Question object in the database
     *
     * @param Reference $obj to {@link Question} object to save
     * @return bool
     * @return bool
     */
    public function updateQuestion($obj)
    {
        $myts = \MyTextSanitizer::getInstance();
        $sql  = 'UPDATE ' . $this->valuetable . "
                SET value='" . $myts->addSlashes($obj->getVar('question', 'n')) . "',
                    weight=" . (int)$obj->getVar('weight') . '
                WHERE fieldid=' . (int)$obj->getVar('questionid');
        if (!$this->db->query($sql)) {
            $obj->setErrors('Update of valuetable failed');

            return false;
        }
        $optiona = $optionb = $optionc = $optiond = 0;

        $correct_option    = $obj->getVar('correct');
        ${$correct_option} = 1;
        $sql2              = 'UPDATE ' . $this->table . " SET optvalue='" . $myts->addSlashes($obj->getVar('optiona', 'n')) . "', correct=" . $optiona . '
                 WHERE questionid=' . (int)$obj->getVar('questionid') . " AND optionno='optiona'";
        $sql3              = 'UPDATE ' . $this->table . " SET optvalue='" . $myts->addSlashes($obj->getVar('optionb', 'n')) . "', correct=" . $optionb . '
                 WHERE questionid=' . (int)$obj->getVar('questionid') . " AND optionno='optionb'";
        $sql4              = 'UPDATE ' . $this->table . " SET optvalue='" . $myts->addSlashes($obj->getVar('optionc', 'n')) . "', correct=" . $optionc . '
                 WHERE questionid=' . (int)$obj->getVar('questionid') . " AND optionno='optionc'";
        $sql5              = 'UPDATE ' . $this->table . " SET optvalue='" . $myts->addSlashes($obj->getVar('optiond', 'n')) . "', correct=" . $optiond . '
                 WHERE questionid=' . (int)$obj->getVar('questionid') . " AND optionno='optiond'";
        if (!$this->db->query($sql2)) {
            $obj->setErrors('Option A Update failed');
        }
        if (!$this->db->query($sql3)) {
            $obj->setErrors('Option B Update failed');
        }
        if (!$this->db->query($sql4)) {
            $obj->setErrors('Option C Update failed');
        }
        if (!$this->db->query($sql5)) {
            $obj->setErrors('Option D Update failed');
        }
        if (count($obj->getErrors()) > 0) {
            return false;
        }

        return true;
    }

    /**
     * Delete a Question object in the database
     *
     * @param Reference $obj to {@link Question} object to delete
     * @return bool
     * @return bool
     */
    public function deleteQuestion($obj)
    {
        $sql = 'DELETE FROM ' . $this->table . '
                WHERE questionid=' . (int)$obj->getVar('questionid');
        if (!$this->db->queryF($sql)) {
            return false;
        }
        $sql = 'DELETE FROM ' . $this->valuetable . '
                WHERE fieldid=' . (int)$obj->getVar('questionid');
        if (!$this->db->queryF($sql)) {
            return false;
        }

        return true;
    }

    /**
     * Delete all Question records in the database based on criteria
     *
     * @param Reference $criteria to {@link Criteria} object describing which questions to delete
     * @return mixed
     * @return mixed
     */
    public function deleteAll($criteria)
    {
        $sql = 'DELETE FROM ' . $this->table;
        if (isset($criteria) && $criteria instanceof \CriteriaElement) {
            $sql .= ' ' . $criteria->renderWhere();
        }

        return $this->db->query($sql);
    }
}


