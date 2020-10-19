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
 * Classes for managing classrooms
 *
 *
 * @author  Jan Keller Pedersen
 * @package modules
 */

/**
 * Classroom handler class
 *
 * @package    modules
 * @subpackage classroom
 */
class ClassroomHandler extends \XoopsPersistableObjectHandler
{
    /**
     * An instance of database manipulation (INSERT, UPDATE, DELETE) queries
     * @access private
     * @var    {@link ClassroomDML} object
     */
    public $dml;
    /**
     * Table name
     * @access private
     * @var string
     */
    public $table;

    /**
     * Constructor sets up {@link ClassroomBaseHandler} object
     * @param \XoopsDatabase|null $db to {@link Database} object
     */
    public function __construct(\XoopsDatabase $db = null)
    {
        $this->db    = $db;
        $this->table = $this->db->prefix('classroom_classroom');
        $this->dml   = new ClassroomDML($db, $this->table);
    }

    /**
     * create a new Classroom object
     *
     * @param bool $isNew flags the new object as "new"
     * @staticvar object $classroom {@link ClassroomBase} object
     *
     * @return object
     */
    public function &create($isNew = true)
    {
        $classroom = new ClassroomBase();
        if ($isNew) {
            $classroom->setNew();
        }

        return $classroom;
    }

    /**
     * retrieve a {@link ClassroomBase} object
     *
     * @param int|null $id ID of the classroom
     * @param null     $blockid
     * @return mixed
     * @staticvar object reference to the {@link ClassroomBase} object
     *
     */
    public function get($id = null, $blockid = null)
    {
        if (false === $id) {
            return false;
        }
        $id = (int)$id;
        if ($id > 0) {
            $criteria  = new \Criteria('classroomid', $id);
            $classroom =& $this->getObjects($criteria, true, false);

            return $classroom[0];
        }

        return false;
    }

    /**
     * Save classroom in database
     * @param object $obj reference to the {@link ClassroomBase} object
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
            if (!$this->dml->insertClassroom($obj)) {
                return false;
            }
        } else {
            if (!$this->dml->updateClassroom($obj)) {
                return false;
            }
        }

        return true;
    }

    /**
     * delete a {@link ClassroomBase} from the database
     *
     * @param object $classroom reference to the {@link ClassroomBase} to delete
     * @param bool   $force
     * @return bool
     */
    public function delete($classroom, $force = false)
    {
        return $this->dml->deleteClassroom($classroom);
    }

    /**
     * get {@link ClassroomBase} objects from criteria
     *
     * @param object $criteria   reference to a {@link Criteria} or {@link CriteriaCompo} object
     * @param bool   $as_objects if true, the returned array will be {@link ClassroomBase} objects
     * @param bool   $id_as_key  if true, the returned array will have the classroom ids as key
     *
     * @staticvar array $ret array of {@link ClassroomBase} objects
     *
     * @return array
     */
    public function &getObjects($criteria = null, $as_objects = true, $id_as_key = false)
    {
        $ret   = [];
        $start = 0;
        $limit = $start;
        $sql   = 'SELECT c.*, u.name AS ownername FROM ' . $this->table . ' c, ' . $this->db->prefix('users') . ' u WHERE u.uid=c.owner';
        if (isset($criteria) && $criteria instanceof \CriteriaElement) {
            $sql .= ' AND ' . $criteria->render();
            if ('' != $criteria->getSort()) {
                $sql .= ' ORDER BY ' . $criteria->getSort() . ' ' . $criteria->getOrder();
            } else {
                $sql .= ' ORDER BY weight ASC';
            }
            $limit = $criteria->getLimit();
            $start = $criteria->getStart();
        } else {
            $sql .= ' ORDER BY weight ASC';
        }
        $result = $this->db->query($sql, $limit, $start);
        if (!$result) {
            return $ret;
        }
        while (false !== ($myrow = $this->db->fetchArray($result))) {
            if ($as_objects) {
                $classroom = $this->create(false);
                $classroom->assignVars($myrow);
                if ($id_as_key) {
                    $ret[$myrow['classroomid']] =& $classroom;
                } else {
                    $ret[] =& $classroom;
                }
                unset($classroom);
            } else {
                if ($id_as_key) {
                    $ret[$myrow['classroomid']] = $myrow;
                } else {
                    $ret[] = $myrow;
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
    public function getSubitemCount($classroomid = false)
    {
        $ret            = [];
        $sql            = 'SELECT count(c.classid) AS classes
                FROM ' . $this->db->prefix('classroom_class') . ' c
                WHERE c.classroomid = ' . (int)$classroomid;
        $result         = $this->db->query($sql);
        $classes        = $this->db->fetchArray($result);
        $ret['classes'] = $classes['classes'];
        $sql            = 'SELECT count(b.blockid) AS blocks
                FROM ' . $this->db->prefix('classroom_block') . ' b
                WHERE b.classroomid = ' . (int)$classroomid;
        $result         = $this->db->query($sql);
        $blocks         = $this->db->fetchArray($result);
        $ret['blocks']  = $blocks['blocks'];

        return $ret;
    }

    /**
     * update or insert a {@link ClassroomBase} following form submissal
     *
     * @return string
     */
    public function updateInsert()
    {
        if (!isset($_POST['name']) || '' == $_POST['name']) {
            return false;
        }
        if (!isset($_POST['owner']) || !$_POST['owner']) {
            return false;
        }
        if (isset($_POST['classroomid']) && $_POST['classroomid'] > 0) {
            $obj = $this->create(false);
            $obj->assignVar('classroomid', $_POST['classroomid']);
        } else {
            $obj = $this->create();
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
     * @param $obj
     * @return int ID of the division object
     */
    public function updatePermissions($obj)
    {
        global $xoopsModule;
        $gpermHandler = xoops_getHandler('groupperm');
        if ($obj->getVar('classroomid') > 0) {
            $del_criteria = new CriteriaCompo(new \Criteria('gperm_modid', $xoopsModule->getVar('mid')));
            $del_criteria->add(new \Criteria('gperm_name', 'classroom'));
            $del_criteria->add(new \Criteria('gperm_itemid', $obj->getVar('classroomid')));
            $gpermHandler->deleteAll($del_criteria);
        }
        foreach ($_POST['moderators'] as $groupid) {
            $gpermHandler->addRight('classroom', $obj->getVar('classroomid'), $groupid, $xoopsModule->getVar('mid'));
        }

        return $obj->getVar('classroomid');
    }
}
