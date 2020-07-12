<?
//  ------------------------------------------------------------------------ //
//                XOOPS - PHP Content Management System                      //
//                    Copyright (c) 2000 XOOPS.org                           //
//                       <http://www.xoops.org/>                             //
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
 * @author Jan Keller Pedersen
 * @package modules
 */
 
/**
 * ClassroomQuestion class
 *
 * @package modules
 * @subpackage Classroom
 */
class ClassroomQuestion extends XoopsObject {
    /**
    * Constructor sets up {@link ClassroomQuestion} object
    */
    function ClassroomQuestion() {
        $this->initVar('questionid', XOBJ_DTYPE_INT);
        $this->initVar('question', XOBJ_DTYPE_TXTBOX);
        $this->initVar('optiona', XOBJ_DTYPE_TXTBOX);
        $this->initVar('optionb', XOBJ_DTYPE_TXTBOX);
        $this->initVar('optionc', XOBJ_DTYPE_TXTBOX);
        $this->initVar('optiond', XOBJ_DTYPE_TXTBOX);
        $this->initVar('weight', XOBJ_DTYPE_INT, 0);
        $this->initVar('correct', XOBJ_DTYPE_TXTBOX);
        $this->initVar('blockid', XOBJ_DTYPE_INT);
    }
}

/**
 * ClassroomQuestion handler class
 *
 * @package modules
 * @subpackage ClassroomQuestion
 */
class Obs_classroomQuestionHandler extends XoopsObjectHandler {
    /**
     * An instance of database manipulation (INSERT, UPDATE, DELETE) queries
     * @access private
     * @var {@link ClassroomQuestionDML} object
     */
    var $dml;
    
    /**
     * Table name
     * @access private
     * @var string
     */
    var $table;
    
    /**
    * Constructor sets up {@link ClassroomQuestionHandler} object
    * @param $db Reference to {@link Database} object
    */
    function Obs_classroomQuestionHandler(&$db) {
        $this->db =& $db;
        $this->table = $this->db->prefix('cr_question');
        $this->dml =& new ClassroomQuestionDML($db, $this->table);
    }
    
    
    /**
     * create a new ClassroomQuestion object
     * 
     * @param bool $isNew flags the new object as "new"
     * @staticvar object $ClassroomQuestion {@link ClassroomQuestion} object
     *
     * @return object
     */
    function &create($isNew = true)
    {
        $ClassroomQuestion = new ClassroomQuestion();
        if ($isNew) {
            $ClassroomQuestion->setNew();
        }
        return $ClassroomQuestion;
    }
    
    /**
     * retrieve a {@link ClassroomQuestion} object
     * 
     * @param int $id ID of the ClassroomQuestion
     * @staticvar object reference to the {@link ClassroomQuestion} object
     *
     * @return mixed
     */
    function &get($id = false) {
        if ($id == false) {
            return false;
        }
        $id = intval($id);
        if ($id > 0) {
            $criteria = new Criteria('questionid', $id);
            $classroomQuestion = $this->getObjects($criteria, true);
            $keys = array_keys($classroomQuestion);
            return $classroomQuestion[$keys[0]];
        }
        return false;
    }
    
    /**
    * Save ClassroomQuestion in database
    * @param object $obj reference to the {@link ClassroomQuestion} object
    * @return bool
    */
    function insert(&$obj) {
        if (!$obj->isDirty()) {
            return true;
        }
        if (!$obj->cleanVars()) {
            return false;
        }
        if ($obj->_isNew) {
            if (!$this->dml->insertQuestion(&$obj)) {
                return false;
            }
        }
        else {
            if (!$this->dml->updateQuestion(&$obj)) {
                return false;
            }
        }
        return true;
    }
    
    /**
    * delete a {@link ClassroomQuestion} from the database
    *
    * @param object $ClassroomQuestion reference to the {@link ClassroomQuestion} to delete
    *
    * @return bool
    */
    function delete(&$ClassroomQuestion) {
        return $this->dml->deleteQuestion($ClassroomQuestion);
    }
    
    /**
    * delete aall {@link ClassroomQuestion} from the database based on criteria
    *
    * @param object $criteria reference to the {@link Criteria} object, describing which questions to delete
    *
    * @return bool
    */
    function deleteAll(&$criteria) {
        return $this->dml->deleteAll($criteria);
    }
    
    /**
    * get {@link ClassroomQuestion} objects from criteria
    *
    * @param object $criteria reference to a {@link Criteria} or {@link CriteriaCompo} object
    * @param bool $as_objects if true, the returned array will be {@link ClassroomQuestion} objects
    *
    * @staticvar array $ret array of {@link ClassroomQuestion} objects
    *
    * @return array
    */
    function &getObjects($criteria = null, $as_objects = true) {
        $ret = array();
        $limit = $start = 0;
        $sql = 'SELECT * FROM '.$this->table.' q, '.$this->db->prefix('cr_value').' v WHERE v.fieldid=q.questionid';
        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $sql .= ' AND '.$criteria->render();
            if ($criteria->getSort() != '') {
                $sql .= ' ORDER BY '.$criteria->getSort().' '.$criteria->getOrder();
            }
            $limit = $criteria->getLimit();
            $start = $criteria->getStart();
        }
        $result = $this->db->query($sql, $limit, $start);
        if (!$result) {
            return $ret;
        }
        while ($myrow = $this->db->fetchArray($result)) {
            if ($as_objects) {
                if (!isset($ret[$myrow['fieldid']])) {
                    $ret[$myrow['fieldid']] =& $this->create(false);
                    $ret[$myrow['fieldid']]->assignVar('questionid', $myrow['fieldid']);
                    $ret[$myrow['fieldid']]->assignVar('blockid', $myrow['blockid']);
                    $ret[$myrow['fieldid']]->assignVar('weight', $myrow['weight']);
                    $ret[$myrow['fieldid']]->assignVar('question', $myrow['value']);
                }
                $ret[$myrow['fieldid']]->assignVar($myrow['optionno'], $myrow['optvalue']);
                if ($myrow['correct'] == 1) {
                    $ret[$myrow['fieldid']]->assignVar('correct', $myrow['optionno']);
                }
            }
            else {
                if (!isset($ret[$myrow['fieldid']])) {
                    $ret[$myrow['fieldid']]['questionid'] = $myrow['questionid'];
                    $ret[$myrow['fieldid']]['question'] = $myrow['value'];
                    $ret[$myrow['fieldid']]['blockid'] = $myrow['blockid'];
                    $ret[$myrow['fieldid']]['weight'] = $myrow['weight'];
                }
                $ret[$myrow['fieldid']][$myrow['optionno']] = $myrow['optvalue'];
                if ($myrow['correct'] == 1) {
                    $ret[$myrow['fieldid']]['correct'] = $myrow['optionno'];
                }
            }
        }
        return $ret;
    }
    
    /**
    * update or insert a {@link ClassroomQuestion} following form submissal
    *
    * @return string
    */    
    function &updateInsert() {
        if (isset($_POST['questionid']) && $_POST['questionid'] > 0) {
            $obj =& $this->create(false);
            $obj->assignVar('questionid', $_POST['questionid']);
        }
        else {
            $obj =& $this->create();
        }
        $obj->setVar('weight', $_POST['weight']);
        $obj->setVar('question', $_POST['question']);
        $obj->setVar('optiona', $_POST['optiona']);
        $obj->setVar('optionb', $_POST['optionb']);
        $obj->setVar('optionc', $_POST['optionc']);
        $obj->setVar('optiond', $_POST['optiond']);
        $obj->setVar('correct', $_POST['correct']);
        $obj->setVar('blockid', $_POST['blockid']);
        $this->insert(&$obj);
        return $obj;
    }
}
/**
 * ClassroomQuestion database manipulation class
 *
 * @package modules
 * @subpackage ClassroomQuestion
 */
class ClassroomQuestionDML {
    /**
     * Instance of database class
     * @access private
     * @var {@link Database} object
     */
    var $db;
    
    /**
     * Table name
     * @access private
     * @var string
     */
    var $table;
    
    /**
     * Value Table name
     * @access private
     * @var string
     */
    var $valuetable;
    
    /**
    * Constructor sets up {@link ClassroomQuestionDML} object
    * @param $db Reference to {@link Database} object
    */
    function ClassroomQuestionDML(&$db, $table) {
        $this->db =& $db;
        $this->table = $table;
        $this->valuetable = $this->db->prefix('cr_value');
    }
    
    /**
    * Insert a ClassroomQuestion object into the database
    *
    * @param $obj Reference to {@link ClassroomQuestion} object to save
    */
    function insertQuestion(&$obj) {
        $myts =& MyTextSanitizer::getInstance();
        $sql = "INSERT INTO ".$this->valuetable."
                    (blockid, value, weight)
                VALUES (".intval($obj->getVar('blockid')).", '".$myts->addSlashes($obj->getVar('question', 'n'))."', ".intval($obj->getVar('weight')).")";
        if (!$this->db->query($sql)) {
            $obj->setErrors("Insert into valuetable failed");
            return false;
        }
        $obj->setVar('questionid', $this->db->getInsertId());
        
        $optiona = $optionb = $optionc = $optiond = 0;
        
        $correct_option = $obj->getVar('correct');
        ${$correct_option} = 1;
                
        $sql2 = "INSERT INTO ".$this->table." 
                    (questionid, optionno, optvalue, correct)
                 VALUES (".intval($obj->getVar('questionid')).", 'optiona', '".$myts->addSlashes($obj->getVar('optiona', 'n'))."', ".$optiona.")";
        $sql3 = "INSERT INTO ".$this->table." 
                    (questionid, optionno, optvalue, correct)
                 VALUES (".intval($obj->getVar('questionid')).", 'optionb', '".$myts->addSlashes($obj->getVar('optionb', 'n'))."', ".$optionb.")";
        $sql4 = "INSERT INTO ".$this->table." 
                    (questionid, optionno, optvalue, correct)
                 VALUES (".intval($obj->getVar('questionid')).", 'optionc', '".$myts->addSlashes($obj->getVar('optionc', 'n'))."', ".$optionc.")";
        $sql5 = "INSERT INTO ".$this->table." 
                    (questionid, optionno, optvalue, correct)
                 VALUES (".intval($obj->getVar('questionid')).", 'optiond', '".$myts->addSlashes($obj->getVar('optiond', 'n'))."', ".$optiond.")";
        if (!$this->db->query($sql2)) {
            $obj->setErrors("Option A Insertion failed");
        }
        if (!$this->db->query($sql3)) {
            $obj->setErrors("Option B Insertion failed");
        }
        if (!$this->db->query($sql4)) {
            $obj->setErrors("Option C Insertion failed");
        }
        if (!$this->db->query($sql5)) {
            $obj->setErrors("Option D Insertion failed");
        }
        if (count($obj->getErrors()) > 0) {
            return false;
        }
        $obj->_isNew = false;
        return true;
    }
    
    /**
    * Update an existing ClassroomQuestion object in the database
    *
    * @param $obj Reference to {@link ClassroomQuestion} object to save
    */
    function updateQuestion(&$obj) {
        $myts =& MyTextSanitizer::getInstance();
        $sql = "UPDATE ".$this->valuetable."
                SET value='".$myts->addSlashes($obj->getVar('question', 'n'))."',
                    weight=".intval($obj->getVar('weight'))."
                WHERE fieldid=".intval($obj->getVar('questionid'));
        if (!$this->db->query($sql)) {
            $obj->setErrors("Update of valuetable failed");
            return false;
        }
        $optiona = $optionb = $optionc = $optiond = 0;
        
        $correct_option = $obj->getVar('correct');
        ${$correct_option} = 1;
        $sql2 = "UPDATE ".$this->table." SET optvalue='".$myts->addSlashes($obj->getVar('optiona', 'n'))."', correct=".$optiona."
                 WHERE questionid=".intval($obj->getVar('questionid'))." AND optionno='optiona'";
        $sql3 = "UPDATE ".$this->table." SET optvalue='".$myts->addSlashes($obj->getVar('optionb', 'n'))."', correct=".$optionb."
                 WHERE questionid=".intval($obj->getVar('questionid'))." AND optionno='optionb'";
        $sql4 = "UPDATE ".$this->table." SET optvalue='".$myts->addSlashes($obj->getVar('optionc', 'n'))."', correct=".$optionc."
                 WHERE questionid=".intval($obj->getVar('questionid'))." AND optionno='optionc'";
        $sql5 = "UPDATE ".$this->table." SET optvalue='".$myts->addSlashes($obj->getVar('optiond', 'n'))."', correct=".$optiond."
                 WHERE questionid=".intval($obj->getVar('questionid'))." AND optionno='optiond'";
        if (!$this->db->query($sql2)) {
            $obj->setErrors("Option A Update failed");
        }
        if (!$this->db->query($sql3)) {
            $obj->setErrors("Option B Update failed");
        }
        if (!$this->db->query($sql4)) {
            $obj->setErrors("Option C Update failed");
        }
        if (!$this->db->query($sql5)) {
            $obj->setErrors("Option D Update failed");
        }
        if (count($obj->getErrors()) > 0) {
            return false;
        }
        return true;
    }
    
    /**
    * Delete a ClassroomQuestion object in the database
    *
    * @param $obj Reference to {@link ClassroomQuestion} object to delete
    */
    function deleteQuestion(&$obj) {
        $sql = "DELETE FROM ".$this->table."
                WHERE questionid=".intval($obj->getVar('questionid'));
        if (!$this->db->queryF($sql)) {
            return false;
        }
        $sql = "DELETE FROM ".$this->valuetable."
                WHERE fieldid=".intval($obj->getVar('questionid'));
        if (!$this->db->queryF($sql)) {
            return false;
        }
        return true;
    }
    
    /**
    * Delete all ClassroomQuestion records in the database based on criteria
    *
    * @param $criteria Reference to {@link Criteria} object describing which questions to delete
    */
    function deleteAll(&$criteria) {
        $sql = "DELETE FROM ".$this->table;
        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $sql .= ' '.$criteria->renderWhere();
        }
        return $this->db->query($sql);
    }
}
?>