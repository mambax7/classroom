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
 * Classes for managing classrooms
 *
 *
 * @author Jan Keller Pedersen
 * @package modules
 */
 
/**
 * ClassroomClass class
 *
 * @package modules
 * @subpackage classroom
 */
class ClassroomClass extends XoopsObject {
    /**
    * Constructor sets up {@link ClassroomClass} object
    */
    function ClassroomClass() {
        $this->initVar('classid', XOBJ_DTYPE_INT);
        $this->initVar('classroomid', XOBJ_DTYPE_INT);
        $this->initVar('name', XOBJ_DTYPE_TXTBOX);
        $this->initVar('time', XOBJ_DTYPE_TXTBOX);
        $this->initVar('description', XOBJ_DTYPE_TXTAREA);
        $this->initVar('weight', XOBJ_DTYPE_INT);
    }
}

/**
 * Class handler class
 *
 * @package modules
 * @subpackage classroom
 */
class Obs_classroomClassHandler extends XoopsObjectHandler {
    /**
     * An instance of database manipulation (INSERT, UPDATE, DELETE) queries
     * @access private
     * @var {@link ClassDML} object
     */
    var $dml;
    
    /**
     * Table name
     * @access private
     * @var string
     */
    var $table;
    
    /**
    * Constructor sets up {@link ClassroomHandler} object
    * @param $db Reference to {@link Database} object
    */
    function Obs_classroomClassHandler(&$db) {
        $this->db =& $db;
        $this->table = $this->db->prefix('cr_class');
        $this->dml =& new ClassDML($db, $this->table);
    }
    
    
    /**
     * create a new Class object
     * 
     * @param bool $isNew flags the new object as "new"
     * @staticvar object $classroom {@link ClassroomClass} object
     *
     * @return object
     */
    function &create($isNew = true)
    {
        $class = new ClassroomClass();
        if ($isNew) {
            $class->setNew();
        }
        return $class;
    }
    
    /**
     * retrieve a {@link ClassroomClass} object
     * 
     * @param int $id ID of the class
     * @staticvar object reference to the {@link ClassroomClass} object
     *
     * @return mixed
     */
    function &get($id = false) {
        if ($id == false) {
            return false;
        }
        $id = intval($id);
        if ($id > 0) {
            $sql = "SELECT * FROM ".$this->table." WHERE classid=".$id;
            if (!$result = $this->db->query($sql)) {
                return false;
            }
            $classroom =& $this->create(false);
            $classroom->assignVars($this->db->fetchArray($result));
            return $classroom;
        }
        return false;
    }
    
    /**
    * Save class in database
    * @param object $obj reference to the {@link ClassroomClass} object
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
            if (!$this->dml->insertClass($obj)) {
                return false;
            }
        }
        else {
            if (!$this->dml->updateClass($obj)) {
                return false;
            }
        }
        return true;
    }
    
    /**
    * delete a {@link ClassroomClass} from the database
    *
    * @param object $classroom reference to the {@link ClassroomClass} to delete
    *
    * @return bool
    */
    function delete(&$obj) {
        return $this->dml->deleteClass($obj);
    }
    
    /**
    * get {@link ClassroomClass} objects from criteria
    *
    * @param object $criteria reference to a {@link Criteria} or {@link CriteriaCompo} object
    * @param bool $as_objects if true, the returned array will be {@link ClassroomClass} objects
    * @param bool $id_as_key if true, the returned array will have the class ids as key
    *
    * @staticvar array $ret array of {@link ClassroomClass} objects
    *
    * @return array
    */
    function &getObjects($criteria = null, $as_objects = true, $id_as_key = false) {
        $ret = array();
        $limit = $start = 0;
        $sql = 'SELECT * FROM '.$this->table;
        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $sql .= ' '.$criteria->renderWhere();
            if ($criteria->getSort() != '') {
                $sql .= ' ORDER BY '.$criteria->getSort().' '.$criteria->getOrder();
            }
            else {
                $sql .= ' ORDER BY weight ASC';
            }
            $limit = $criteria->getLimit();
            $start = $criteria->getStart();
        }
        else {
            $sql .= ' ORDER BY weight ASC';
        }
        $result = $this->db->query($sql, $limit, $start);
        if (!$result) {
            return $ret;
        }
        while ($myrow = $this->db->fetchArray($result)) {
            if ($as_objects) {            
                $class =& $this->create(false);
                $class->assignVars($myrow);
                if (!$id_as_key) {
                    $ret[] =& $class;
                } else {
                    $ret[$myrow['classid']] =& $class;
                }
                unset($class);
            }
            else {
                if (!$id_as_key) {
                    $ret[] = $myrow;
                }
                else {
                    $ret[$myrow['classid']] = $myrow;
                }
            }
        }
        return $ret;
    }
    
    /**
    * update or insert a {@link ClassroomClass} following form submissal
    *
    * @return string
    */    
    function updateInsert() {
        if (!isset($_POST['name']) || $_POST['name'] == "") {
            return false;
        }
        if (isset($_POST['classid']) && $_POST['classid'] > 0) {
            $obj =& $this->create(false);
            $obj->assignVar('classid', $_POST['classid']);
        }
        else {
            $obj =& $this->create();
        }
        $obj->setVar('name', $_POST['name']);
        $obj->setVar('classroomid', $_POST['classroomid']);
        $obj->setVar('time', $_POST['time']);
        $obj->setVar('description', $_POST['description']);
        $obj->setVar('weight', $_POST['weight']);
        if (!$this->insert($obj)) {
            return false;
        }
        return $obj;
    }
}
/**
 * Class database manipulation class
 *
 * @package modules
 * @subpackage classroom
 */
class ClassDML {
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
    * Constructor sets up {@link ClassDML} object
    * @param $db Reference to {@link Database} object
    */
    function ClassDML(&$db, $table) {
        $this->db =& $db;
        $this->table = $table;
    }
    
    /**
    * Insert a Class object into the database
    *
    * @param $obj Reference to {@link ClassroomClass} object to save
    */
    function insertClass(&$obj) {
        $myts =& MyTextSanitizer::getInstance();
        $sql = "INSERT INTO ".$this->table."
                    (classroomid, name, time, description, weight)
                VALUES (".intval($obj->getVar('classroomid')).", '".$myts->addSlashes($obj->getVar('name', 'n'))."', '".$myts->addSlashes($obj->getVar('time', 'n'))."', '".$myts->addSlashes($obj->getVar('description', 'n'))."', ".intval($obj->getVar('weight')).")";
        if (!$this->db->query($sql)) {
            return false;
        }
        $obj->setVar('classid', $this->db->getInsertId());
        $obj->_isNew = false;
        return true;
    }
    
    /**
    * Update an existing Class object in the database
    *
    * @param $obj Reference to {@link ClassroomClass} object to save
    */
    function updateClass(&$obj) {
        $myts =& MyTextSanitizer::getInstance();
        $sql = "UPDATE ".$this->table."
                SET name='".$myts->addSlashes($obj->getVar('name', 'n'))."',
                    classroomid=".intval($obj->getVar('classroomid')).",
                    time='".$myts->addSlashes($obj->getVar('time', 'n'))."',
                    description='".$myts->addSlashes($obj->getVar('description', 'n'))."', 
                    weight=".intval($obj->getVar('weight'))."
                WHERE classid=".intval($obj->getVar('classid'));
        if (!$this->db->query($sql)) {
            return false;
        }
        return true;
    }
    
    /**
    * Delete a Class object in the database
    *
    * @param $obj Reference to {@link ClassroomClass} object to delete
    */
    function deleteClass(&$obj) {
        $sql = "DELETE FROM ".$this->table."
                WHERE classid=".intval($obj->getVar('classid'));
        if (!$this->db->query($sql)) {
            return false;
        }
        return true;
    }
}
?>
