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
 * Question handler class
 *
 * @package    modules
 * @subpackage Question
 */
class QuestionHandler extends \XoopsPersistableObjectHandler
{
    /**
     * An instance of database manipulation (INSERT, UPDATE, DELETE) queries
     * @access private
     * @var    {@link QuestionDML} object
     */
    public $dml;
    /**
     * Table name
     * @access private
     * @var string
     */
    public $table;

    /**
     * Constructor sets up {@link QuestionHandler} object
     * @param \XoopsDatabase|null $db to {@link Database} object
     */
    public function __construct(\XoopsDatabase $db = null)
    {
        $this->db    = $db;
        $this->table = $this->db->prefix('classroom_question');
        $this->dml   = new QuestionDML($db, $this->table);
    }

    /**
     * create a new Question object
     *
     * @param bool $isNew flags the new object as "new"
     * @staticvar object $Question {@link Question} object
     *
     * @return object
     */
    public function &create($isNew = true)
    {
        $Question = new Question();
        if ($isNew) {
            $Question->setNew();
        }

        return $Question;
    }

    /**
     * retrieve a {@link Question} object
     *
     * @param int|null $id ID of the Question
     * @param null     $fields
     * @return mixed
     * @staticvar object reference to the {@link Question} object
     *
     */
    public function get($id = null, $fields = null)
    {
        if (null === $id) {
            return false;
        }
        $id = (int)$id;
        if ($id > 0) {
            $criteria          = new \Criteria('questionid', $id);
            $classroomQuestion = $this->getObjects($criteria, true);
            $keys              = array_keys($classroomQuestion);

            return $classroomQuestion[$keys[0]];
        }

        return false;
    }

    /**
     * Save Question in database
     * @param \XoopsObject $obj reference to the {@link Question} object
     * @param bool         $force
     * @return bool
     */
    public function insert(\XoopsObject $obj, $force = true)
    {
        if (!$obj->isDirty()) {
            return true;
        }
        if (!$obj->cleanVars()) {
            return false;
        }
        if ($obj->_isNew) {
            if (!$this->dml->insertQuestion($obj)) {
                return false;
            }
        } else {
            if (!$this->dml->updateQuestion($obj)) {
                return false;
            }
        }

        return true;
    }

    /**
     * delete a {@link Question} from the database
     *
     * @param \XoopsObject $Question reference to the {@link Question} to delete
     * @param bool         $force
     * @return bool
     */
    public function delete(\XoopsObject $Question, $force = false)
    {
        return $this->dml->deleteQuestion($Question);
    }

    /**
     * delete aall {@link Question} from the database based on criteria
     *
     * @param \CriteriaElement|null $criteria reference to the {@link Criteria} object, describing which questions to delete
     * @param bool                  $force
     * @param bool                  $asObject
     * @return bool
     */
    public function deleteAll(\CriteriaElement $criteria = null, $force = true, $asObject = false)
    {
        return $this->dml->deleteAll($criteria);
    }

    /**
     * get {@link Question} objects from criteria
     *
     * @param \CriteriaElement|null $criteria reference to a {@link Criteria} or {@link CriteriaCompo} object
     * @param bool                  $id_as_key
     * @param bool                  $as_object
     * @return array
     * @staticvar array $ret array of {@link Question} objects
     *
     */
    public function &getObjects(\CriteriaElement $criteria = null, $id_as_key = false, $as_object = true)
        //    public function &getObjects($criteria = null, $as_objects = true)
    {
        $ret   = [];
        $start = 0;
        $limit = $start;
        $sql   = 'SELECT * FROM ' . $this->table . ' q, ' . $this->db->prefix('classroom_value') . ' v WHERE v.fieldid=q.questionid';
        if (isset($criteria) && $criteria instanceof \CriteriaElement) {
            $sql .= ' AND ' . $criteria->render();
            if ('' != $criteria->getSort()) {
                $sql .= ' ORDER BY ' . $criteria->getSort() . ' ' . $criteria->getOrder();
            }
            $limit = $criteria->getLimit();
            $start = $criteria->getStart();
        }
        $result = $this->db->query($sql, $limit, $start);
        if (!$result) {
            return $ret;
        }
        while (false !== ($myrow = $this->db->fetchArray($result))) {
            if ($as_object) {
                if (!isset($ret[$myrow['fieldid']])) {
                    $ret[$myrow['fieldid']] = $this->create(false);
                    $ret[$myrow['fieldid']]->assignVar('questionid', $myrow['fieldid']);
                    $ret[$myrow['fieldid']]->assignVar('blockid', $myrow['blockid']);
                    $ret[$myrow['fieldid']]->assignVar('weight', $myrow['weight']);
                    $ret[$myrow['fieldid']]->assignVar('question', $myrow['value']);
                }
                $ret[$myrow['fieldid']]->assignVar($myrow['optionno'], $myrow['optvalue']);
                if (1 == $myrow['correct']) {
                    $ret[$myrow['fieldid']]->assignVar('correct', $myrow['optionno']);
                }
            } else {
                if (!isset($ret[$myrow['fieldid']])) {
                    $ret[$myrow['fieldid']]['questionid'] = $myrow['questionid'];
                    $ret[$myrow['fieldid']]['question']   = $myrow['value'];
                    $ret[$myrow['fieldid']]['blockid']    = $myrow['blockid'];
                    $ret[$myrow['fieldid']]['weight']     = $myrow['weight'];
                }
                $ret[$myrow['fieldid']][$myrow['optionno']] = $myrow['optvalue'];
                if (1 == $myrow['correct']) {
                    $ret[$myrow['fieldid']]['correct'] = $myrow['optionno'];
                }
            }
        }

        return $ret;
    }

    /**
     * update or insert a {@link Question} following form submissal
     *
     * @return string
     */
    public function &updateInsert()
    {
        if (isset($_POST['questionid']) && $_POST['questionid'] > 0) {
            $obj = $this->create(false);
            $obj->assignVar('questionid', $_POST['questionid']);
        } else {
            $obj = $this->create();
        }
        $obj->setVar('weight', $_POST['weight']);
        $obj->setVar('question', $_POST['question']);
        $obj->setVar('optiona', $_POST['optiona']);
        $obj->setVar('optionb', $_POST['optionb']);
        $obj->setVar('optionc', $_POST['optionc']);
        $obj->setVar('optiond', $_POST['optiond']);
        $obj->setVar('correct', $_POST['correct']);
        $obj->setVar('blockid', $_POST['blockid']);
        $this->insert($obj);

        return $obj;
    }
}
