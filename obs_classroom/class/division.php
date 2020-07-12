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
 * Classes for managing ClassroomDivisions
 *
 *
 * @author Jan Keller Pedersen
 * @package modules
 */
 
/**
 * ClassroomDivision class
 *
 * @package modules
 * @subpackage ClassroomDivision
 */
class ClassroomDivision extends XoopsObject {
    /**
    * Constructor sets up {@link ClassroomDivision} object
    */
    function ClassroomDivision() {
        $this->initVar('divisionid', XOBJ_DTYPE_INT);
        $this->initVar('schoolid', XOBJ_DTYPE_INT);
        $this->initVar('name', XOBJ_DTYPE_TXTBOX);
        $this->initVar('location', XOBJ_DTYPE_TXTBOX);
        $this->initVar('director', XOBJ_DTYPE_INT);
        $this->initVar('description', XOBJ_DTYPE_TXTAREA);
        $this->initVar('weight', XOBJ_DTYPE_INT);
        $this->initVar('directorname', XOBJ_DTYPE_TXTBOX);
    }
    
    /**
    * Updates cached template for division pages
    */
    function updateCache() {
        include_once XOOPS_ROOT_PATH . '/class/template.php';
        global $xoopsModule;
        $xoopsTpl = new XoopsTpl();
        $xoopsTpl->clear_cache('db:cr_school.html', 'mod_'.$xoopsModule->getVar('dirname').'|'.md5('/modules/obs_classroom/school.php?s='.$this->getVar('schoolid')));
        $xoopsTpl->clear_cache('db:cr_division.html', 'mod_'.$xoopsModule->getVar('dirname').'|'.md5('/modules/obs_classroom/division.php?d='.$this->getVar('divisionid')));
    }
}

/**
 * ClassroomDivision handler class
 *
 * @package modules
 * @subpackage ClassroomDivision
 */
class Obs_classroomDivisionHandler extends XoopsObjectHandler {
    /**
     * An instance of database manipulation (INSERT, UPDATE, DELETE) queries
     * @access private
     * @var {@link ClassroomDivisionDML} object
     */
    var $dml;
    
    /**
     * Table name
     * @access private
     * @var string
     */
    var $table;
    
    /**
    * Constructor sets up {@link ClassroomDivisionHandler} object
    * @param $db Reference to {@link Database} object
    */
    function Obs_classroomDivisionHandler(&$db) {
        $this->db =& $db;
        $this->table = $this->db->prefix('cr_division');
        $this->dml =& new ClassroomDivisionDML($db, $this->table);
    }
    
    
    /**
     * create a new ClassroomDivision object
     * 
     * @param bool $isNew flags the new object as "new"
     * @staticvar object $ClassroomDivision {@link ClassroomDivision} object
     *
     * @return object
     */
    function &create($isNew = true)
    {
        $ClassroomDivision = new ClassroomDivision();
        if ($isNew) {
            $ClassroomDivision->setNew();
        }
        return $ClassroomDivision;
    }
    
    /**
     * retrieve a {@link ClassroomDivision} object
     * 
     * @param int $id ID of the ClassroomDivision
     * @staticvar object reference to the {@link ClassroomDivision} object
     *
     * @return mixed
     */
    function &get($id = false) {
        if ($id == false) {
            return false;
        }
        $id = intval($id);
        if ($id > 0) {
            $criteria = new Criteria('divisionid', $id);
            $classroomDivision =& $this->getObjects($criteria, true, false);
            return $classroomDivision[0];
        }
        return false;
    }
    
    /**
    * Save ClassroomDivision in database
    * @param object $obj reference to the {@link ClassroomDivision} object
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
            if (!$this->dml->insertDivision($obj)) {
                return false;
            }
        }
        else {
            if (!$this->dml->updateDivision($obj)) {
                return false;
            }
        }
        return true;
    }
    
    /**
    * delete a {@link ClassroomDivision} from the database
    *
    * @param object $ClassroomDivision reference to the {@link ClassroomDivision} to delete
    *
    * @return bool
    */
    function delete(&$ClassroomDivision) {
        return $this->dml->deleteDivision($ClassroomDivision);
    }
    
    /**
    * get {@link ClassroomDivision} objects from criteria
    *
    * @param object $criteria reference to a {@link Criteria} or {@link CriteriaCompo} object
    * @param bool $as_objects if true, the returned array will be {@link ClassroomDivision} objects
    * @param bool $id_as_key if true, the returned array will have the ClassroomDivision ids as key
    *
    * @staticvar array $ret array of {@link ClassroomDivision} objects
    *
    * @return array
    */
    function &getObjects($criteria = null, $as_objects = true, $id_as_key = true) {
        $ret = array();
        $limit = $start = 0;
        $sql = 'SELECT d.*, u.name AS directorname FROM '.$this->table.' d, '.$this->db->prefix('users').' u WHERE u.uid=d.director';
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
                $ClassroomDivision =& $this->create(false);
                $ClassroomDivision->assignVars($myrow);
                if (!$id_as_key) {
                    $ret[] =& $ClassroomDivision;
                } else {
                    $ret[$myrow['divisionid']] =& $ClassroomDivision;
                }
                unset($ClassroomDivision);
            }
            else {
                if (!$id_as_key) {
                    $ret[] = $myrow;
                }
                else {
                    $ret[$myrow['divisionid']] = $myrow;
                }
            }
        }
        return $ret;
    }
    
    /**
    * Get count of subitems (Classrooms, Classes and Blocks)
    *
    * @param int $divid ID of division if only one division is required
    * 
    * @return array
    */
    function getSubitemCount($divid = false) {
        $ret = array();
        $sql = "SELECT cr.classroomid
                FROM ".$this->db->prefix("cr_classroom")." cr
                WHERE cr.divisionid = ".intval($divid);
        $result = $this->db->query($sql);
        while ($row = $this->db->fetchArray($result)) {
            $classrooms[$row['classroomid']] = $row['classroomid'];
        }
        $sql = "SELECT c.classid
                FROM ".$this->db->prefix("cr_class")." c
                WHERE c.classroomid IN (".implode(',', array_keys($classrooms)).")";
        $result = $this->db->query($sql);
        while ($row = $this->db->fetchArray($result)) {
            $classes[$row['classid']] = $row['classid'];
        }
        $sql = "SELECT b.blockid
                FROM ".$this->db->prefix("cr_block")." b
                WHERE b.classroomid IN (".implode(',', array_keys($classrooms)).")";
        $result = $this->db->query($sql);
        while ($row = $this->db->fetchArray($result)) {
            $blocks[$row['blockid']] = $row['blockid'];
        }
        $ret['classrooms'] = count($classrooms);
        $ret['classes'] = count($classes);
        $ret['blocks'] = count($blocks);
        return $ret;
    }
    
    /**
    * update or insert a {@link ClassroomDivision} following form submissal
    *
    * @return string
    */    
    function updateInsert() {
        if (!isset($_POST['name']) || $_POST['name'] == "") {
            return false;
        }
        if (!isset($_POST['director']) || !$_POST['director']) {
            return false;
        }
        if (isset($_POST['divisionid']) && $_POST['divisionid'] > 0) {
            $obj =& $this->create(false);
            $obj->assignVar('divisionid', $_POST['divisionid']);
        }
        else {
            $obj =& $this->create();
        }
        $obj->setVar('name', $_POST['name']);
        $obj->setVar('schoolid', $_POST['schoolid']);
        $obj->setVar('description', $_POST['description']);
        $obj->setVar('location', $_POST['location']);
        $obj->setVar('director', $_POST['director']);
        $obj->setVar('weight', $_POST['weight']);
        if (!$this->insert($obj)) {
            return false;
        }
        $obj->updateCache();
        return $this->updatePermissions($obj);
    }
    
    /**
    * Updates division permissions
    *
    * @param object $division {@link ClassroomDivision} to update permissions for
    *
    * @return int ID of the division object
    */
    function updatePermissions(&$division) {
        global $xoopsModule;
        if ($division->getVar('divisionid') > 0) {
            $del_criteria = new CriteriaCompo(new Criteria('gperm_modid', $xoopsModule->getVar('mid')));
            $del_criteria->add(new Criteria('gperm_name', 'division'));
            $del_criteria->add(new Criteria('gperm_itemid', $division->getVar('divisionid')));
            $gperm_handler =& xoops_gethandler('groupperm');
            $gperm_handler->deleteAll($del_criteria);
        }
        foreach ($_POST['moderators'] as $groupid) {
            $gperm_handler->addRight('division', $division->getVar('divisionid'), $groupid, $xoopsModule->getVar('mid'));
        }
        return $division->getVar('divisionid');
    }
}
/**
 * ClassroomDivision database manipulation class
 *
 * @package modules
 * @subpackage ClassroomDivision
 */
class ClassroomDivisionDML {
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
    * Constructor sets up {@link ClassroomDivisionDML} object
    * @param $db Reference to {@link Database} object
    */
    function ClassroomDivisionDML(&$db, $table) {
        $this->db =& $db;
        $this->table = $table;
    }
    
    /**
    * Insert a ClassroomDivision object into the database
    *
    * @param $obj Reference to {@link ClassroomDivision} object to save
    */
    function insertDivision(&$obj) {
        $myts =& MyTextSanitizer::getInstance();
        $sql = "INSERT INTO ".$this->table."
                    (schoolid, name, description, director, location, weight)
                VALUES (".intval($obj->getVar('schoolid')).", '".$myts->addSlashes($obj->getVar('name', 'n'))."', '".$myts->addSlashes($obj->getVar('description', 'n'))."', ".intval($obj->getVar('director')).", '".$myts->addSlashes($obj->getVar('location', 'n'))."', ".intval($obj->getVar('weight')).")";
        if (!$this->db->query($sql)) {
            return false;
        }
        $obj->setVar('divisionid', $this->db->getInsertId());
        $obj->_isNew = false;
        return true;
    }
    
    /**
    * Update an existing ClassroomDivision object in the database
    *
    * @param $obj Reference to {@link ClassroomDivision} object to save
    */
    function updateDivision(&$obj) {
        $myts =& MyTextSanitizer::getInstance();
        $sql = "UPDATE ".$this->table."
                SET name='".$myts->addSlashes($obj->getVar('name', 'n'))."',
                    schoolid=".intval($obj->getVar('schoolid')).",
                    description='".$myts->addSlashes($obj->getVar('description', 'n'))."',
                    location='".$myts->addSlashes($obj->getVar('location', 'n'))."',
                    director=".intval($obj->getVar('director')).",
                    weight=".intval($obj->getVar('weight'))."
                WHERE divisionid=".intval($obj->getVar('divisionid'));
        if (!$this->db->query($sql)) {
            return false;
        }
        return true;
    }
    
    /**
    * Delete a ClassroomDivision object in the database
    *
    * @param $obj Reference to {@link ClassroomDivision} object to delete
    */
    function deleteDivision(&$obj) {
        $sql = "DELETE FROM ".$this->table."
                WHERE divisionid=".intval($obj->getVar('divisionid'));
        if (!$this->db->query($sql)) {
            return false;
        }
        return true;
    }
}
?>