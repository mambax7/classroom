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
 * Classes for managing Classblocks
 *
 *
 * @author  Jan Keller Pedersen
 * @package modules
 */

/**
 * ClassroomClassBlock handler class
 *
 * @package    modules
 * @subpackage Classroom
 */
class ClassblockHandler extends \XoopsPersistableObjectHandler
{
    /**
     * An instance of database manipulation (INSERT, UPDATE, DELETE) queries
     * @access private
     * @var    {@link ClassblockDML} object
     */
    public $dml;
    /**
     * Table name
     * @access private
     * @var string
     */
    public $table;
    /**
     * errors
     *
     * @var array
     * @access private
     */
    public $_errors = [];

    /**
     * Constructor sets up {@link ClassblockHandler} object
     * @param \XoopsDatabase|null $db to {@link Database} object
     */
    public function __construct(\XoopsDatabase $db = null)
    {
        $this->db    = $db;
        $this->table = $this->db->prefix('classroom_classblock');
        $this->dml   = new ClassblockDML($db, $this->table);
    }

    /**
     * create a new Classblock object
     *
     * @param bool $isNew flags the new object as "new"
     * @param int  $blocktypeid
     * @return object
     * @staticvar object $ClassroomClassBlock {@link Classblock} object
     *
     */
    public function &create($isNew = true, $blocktypeid = 1)
    {
        $ClassroomClassBlock = new Classblock();
        if ($isNew) {
            $ClassroomClassBlock->setNew();
        }

        return $ClassroomClassBlock;
    }

    /**
     * retrieve a {@link Classblock} object
     *
     * @param int|null  $classid
     * @param bool|null $blockid
     * @return mixed
     * @staticvar object reference to the {@link Classblock} object
     *
     */
    public function get($classid = null, $blockid = null)
    {
        if (null === $blockid) {
            return false;
        }
        $blockid = (int)$blockid;
        $classid = (int)$classid;
        if ($blockid > 0) {
            $sql = 'SELECT * FROM ' . $this->table . ' WHERE blockid=' . $blockid . ' AND classid=' . $classid;
            if (!$result = $this->db->query($sql)) {
                return false;
            }
            $ClassroomClassBlock = $this->create(false);
            $ClassroomClassBlock->assignVars($this->db->fetchArray($result));

            return $ClassroomClassBlock;
        }

        return false;
    }

    /**
     * Save Classblock in database
     * @param object $obj reference to the {@link Classblock} object
     * @param bool   $force
     * @return bool
     */
    public function insert($obj, $force = true)
    {
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
        } elseif (!$this->dml->updateClassblock($obj)) {
            return false;
        }

        return true;
    }

    /**
     * delete a {@link Classblock} from the database
     *
     * @param      $block
     * @param bool $force
     * @return bool
     */
    public function delete($block, $force = false)
    {
        return $this->dml->deleteClassblock($block);
    }

    /**
     * add an error
     *
     * @param $err_str
     * @access public
     */
    public function setErrors($err_str)
    {
        $this->_errors[] = trim($err_str);
    }

    /**
     * return the errors for this object as an array
     *
     * @return array an array of errors
     * @access public
     */
    public function getErrors()
    {
        return $this->_errors;
    }

    /**
     * get {@link Classblock} objects from criteria
     *
     * @param null $criteria  reference to a {@link Criteria} or {@link CriteriaCompo} object
     * @param bool $id_as_key
     * @param bool $as_object if true, the returned array will be {@link Classblock} objects
     *
     * @return array
     * @staticvar array $ret array of {@link Classblock} objects
     *
     */

    public function &getObjects($criteria = null, $id_as_key = false, $as_object = true)
    {
        $helper = Helper::getInstance();
        $ret    = [];
        $start  = 0;
        $limit  = $start;
        $sql    = 'SELECT cb.blockid, cb.classid, cb.visible, cb.side, cb.weight, b.blocktypeid, b.classroomid, b.name AS blockname
                FROM ' . $this->table . ' cb,
                     ' . $this->db->prefix('classroom_block') . ' b
                WHERE cb.blockid=b.blockid';
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
        $blockHandler = $helper->getHandler('Block');
        while (false !== ($myrow = $this->db->fetchArray($result))) {
            if ($as_object) {
                $classroomClassBlock = $this->create(false);
                $classroomClassBlock->assignVar('blockid', $myrow['blockid']);
                $classroomClassBlock->assignVar('classid', $myrow['classid']);
                $classroomClassBlock->assignVar('visible', $myrow['visible']);
                $classroomClassBlock->assignVar('side', $myrow['side']);
                $classroomClassBlock->assignVar('weight', $myrow['weight']);
                $classroomClassBlock->block = $blockHandler->create(false, $myrow['blocktypeid']);
                $classroomClassBlock->block->assignVar('blockid', $myrow['blockid']);
                $classroomClassBlock->block->assignVar('classroomid', $myrow['classroomid']);
                $classroomClassBlock->block->assignVar('name', $myrow['blockname']);
                $ret[] =& $classroomClassBlock;
                unset($classroomClassBlock);
            } else {
                $ret[] = $myrow;
            }
        }

        return $ret;
    }

    /**
     * insert a range of {@link Classblock} following form submissal
     *
     * @param object &$block {@link Block} object
     *
     * @return int
     */
    public function updateInsert(&$block)
    {
        $helper = Helper::getInstance();
        $classHandler = $helper->getHandler('ClassroomClass');
        if ('' == $_POST['blockname']) {
            $this->setErrors('Block Name not set');

            return;
        }
        if ($_POST['blockname'] != $block->getVar('name')) {
            $blockHandler = $helper->getHandler('Block');
            $block->setVar('name', $_POST['blockname']);
            $blockHandler->insert($block);
        }

        $criteria = new \Criteria('classroomid', $_POST['classroomid']);
        $classes  = $classHandler->getObjects($criteria, true, true);

        $this->dml->clearClassBlocks($_POST['b']);
        foreach ($classes as $classid => $class) {
            $obj = $this->create();
            $obj->setVar('blockid', $_POST['b']);
            $obj->setVar('classid', $classid);
            if (isset($_POST['visible'][$classid])) {
                $obj->setVar('visible', 1);
            }
            $obj->setVar('side', $_POST['position'][$classid]);
            $obj->setVar('weight', $_POST['weight'][$classid]);
            if (!$this->insert($obj)) {
                $this->setErrors('Failed Saving ' . $class->getVar('name'));
            }
        }

        return;
    }

    /**
     * Return an array of a block's position, weight and side for each of a classroom's classes
     *
     * @param object $block {@link Block} child object
     *
     * @return array
     */
    public function getSettings($block)
    {
        $ret            = [];
        $block_criteria = new \Criteria('b.blockid', $block->getVar('blockid'));
        $classblocks    = $this->getObjects($block_criteria);
        if (count($classblocks) > 0) {
            foreach ($classblocks as $key => $classblock) {
                $ret[$classblock->getVar('classid')]['visible'] = $classblock->getVar('visible');
                $ret[$classblock->getVar('classid')]['side']    = $classblock->getVar('side');
                $ret[$classblock->getVar('classid')]['weight']  = $classblock->getVar('weight');
            }
        }

        return $ret;
    }
}
