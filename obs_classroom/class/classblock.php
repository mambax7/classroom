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
 * Classes for managing ClassroomClassblocks
 *
 *
 * @author Jan Keller Pedersen
 * @package modules
 */
 
/**
 * ClassroomClassblock class
 *
 * @package modules
 * @subpackage ClassroomClassblock
 */
class ClassroomClassblock extends XoopsObject {
    /**
    * Block object for this classblock
    */
    var $block;

    /**
    * Constructor sets up {@link ClassroomClassBlock} object
    */
    function ClassroomClassBlock() {
        $this->initVar('blockid', XOBJ_DTYPE_INT);
        $this->initVar('classid', XOBJ_DTYPE_INT);
        $this->initVar('side', XOBJ_DTYPE_INT, 0, false);
		$this->initVar('weight', XOBJ_DTYPE_INT, 0, false);
		$this->initVar('visible', XOBJ_DTYPE_INT, 0, false);
    }
    
    /**
    * Builds a ClassroomClassblock of this type for display
    *
    * @abstract
    */
    function buildBlock() {
        return $this->block->buildBlock();
    }
}

/**
 * ClassroomClassBlock handler class
 *
 * @package modules
 * @subpackage Classroom
 */
class Obs_classroomClassblockHandler extends XoopsObjectHandler {
    /**
     * An instance of database manipulation (INSERT, UPDATE, DELETE) queries
     * @access private
     * @var {@link ClassroomClassblockDML} object
     */
    var $dml;
    
    /**
     * Table name
     * @access private
     * @var string
     */
    var $table;
    
    /**
    * errors
    *
    * @var array
    * @access private
    */
    var $_errors = array();
    
    /**
    * Constructor sets up {@link ClassroomClassblockHandler} object
    * @param $db Reference to {@link Database} object
    */
    function Obs_classroomClassblockHandler(&$db) {
        $this->db =& $db;
        $this->table = $this->db->prefix('cr_classblock');
        $this->dml =& new ClassroomClassblockDML($db, $this->table);
    }
    
    
    /**
     * create a new ClassroomClassblock object
     * 
     * @param bool $isNew flags the new object as "new"
     * @param int $blocktype id of the blocktype
     * @staticvar object $ClassroomClassBlock {@link ClassroomClassblock} object
     *
     * @return object
     */
    function &create($isNew = true, $blocktypeid = 1)
    {
        $ClassroomClassBlock = new ClassroomClassblock();
        if ($isNew) {
            $ClassroomClassBlock->setNew();
        }
        return $ClassroomClassBlock;
    }
    
    /**
     * retrieve a {@link ClassroomClassblock} object
     * 
     * @param int $id ID of the ClassroomClassblock
     * @staticvar object reference to the {@link ClassroomClassblock} object
     *
     * @return mixed
     */
    function &get($classid, $blockid = false) {
        if ($blockid == false) {
            return false;
        }
        $blockid = intval($blockid);
        $classid = intval($classid);
        if ($blockid > 0) {
            $sql = "SELECT * FROM ".$this->table." WHERE blockid=".$blockid." AND classid=".$classid;
            if (!$result = $this->db->query($sql)) {
                return false;
            }
            $ClassroomClassBlock =& $this->create(false);
            $ClassroomClassBlock->assignVars($this->db->fetchArray($result));
            return $ClassroomClassBlock;
        }
        return false;
    }
    
    /**
    * Save ClassroomClassblock in database
    * @param object $obj reference to the {@link ClassroomClassblock} object
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
            if (!$this->dml->insertClassblock($obj)) {
                return false;
            }
        }
        else {
            if (!$this->dml->updateClassblock($obj)) {
                return false;
            }
        }
        return true;
    }
    
    /**
    * delete a {@link ClassroomClassblock} from the database
    *
    * @param object $ClassroomClassblock reference to the {@link ClassroomClassblock} to delete
    *
    * @return bool
    */
    function delete(&$block) {
        return $this->dml->deleteClassblock($block);
    }
    
    /**
     * add an error
     *
     * @param string $value error to add
     * @access public
     */
    function setErrors($err_str)
    {
        $this->_errors[] = trim($err_str);
    }

    /**
     * return the errors for this object as an array
     *
     * @return array an array of errors
     * @access public
     */
    function getErrors()
    {
        return $this->_errors;
    }
    
    /**
    * get {@link ClassroomClassblock} objects from criteria
    *
    * @param object $criteria reference to a {@link Criteria} or {@link CriteriaCompo} object
    * @param bool $as_objects if true, the returned array will be {@link ClassroomClassblock} objects
    *
    * @staticvar array $ret array of {@link ClassroomClassblock} objects
    *
    * @return array
    */
    function &getObjects($criteria = null, $as_objects = true) {
        $ret = array();
        $limit = $start = 0;
        $sql = 'SELECT cb.blockid, cb.classid, cb.visible, cb.side, cb.weight, b.blocktypeid, b.classroomid, b.name AS blockname
                FROM '.$this->table.' cb,
                     '.$this->db->prefix('cr_block').' b
                WHERE cb.blockid=b.blockid';
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
        $block_handler =& xoops_getmodulehandler('block', 'obs_classroom');
        while ($myrow = $this->db->fetchArray($result)) {
            if ($as_objects) {            
                $classroomClassBlock =& $this->create(false);
                $classroomClassBlock->assignVar('blockid', $myrow['blockid']);
                $classroomClassBlock->assignVar('classid', $myrow['classid']);
                $classroomClassBlock->assignVar('visible', $myrow['visible']);
                $classroomClassBlock->assignVar('side', $myrow['side']);
                $classroomClassBlock->assignVar('weight', $myrow['weight']);
                $classroomClassBlock->block = $block_handler->create(false, $myrow['blocktypeid']);
                $classroomClassBlock->block->assignVar('blockid', $myrow['blockid']);
                $classroomClassBlock->block->assignVar('classroomid', $myrow['classroomid']);
                $classroomClassBlock->block->assignVar('name', $myrow['blockname']);
                $ret[] =& $classroomClassBlock;
                unset($classroomClassBlock);
            }
            else {
                $ret[] = $myrow;
            }
        }
        return $ret;
    }
    
     /**
    * insert a range of {@link ClassroomClassblock} following form submissal
    *
    * @param object &$block {@link ClassroomBlock} object
    *
    * @return int
    */    
    function updateInsert(&$block) {
        $class_handler =& xoops_getmodulehandler('class', 'obs_classroom');
        if ($_POST['blockname'] == "") {
            $this->setErrors('Block Name not set');
            return;
        }
        if ($_POST['blockname'] != $block->getVar('name')) {
            $block_handler =& xoops_getmodulehandler('block', 'obs_classroom');
            $block->setVar('name', $_POST['blockname']);
            $block_handler->insert($block);
        }

        $criteria = new Criteria('classroomid', $_POST['classroomid']);
        $classes = $class_handler->getObjects($criteria, true, true);

        $this->dml->clearClassBlocks($_POST['b']);
        foreach ($classes as $classid => $class) {
            $obj =& $this->create();
            $obj->setVar('blockid', $_POST['b']);
            $obj->setVar('classid', $classid);
            if (isset($_POST['visible'][$classid])) {
                $obj->setVar('visible', 1);
            }
            $obj->setVar('side', $_POST['position'][$classid]);
            $obj->setVar('weight', $_POST['weight'][$classid]);
            if (!$this->insert($obj)) {
                $this->setErrors('Failed Saving '.$class->getVar('name'));
            }
        }
        return;
    }
    
    /**
    * Return an array of a block's position, weight and side for each of a classroom's classes
    *
    * @param object $block {@link ClassroomBlock} child object
    *
    * @return array
    */
    function getSettings(&$block) {
        $ret = array();
        $block_criteria = new Criteria('b.blockid', $block->getVar('blockid'));
        $classblocks = $this->getObjects($block_criteria);
        if (count($classblocks) > 0) {
            foreach ($classblocks as $key => $classblock) {
                $ret[$classblock->getVar('classid')]['visible'] = $classblock->getVar('visible');
                $ret[$classblock->getVar('classid')]['side'] = $classblock->getVar('side');
                $ret[$classblock->getVar('classid')]['weight'] = $classblock->getVar('weight');
            }
        }
        return $ret;
    }
        
}
/**
 * ClassroomClassblock database manipulation class
 *
 * @package modules
 * @subpackage Classroom
 */
class ClassroomClassblockDML {
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
    * Constructor sets up {@link ClassroomClassblockDML} object
    * @param $db Reference to {@link Database} object
    */
    function ClassroomClassblockDML(&$db, $table) {
        $this->db =& $db;
        $this->table = $table;
    }
    
    /**
    * Insert a ClassroomClassblock object into the database
    *
    * @param $obj Reference to {@link ClassroomClassblock} object to save
    */
    function insertClassblock(&$obj) {
        $sql = "INSERT INTO ".$this->table."
                    (blockid, classid, side, weight, visible)
                VALUES (".intval($obj->getVar('blockid')).",
                       ".intval($obj->getVar('classid')).",
                       '".$obj->getVar('side')."',
                       ".intval($obj->getVar('weight')).",
                       ".intval($obj->getVar('visible'))."
                       )";
        if (!$this->db->query($sql)) {
            return false;
        }
        $obj->setVar('classblockid', $this->db->getInsertId());
        $obj->_isNew = false;
        return true;
    }
    
    /**
    * Update an existing ClassroomClassblock object in the database
    *
    * @param $obj Reference to {@link ClassroomClassblock} object to save
    */
    function updateClassblock(&$obj) {
        $sql = "UPDATE ".$this->table."
                SET weight=".intval($obj->getVar('weight')).",
                    visible=".intval($obj->getVar('visible'))."
                    side=".intval($obj->getVar('side'))."
                WHERE classid=".intval($obj->getVar('classid'))." AND blockid=".intval($obj->getVar('blockid'));
        if (!$this->db->query($sql)) {
            return false;
        }
        return true;
    }
    
    /**
    * Delete a ClassroomClassblock object in the database
    *
    * @param $obj Reference to {@link ClassroomClassblock} object to delete
    */
    function deleteClassblock(&$obj) {
        $sql = "DELETE FROM ".$this->table."
                WHERE classid=".intval($obj->getVar('classid'))." AND blockid=".intval($obj->getVar('blockid'));
        if (!$this->db->query($sql)) {
            return false;
        }
        return true;
    }
    
    /**
    * Clears a block's settings in a classroom
    */
    function clearClassBlocks($id) {
        $id = intval($id);
        $sql = "DELETE FROM ".$this->table."
               WHERE blockid=".$id;
        return $this->db->query($sql);
    }
}
?>
