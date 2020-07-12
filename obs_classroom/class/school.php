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
 * Classes for managing ClassroomSchools
 *
 *
 * @author Jan Keller Pedersen
 * @package modules
 */
 
/**
 * ClassroomSchool class
 *
 * @package modules
 * @subpackage ClassroomSchool
 */
class ClassroomSchool extends XoopsObject {
    /**
    * Repository of divisions
    */
    var $divisions;
    
    /**
    * Constructor sets up {@link ClassroomSchool} object
    */
    function ClassroomSchool() {
        $this->initVar('schoolid', XOBJ_DTYPE_INT);
        $this->initVar('name', XOBJ_DTYPE_TXTBOX);
        $this->initVar('location', XOBJ_DTYPE_TXTBOX);
        $this->initVar('description', XOBJ_DTYPE_TXTAREA);
        $this->initVar('head', XOBJ_DTYPE_INT);
        $this->initVar('headname', XOBJ_DTYPE_TXTBOX);
        $this->initVar('weight', XOBJ_DTYPE_INT);
    }
    
    /**
    * Add a division object to the school object
    */
    
    function addDivision(&$division) {
        $this->divisions[] =& $division;
    }
    
    /**
    * Updates cached template for school pages
    */
    function updateCache() {
        include_once XOOPS_ROOT_PATH . '/class/template.php';
        global $xoopsModule;
        $xoopsTpl = new XoopsTpl();
        $xoopsTpl->clear_cache('db:cr_school.html', 'mod_'.$xoopsModule->getVar('dirname').'|'.md5('/modules/obs_classroom/school.php?s='.$this->getVar('schoolid')));
    }
}

/**
 * ClassroomSchool handler class
 *
 * @package modules
 * @subpackage ClassroomSchool
 */
class Obs_classroomSchoolHandler extends XoopsObjectHandler {
    /**
     * An instance of database manipulation (INSERT, UPDATE, DELETE) queries
     * @access private
     * @var {@link ClassroomSchoolDML} object
     */
    var $dml;
    
    /**
     * Table name
     * @access private
     * @var string
     */
    var $table;
    
    /**
    * Constructor sets up {@link ClassroomSchoolHandler} object
    * @param $db Reference to {@link Database} object
    */
    function Obs_classroomSchoolHandler(&$db) {
        $this->db =& $db;
        $this->table = $this->db->prefix('cr_school');
        $this->dml =& new ClassroomSchoolDML($db, $this->table);
    }
    
    
    /**
     * create a new ClassroomSchool object
     * 
     * @param bool $isNew flags the new object as "new"
     * @staticvar object $ClassroomSchool {@link ClassroomSchool} object
     *
     * @return object
     */
    function &create($isNew = true)
    {
        $ClassroomSchool = new ClassroomSchool();
        if ($isNew) {
            $ClassroomSchool->setNew();
        }
        return $ClassroomSchool;
    }
    
    /**
     * retrieve a {@link ClassroomSchool} object
     * 
     * @param int $id ID of the ClassroomSchool
     * @staticvar object reference to the {@link ClassroomSchool} object
     *
     * @return mixed
     */
    function &get($id = false) {
        if ($id == false) {
            return false;
        }
        $id = intval($id);
        if ($id > 0) {
            $criteria = new Criteria('schoolid', $id);
            $ClassroomSchool =& $this->getObjects($criteria, true, false);
            return $ClassroomSchool[0];
        }
        return false;
    }
    
    /**
    * Save ClassroomSchool in database
    * @param object $obj reference to the {@link ClassroomSchool} object
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
            if (!$this->dml->insertSchool($obj)) {
                return false;
            }
        }
        else {
            if (!$this->dml->updateSchool($obj)) {
                return false;
            }
        }
        return true;
    }
    
    /**
    * delete a {@link ClassroomSchool} from the database
    *
    * @param object $ClassroomSchool reference to the {@link ClassroomSchool} to delete
    *
    * @return bool
    */
    function delete(&$ClassroomSchool) {
        return $this->dml->deleteSchool($ClassroomSchool);
    }
    
    /**
    * get {@link ClassroomSchool} objects from criteria
    *
    * @param object $criteria reference to a {@link Criteria} or {@link CriteriaCompo} object
    * @param bool $as_objects if true, the returned array will be {@link ClassroomSchool} objects
    * @param bool $id_as_key if true, the returned array will have the ClassroomSchool ids as key
    *
    * @staticvar array $ret array of {@link ClassroomSchool} objects
    *
    * @return array
    */
    function &getObjects($criteria = null, $as_objects = true, $id_as_key = true) {
        $ret = array();
        $limit = $start = 0;
        $sql = 'SELECT s.*, u.name AS headname FROM '.$this->table.' s, '.$this->db->prefix('users').' u WHERE u.uid=s.head';
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
                $ClassroomSchool =& $this->create(false);
                $ClassroomSchool->assignVars($myrow);
                if (!$id_as_key) {
                    $ret[] =& $ClassroomSchool;
                } else {
                    $ret[$myrow['schoolid']] =& $ClassroomSchool;
                }
                unset($ClassroomSchool);
            }
            else {
                $ret[] = $myrow;
            }
        }
        return $ret;
    }
    
    /**
    * Get count of subitems (Divisions, Classrooms, Classes and Blocks)
    *
    * @param int $schoolid ID of school
    *
    * @return array
    */
    function getSubitemCount($schoolid) {
        $ret = array();
        $sql = "SELECT d.divisionid
                FROM ".$this->db->prefix("cr_division")." d
                WHERE d.schoolid =".intval($schoolid);
        
        $result = $this->db->query($sql);
        while ($row = $this->db->fetchArray($result)) {
            $divisions[$row['divisionid']] = $row['divisionid'];
        }
        $sql = "SELECT cr.classroomid
                FROM ".$this->db->prefix("cr_classroom")." cr
                WHERE cr.divisionid IN (".implode(',', array_keys($divisions)).")";
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
        $ret['divisions'] = count($divisions);
        $ret['classrooms'] = count($classrooms);
        $ret['classes'] = count($classes);
        $ret['blocks'] = count($blocks);
        return $ret;
    }
    
    /**
    * update or insert a {@link ClassroomSchool} following form submissal
    *
    * @return string
    */    
    function updateInsert() {
        if (!isset($_POST['name']) || $_POST['name'] == "") {
            return false;
        }
        if (!isset($_POST['head']) || !$_POST['head']) {
            return false;
        }
        if (isset($_POST['schoolid']) && $_POST['schoolid'] > 0) {
            $post_school =& $this->create(false);
            $post_school->assignVar('schoolid', $_POST['schoolid']);
        }
        else {
            $post_school =& $this->create();
        }
        $post_school->setVar('name', $_POST['name']);
        $post_school->setVar('location', $_POST['location']);
        $post_school->setVar('head', $_POST['head']);
        $post_school->setVar('description', $_POST['description']);
        $post_school->setVar('weight', $_POST['weight']);
        if (!$this->insert($post_school)) {
            return false;
        }
        $post_school->updateCache();
        return $this->updatePermissions($post_school);
    }
    
    /**
    * Updates school permissions
    *
    * @param object $school {@link ClassroomSchool} to update permissions for
    *
    * @return int ID of the school object
    */
    function updatePermissions(&$school) {
        global $xoopsModule;
        if ($school->getVar('schoolid') > 0) {
            $del_criteria = new CriteriaCompo(new Criteria('gperm_modid', $xoopsModule->getVar('mid')));
            $del_criteria->add(new Criteria('gperm_name', 'school'));
            $del_criteria->add(new Criteria('gperm_itemid', $school->getVar('schoolid')));
            $gperm_handler =& xoops_gethandler('groupperm');
            $gperm_handler->deleteAll($del_criteria);
        }
        foreach ($_POST['moderators'] as $groupid) {
            $gperm_handler->addRight('school', $school->getVar('schoolid'), $groupid, $xoopsModule->getVar('mid'));
        }
        return $school->getVar('schoolid');
    }
    /**
    * Updates cached template for overview pages
    */
    function updateCache() {
        include_once XOOPS_ROOT_PATH . '/class/template.php';
        global $xoopsModule;
        $xoopsTpl = new XoopsTpl();
        $xoopsTpl->clear_cache('db:cr_overview.html', 'mod_'.$xoopsModule->getVar('dirname').'|'.md5('/modules/obs_classroom/index.php'));
    }    
}
/**
 * ClassroomSchool database manipulation class
 *
 * @package modules
 * @subpackage ClassroomSchool
 */
class ClassroomSchoolDML {
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
    * Constructor sets up {@link ClassroomSchoolDML} object
    * @param $db Reference to {@link Database} object
    */
    function ClassroomSchoolDML(&$db, $table) {
        $this->db =& $db;
        $this->table = $table;
    }
    
    /**
    * Insert a ClassroomSchool object into the database
    *
    * @param $obj Reference to {@link ClassroomSchool} object to create
    */
    function insertSchool(&$obj) {
        $myts =& MyTextSanitizer::getInstance();
        $sql = "INSERT INTO ".$this->table."
                    (name, location, head, description, weight)
                VALUES ('".$myts->addSlashes($obj->getVar('name', 'n'))."', '".$myts->addSlashes($obj->getVar('location', 'n'))."', ".intval($obj->getVar('head')).", '".$myts->addSlashes($obj->getVar('description', 'n'))."', ".intval($obj->getVar('weight')).")";
        if (!$this->db->query($sql)) {
            return false;
        }
        $obj->setVar('schoolid', $this->db->getInsertId());
        $obj->_isNew = false;
        return true;
    }
    
    /**
    * Update an existing ClassroomSchool object in the database
    *
    * @param $obj Reference to {@link ClassroomSchool} object to update
    */
    function updateSchool(&$obj) {
        $myts =& MyTextSanitizer::getInstance();
        $sql = "UPDATE ".$this->table."
                SET name='".$myts->addSlashes($obj->getVar('name', 'n'))."',
                    location='".$myts->addSlashes($obj->getVar('location', 'n'))."',
                    head=".intval($obj->getVar('head')).",
                    description='".$myts->addSlashes($obj->getVar('description', 'n'))."',
                    weight=".intval($obj->getVar('weight'))."
                WHERE schoolid=".intval($obj->getVar('schoolid'));
        if (!$this->db->query($sql)) {
            return false;
        }
        return true;
    }
    
    /**
    * Delete a ClassroomSchool object in the database
    *
    * @param $obj Reference to {@link ClassroomSchool} object to delete
    */
    function deleteSchool(&$obj) {
        $sql = "DELETE FROM ".$this->table."
                WHERE schoolid=".intval($obj->getVar('schoolid'));
        if (!$this->db->query($sql)) {
            return false;
        }
        return true;
    }
}
?>