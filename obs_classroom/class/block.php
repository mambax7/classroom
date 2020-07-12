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
 * Classes for managing ClassroomBlocks
 *
 *
 * @author Jan Keller Pedersen
 * @package modules
 */
 
/**
 * ClassroomBlock class
 *
 * @package modules
 * @subpackage ClassroomBlock
 */
class ClassroomBlock extends XoopsObject {
    /**
    * Database object
    */
    var $db;
    
    /**
    * Database table used for storing searchable values
    */
    var $table;
    
    /**
    * Constructor sets up {@link ClassroomBlock} object
    */
    function ClassroomBlock() {
        $this->db =& XoopsDatabaseFactory::getDatabaseConnection();
        $this->table = $this->db->prefix('cr_value');
        
        $this->initVar('blockid', XOBJ_DTYPE_INT);
        $this->initVar('classroomid', XOBJ_DTYPE_INT);
        $this->initVar('name', XOBJ_DTYPE_TXTBOX);
        $this->initVar('blocktypeid', XOBJ_DTYPE_INT);

		$this->initVar('template', XOBJ_DTYPE_TXTBOX);
        $this->initVar('blocktypename', XOBJ_DTYPE_TXTBOX);
        $this->initVar('bcachetime', XOBJ_DTYPE_INT);
    }
    
    /**
    * Builds a ClassroomBlock of this type for display
    *
    * @abstract
    */
    function buildBlock() {

    }

    /**
    * Builds a form for adding or editing a ClassroomBlock
    *
    * @abstract
    */
    function buildForm() {

    }

    /**
    * Updates the specific block and its cache
    *
    */
    function update() {
        if (!$this->updateBlock()) {
            return false;
        }
        $this->updateCache();
        return true;
    }
    
    /**
    * Updates the specific block
    * 
    * @abstract
    */
    function updateBlock() {
    }
    
    /**
    * Updates the block's cache
    */
    function updateCache() {
        require_once XOOPS_ROOT_PATH."/class/template.php";
        $xoopsTpl = new XoopsTpl();
        $xoopsTpl->clear_cache("db:".$this->getVar('template'), "blk_".$this->getVar('blockid'));
    }
    
    /**
    * Deletes an item of the object (not always applicable)
    *
    * @abstract
    */
    function deleteItem() {
        return true;
    }
    
    /**
    * Deletes block-specific database values (Not applicable for blocks without database interaction or blocks that only use cr_value table)
    *
    * @abstract
    */
    function delete() {
        return true;
    }
}

/**
 * ClassroomBlock handler class
 *
 * @package modules
 * @subpackage ClassroomBlock
 */
class Obs_classroomBlockHandler extends XoopsObjectHandler {
    /**
     * An instance of database manipulation (INSERT, UPDATE, DELETE) queries
     * @access private
     * @var {@link ClassroomBlockDML} object
     */
    var $dml;
    
    /**
     * Table name
     * @access private
     * @var string
     */
    var $table;
    
    /**
    * Constructor sets up {@link ClassroomBlockHandler} object
    * @param $db Reference to {@link Database} object
    */
    function Obs_classroomBlockHandler(&$db) {
        $this->db =& $db;
        $this->table = $this->db->prefix('cr_block');
        $this->dml =& new ClassroomBlockDML($db, $this->table);
    }
    
    
    /**
     * create a new ClassroomBlock object
     * 
     * @param bool $isNew flags the new object as "new"
     * @param int $blocktype id of the blocktype
     * @staticvar object $ClassroomBlock {@link ClassroomBlock} object
     *
     * @return object
     */
    function &create($isNew = true, $blocktypeid = 1)
    {
        global $xoopsModule;
        $blocktypes = $xoopsModule->getInfo('blocktypes');
        include_once XOOPS_ROOT_PATH."/modules/obs_classroom/class/blocktypeloader.php";
        $classname = ucfirst($blocktypes[$blocktypeid]['name'])."Block";
        $classroomBlock = new $classname();
        if ($isNew) {
            $classroomBlock->setNew();
        }
        return $classroomBlock;
    }
    
    /**
     * retrieve a {@link ClassroomBlock} object
     * 
     * @param int $id ID of the ClassroomBlock
     * @staticvar object reference to the {@link ClassroomBlock} object
     *
     * @return mixed
     */
    function &get($id = false) {
        if ($id == false) {
            return false;
        }
        $id = intval($id);
        if ($id > 0) {
            $sql = "SELECT * FROM ".$this->table." WHERE blockid=".$id;
            if (!$result = $this->db->query($sql)) {
                return false;
            }
            $values = $this->db->fetchArray($result);
            $ClassroomBlock =& $this->create(false, $values['blocktypeid']);
            $ClassroomBlock->assignVars($values);
            return $ClassroomBlock;
        }
        return false;
    }
    
    /**
    * Save ClassroomBlock in database
    * @param object $obj reference to the {@link ClassroomBlock} object
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
            if (!$this->dml->insertBlock($obj)) {
                return false;
            }
        }
        else {
            if (!$this->dml->updateBlock($obj)) {
                return false;
            }
        }
        return true;
    }
    
    /**
    * delete a {@link ClassroomBlock} from the database
    *
    * @param object $ClassroomBlock reference to the {@link ClassroomBlock} to delete
    *
    * @return bool
    */
    function delete(&$block) {
        if ($block->delete()) {            
            return $this->dml->deleteBlock($block);
        }
        return false;
    }
    
    /**
    * get {@link ClassroomBlock} objects from criteria
    *
    * @param object $criteria reference to a {@link Criteria} or {@link CriteriaCompo} object
    * @param bool $as_objects if true, the returned array will be {@link ClassroomBlock} objects
    * @param bool $id_as_key if true, the returned array will have the ClassroomBlock ids as key
    *
    * @staticvar array $ret array of {@link ClassroomBlock} objects
    *
    * @return array
    */
    function &getObjects($criteria = null, $as_objects = true, $id_as_key = false) {
        $ret = array();
        $limit = $start = 0;
        $sql = 'SELECT * FROM '.$this->table.' b';
        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $sql .= ' '.$criteria->renderWhere();
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
                $ClassroomBlock =& $this->create(false, $myrow['blocktypeid']);
                $ClassroomBlock->assignVars($myrow);
                if (!$id_as_key) {
                    $ret[] =& $ClassroomBlock;
                } else {
                    $ret[$myrow['blockid']] =& $ClassroomBlock;
                }
                unset($ClassroomBlock);
            }
            else {
                if (!$id_as_key) {
                    $ret[] = $myrow;
                }
                else {
                    $ret[$myrow['blockid']] = $myrow;
                }
            }
        }
        return $ret;
    }
    
     /**
    * update or insert a {@link ClassroomClassroom} following form submissal
    *
    * @return string
    */    
    function updateInsert() {
        if (isset($_POST['blockid']) && $_POST['blockid'] > 0) {
            $obj =& $this->create(false);
            $obj->assignVar('blockid', $_POST['blockid']);
        }
        else {
            $obj =& $this->create();
        }
        $obj->setVar('name', $_POST['name']);
        $obj->setVar('blocktypeid', $_POST['blocktypeid']);
        $obj->setVar('classroomid', $_POST['classroomid']);

        if ($this->insert($obj)) {
            $obj->updateCache();
            return $obj;
        }
        return false;
    }
    
    /**
    * List blocks in a classroom
    *
    * @param int $classroomid ID of classroom
    *
    * @return string
    */
    function listBlocks($classroomid) {
        $criteria = new Criteria('classroomid', intval($classroomid));
        $criteria->setSort('name');
        $blocks = $this->getObjects($criteria);
        $return = "<table>";
        if (count($blocks) > 0) {
            foreach ($blocks as $key => $block) {
                $class = isset($class) && $class == "odd" ? "even" : "odd";
                $return .= "<tr class='".$class."'>
                            <td>".$block->getVar('blockid')."</td>
                            <td><a href='manage.php?op=editblock&amp;blockid=".$block->getVar('blockid')."'>".$block->getVar('name')."</a></td>
                            <td>".$block->getVar('blocktypename')."</td>
                            <td><form action='manage.php' method='POST'>
                                <input type='hidden' name='op' value='classblock'>
                                <input type='hidden' name='b' value='".$block->getVar('blockid')."'>
                                <input type='hidden' name='cr' value='".$block->getVar('classroomid')."'>
                                <input type='submit' name='edit' id='edit' value='"._CR_MA_EDITPOSITIONS."'></form></td>
                            <td><form action='manage.php' method='POST'>
                                <input type='hidden' name='op' value='block'>
                                <input type='hidden' name='b' value='".$block->getVar('blockid')."'>
                                <input type='hidden' name='cr' value='".$block->getVar('classroomid')."'>
                                <input type='submit' name='delete' id='delete' value='"._CR_MA_DELETE."'></form></td>
                            </tr>";
            }
        }
        $return .= "</table>";
        return $return;
    }
}
/**
 * ClassroomBlock database manipulation class
 *
 * @package modules
 * @subpackage ClassroomBlock
 */
class ClassroomBlockDML {
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
    * Constructor sets up {@link ClassroomBlockDML} object
    * @param $db Reference to {@link Database} object
    */
    function ClassroomBlockDML(&$db, $table) {
        $this->db =& $db;
        $this->table = $table;
    }
    
    /**
    * Insert a ClassroomBlock object into the database
    *
    * @param $obj Reference to {@link ClassroomBlock} object to save
    */
    function insertBlock(&$obj) {
        $myts =& MyTextSanitizer::getInstance();
        $sql = "INSERT INTO ".$this->table."
                    (classroomid, name, blocktypeid)
                VALUES (".intval($obj->getVar('classroomid')).",
                       '".$myts->addSlashes($obj->getVar('name', 'n'))."',
                       ".intval($obj->getVar('blocktypeid'))."
                       )";
        if (!$this->db->query($sql)) {
            return false;
        }
        $obj->setVar('blockid', $this->db->getInsertId());
        $obj->_isNew = false;
        return true;
    }
    
    /**
    * Update an existing ClassroomBlock object in the database
    *
    * @param $obj Reference to {@link ClassroomBlock} object to save
    */
    function updateBlock(&$obj) {
        $myts =& MyTextSanitizer::getInstance();
        $sql = "UPDATE ".$this->table."
                SET classroomid=".intval($obj->getVar('classroomid')).",
                    name='".$myts->addSlashes($obj->getVar('name', 'n'))."',
                    blocktypeid=".intval($obj->getVar('blocktypeid'))."
                WHERE blockid=".intval($obj->getVar('blockid'));
        if (!$this->db->query($sql)) {
            return false;
        }
        return true;
    }
    
    /**
    * Delete a ClassroomBlock object in the database
    *
    * @param $obj Reference to {@link ClassroomBlock} object to delete
    */
    function deleteBlock(&$obj) {
        $sql = "DELETE FROM ".$this->db->prefix('cr_value')." WHERE blockid=".intval($obj->getVar('blockid'));
        if (!$this->db->query($sql)) {
            return false;
        }
        $sql = "DELETE FROM ".$this->table."
                WHERE blockid=".intval($obj->getVar('blockid'));
        if (!$this->db->query($sql)) {
            return false;
        }
        $sql = "DELETE FROM ".$this->db->prefix('cr_classblock')."
                WHERE blockid=".intval($obj->getVar('blockid'));
        if (!$this->db->query($sql)) {
            return false;
        }
        return true;
    }
}
?>
