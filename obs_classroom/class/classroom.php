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
 * Classroom class
 *
 * @package modules
 * @subpackage classroom
 */
class ClassroomClassroom extends XoopsObject {
    /**
    * Constructor sets up {@link ClassroomClassroom} object
    */
    function ClassroomClassroom() {
        $this->initVar('classroomid', XOBJ_DTYPE_INT);
        $this->initVar('divisionid', XOBJ_DTYPE_INT);
        $this->initVar('name', XOBJ_DTYPE_TXTBOX);
        $this->initVar('owner', XOBJ_DTYPE_INT);
        $this->initVar('location', XOBJ_DTYPE_TXTBOX);
        $this->initVar('description', XOBJ_DTYPE_TXTAREA);
        $this->initVar('weight', XOBJ_DTYPE_INT);
        $this->initVar('ownername', XOBJ_DTYPE_TXTBOX);
    }
    
    /**
    * Updates cached template for classroom pages
    */
    function updateCache() {
        include_once XOOPS_ROOT_PATH . '/class/template.php';
        global $xoopsModule;
        $xoopsTpl = new XoopsTpl();
        $xoopsTpl->clear_cache('db:cr_classroom.html', 'mod_'.$xoopsModule->getVar('dirname').'|'.md5('/modules/obs_classroom/classroom.php?cr='.$this->getVar('classroomid')));
    }
    
    /**
    * Retrieves all classes for a classroom
    *
    * @return array
    */
    function getClasses() {
        $class_handler =& xoops_getmodulehandler('class', 'obs_classroom');
        return $class_handler->getObjects(null, true, true);
    }
}

/**
 * Classroom handler class
 *
 * @package modules
 * @subpackage classroom
 */
class Obs_classroomClassroomHandler extends XoopsObjectHandler {
    /**
     * An instance of database manipulation (INSERT, UPDATE, DELETE) queries
     * @access private
     * @var {@link ClassroomDML} object
     */
    var $dml;
    
    /**
     * Table name
     * @access private
     * @var string
     */
    var $table;
    
    /**
    * Constructor sets up {@link ClassroomClassroomHandler} object
    * @param $db Reference to {@link Database} object
    */
    function Obs_classroomClassroomHandler(&$db) {
        $this->db =& $db;
        $this->table = $this->db->prefix('cr_classroom');
        $this->dml =& new ClassroomDML($db, $this->table);
    }
    
    
    /**
     * create a new Classroom object
     * 
     * @param bool $isNew flags the new object as "new"
     * @staticvar object $classroom {@link ClassroomClassroom} object
     *
     * @return object
     */
    function &create($isNew = true)
    {
        $classroom = new ClassroomClassroom();
        if ($isNew) {
            $classroom->setNew();
        }
        return $classroom;
    }
    
    /**
     * retrieve a {@link ClassroomClassroom} object
     * 
     * @param int $id ID of the classroom
     * @staticvar object reference to the {@link ClassroomClassroom} object
     *
     * @return mixed
     */
    function &get($id = false) {
        if ($id == false) {
            return false;
        }
        $id = intval($id);
        if ($id > 0) {
            $criteria = new Criteria('classroomid', $id);
            $classroom =& $this->getObjects($criteria, true, false);
            return $classroom[0];
        }
        return false;
    }
    
    /**
    * Save classroom in database
    * @param object $obj reference to the {@link ClassroomClassroom} object
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
            if (!$this->dml->insertClassroom($obj)) {
                return false;
            }
        }
        else {
            if (!$this->dml->updateClassroom($obj)) {
                return false;
            }
        }
        return true;
    }
    
    /**
    * delete a {@link ClassroomClassroom} from the database
    *
    * @param object $classroom reference to the {@link ClassroomClassroom} to delete
    *
    * @return bool
    */
    function delete(&$classroom) {
        return $this->dml->deleteClassroom($classroom);
    }
    
    /**
    * get {@link ClassroomClassroom} objects from criteria
    *
    * @param object $criteria reference to a {@link Criteria} or {@link CriteriaCompo} object
    * @param bool $as_objects if true, the returned array will be {@link ClassroomClassroom} objects
    * @param bool $id_as_key if true, the returned array will have the classroom ids as key
    *
    * @staticvar array $ret array of {@link ClassroomClassroom} objects
    *
    * @return array
    */
    function &getObjects($criteria = null, $as_objects = true, $id_as_key = false) {
        $ret = array();
        $limit = $start = 0;
        $sql = 'SELECT c.*, u.name AS ownername FROM '.$this->table.' c, '.$this->db->prefix('users').' u WHERE u.uid=c.owner';
        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $sql .= ' AND '.$criteria->render();
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
                $classroom =& $this->create(false);
                $classroom->assignVars($myrow);
                if (!$id_as_key) {
                    $ret[] =& $classroom;
                } else {
                    $ret[$myrow['classroomid']] =& $classroom;
                }
                unset($classroom);
            }
            else {
                if (!$id_as_key) {
                    $ret[] = $myrow;
                }
                else {
                    $ret[$myrow['classroomid']] = $myrow;
                }
            }
        }
        return $ret;
    }
    
    /**
    * Get count of subitems (Classes and Blocks)
    *
    * @param int $classroomid ID of Classroom if only one Classroom is required
    *
    * @return array
    */
    function getSubitemCount($classroomid = false) {
        $ret = array();
        $sql = "SELECT count(c.classid) AS classes
                FROM ".$this->db->prefix("cr_class")." c
                WHERE c.classroomid = ".intval($classroomid);
        $result = $this->db->query($sql);
        $classes = $this->db->fetchArray($result);
        $ret['classes'] = $classes['classes'];
        $sql = "SELECT count(b.blockid) AS blocks
                FROM ".$this->db->prefix("cr_block")." b
                WHERE b.classroomid = ".intval($classroomid);
        $result = $this->db->query($sql);
        $blocks = $this->db->fetchArray($result);
        $ret['blocks'] = $blocks['blocks'];
        return $ret;
    }
    
    /**
    * update or insert a {@link ClassroomClassroom} following form submissal
    *
    * @return string
    */    
    function updateInsert() {
        if (!isset($_POST['name']) || $_POST['name'] == "") {
            return false;
        }
        if (!isset($_POST['owner']) || !$_POST['owner']) {
            return false;
        }
        if (isset($_POST['classroomid']) && $_POST['classroomid'] > 0) {
            $obj =& $this->create(false);
            $obj->assignVar('classroomid', $_POST['classroomid']);
        }
        else {
            $obj =& $this->create();
        }
        $obj->setVar('name', $_POST['name']);
        $obj->setVar('divisionid', $_POST['divisionid']);
        $obj->setVar('owner', $_POST['owner']);
        $obj->setVar('location', $_POST['location']);
        $obj->setVar('description', $_POST['description']);
        $obj->setVar('weight', $_POST['weight']);
        if (!$this->insert($obj)) {
            return false;
        }
        $obj->updateCache();
        return $this->updatePermissions($obj);
    }
    
    /**
    * Updates classroom permissions
    *
    * @param object $classroom {@link ClassroomClassroom} to update permissions for
    *
    * @return int ID of the division object
    */
    function updatePermissions(&$obj) {
        global $xoopsModule;
        if ($obj->getVar('classroomid') > 0) {
            $del_criteria = new CriteriaCompo(new Criteria('gperm_modid', $xoopsModule->getVar('mid')));
            $del_criteria->add(new Criteria('gperm_name', 'classroom'));
            $del_criteria->add(new Criteria('gperm_itemid', $obj->getVar('classroomid')));
            $gperm_handler =& xoops_gethandler('groupperm');
            $gperm_handler->deleteAll($del_criteria);
        }
        foreach ($_POST['moderators'] as $groupid) {
            $gperm_handler->addRight('classroom', $obj->getVar('classroomid'), $groupid, $xoopsModule->getVar('mid'));
        }
        return $obj->getVar('classroomid');
    }
}
/**
 * Classroom database manipulation class
 *
 * @package modules
 * @subpackage classroom
 */
class ClassroomDML {
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
    * Constructor sets up {@link ClassroomDML} object
    * @param $db Reference to {@link Database} object
    */
    function ClassroomDML(&$db, $table) {
        $this->db =& $db;
        $this->table = $table;
    }
    
    /**
    * Insert a Classroom object into the database
    *
    * @param $obj Reference to {@link ClassroomClassroom} object to save
    */
    function insertClassroom(&$obj) {
        $myts =& MyTextSanitizer::getInstance();
        $sql = "INSERT INTO ".$this->table."
                    (divisionid, name, owner, location, description, weight)
                VALUES (".intval($obj->getVar('divisionid')).", '".$myts->addSlashes($obj->getVar('name', 'n'))."', ".intval($obj->getVar('owner')).", '".$myts->addSlashes($obj->getVar('location', 'n'))."', '".$myts->addSlashes($obj->getVar('description', 'n'))."', ".intval($obj->getVar('weight')).")";
        if (!$this->db->query($sql)) {
            return false;
        }
        $obj->setVar('classroomid', $this->db->getInsertId());
        $obj->_isNew = false;
        return true;
    }
    
    /**
    * Update an existing Classroom object in the database
    *
    * @param $obj Reference to {@link ClassroomClassroom} object to save
    */
    function updateClassroom(&$obj) {
        $myts =& MyTextSanitizer::getInstance();
        $sql = "UPDATE ".$this->table."
                SET name='".$myts->addSlashes($obj->getVar('name', 'n'))."',
                    divisionid=".intval($obj->getVar('divisionid')).",
                    owner=".intval($obj->getVar('owner')).",
                    location='".$myts->addSlashes($obj->getVar('location', 'n'))."',
                    description='".$myts->addSlashes($obj->getVar('description', 'n'))."',
                    weight=".intval($obj->getVar('weight'))."
                WHERE classroomid=".intval($obj->getVar('classroomid'));
        if (!$this->db->query($sql)) {
            return false;
        }
        return true;
    }
    
    /**
    * Delete a Classroom object in the database
    *
    * @param $obj Reference to {@link ClassroomClassroom} object to delete
    */
    function deleteClassroom(&$obj) {
        $sql = "DELETE FROM ".$this->table."
                WHERE classroomid=".intval($obj->getVar('classroomid'));
        if (!$this->db->query($sql)) {
            return false;
        }
        return true;
    }
}
?>