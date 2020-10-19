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
 * Classes for managing Divisions
 *
 *
 * @author  Jan Keller Pedersen
 * @package modules
 */

/**
 * Division handler class
 *
 * @package    modules
 * @subpackage Division
 */
class DivisionHandler extends \XoopsPersistableObjectHandler
{
    /**
     * An instance of database manipulation (INSERT, UPDATE, DELETE) queries
     * @access private
     * @var    {@link DivisionDML} object
     */
    public $dml;
    /**
     * Table name
     * @access private
     * @var string
     */
    public $table;

    /**
     * Constructor sets up {@link DivisionHandler} object
     * @param \XoopsDatabase|null $db to {@link Database} object
     */
    public function __construct(\XoopsDatabase $db = null)
    {
        $this->db    = $db;
        $this->table = $this->db->prefix('classroom_division');
        $this->dml   = new DivisionDML($db, $this->table);
    }

    /**
     * create a new Division object
     *
     * @param bool $isNew flags the new object as "new"
     * @staticvar object $Division {@link Division} object
     *
     * @return object
     */
    public function &create($isNew = true)
    {
        $Division = new Division();
        if ($isNew) {
            $Division->setNew();
        }

        return $Division;
    }

    /**
     * retrieve a {@link Division} object
     *
     * @param int|null $id ID of the Division
     * @param null     $blockid
     * @return mixed
     * @staticvar object reference to the {@link Division} object
     *
     */
    public function get($id = null, $blockid = null)
    {
        if (null === $id) {
            return false;
        }
        $id = (int)$id;
        if ($id > 0) {
            $criteria          = new \Criteria('divisionid', $id);
            $classroomDivision =& $this->getObjects($criteria, true, false);

            return $classroomDivision[0];
        }

        return false;
    }

    /**
     * Save Division in database
     * @param object $obj reference to the {@link Division} object
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
            if (!$this->dml->insertDivision($obj)) {
                return false;
            }
        } else {
            if (!$this->dml->updateDivision($obj)) {
                return false;
            }
        }

        return true;
    }

    /**
     * delete a {@link Division} from the database
     *
     * @param \XoopsObject $Division reference to the {@link Division} to delete
     * @param bool         $force
     * @return bool
     */
    public function delete($Division, $force = false)
    {
        return $this->dml->deleteDivision($Division);
    }

    /**
     * get {@link Division} objects from criteria
     *
     * @param object $criteria   reference to a {@link Criteria} or {@link CriteriaCompo} object
     * @param bool   $as_objects if true, the returned array will be {@link Division} objects
     * @param bool   $id_as_key  if true, the returned array will have the Division ids as key
     *
     * @staticvar array $ret array of {@link Division} objects
     *
     * @return array
     */
    public function &getObjects($criteria = null, $as_objects = true, $id_as_key = true)
    {
        $ret   = [];
        $start = 0;
        $limit = $start;
        $sql   = 'SELECT d.*, u.name AS directorname FROM ' . $this->table . ' d, ' . $this->db->prefix('users') . ' u WHERE u.uid=d.director';
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
                $Division = $this->create(false);
                $Division->assignVars($myrow);
                if ($id_as_key) {
                    $ret[$myrow['divisionid']] =& $Division;
                } else {
                    $ret[] =& $Division;
                }
                unset($Division);
            } else {
                if ($id_as_key) {
                    $ret[$myrow['divisionid']] = $myrow;
                } else {
                    $ret[] = $myrow;
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
    public function getSubitemCount($divid = false)
    {
        $ret    = [];
        $sql    = 'SELECT cr.classroomid
                FROM ' . $this->db->prefix('classroom_classroom') . ' cr
                WHERE cr.divisionid = ' . (int)$divid;
        $result = $this->db->query($sql);
        while (false !== ($row = $this->db->fetchArray($result))) {
            $classrooms[$row['classroomid']] = $row['classroomid'];
        }
        $sql    = 'SELECT c.classid
                FROM ' . $this->db->prefix('classroom_class') . ' c
                WHERE c.classroomid IN (' . implode(',', array_keys($classrooms)) . ')';
        $result = $this->db->query($sql);
        while (false !== ($row = $this->db->fetchArray($result))) {
            $classes[$row['classid']] = $row['classid'];
        }
        $sql    = 'SELECT b.blockid
                FROM ' . $this->db->prefix('classroom_block') . ' b
                WHERE b.classroomid IN (' . implode(',', array_keys($classrooms)) . ')';
        $result = $this->db->query($sql);
        while (false !== ($row = $this->db->fetchArray($result))) {
            $blocks[$row['blockid']] = $row['blockid'];
        }
        $ret['classrooms'] = count($classrooms);
        $ret['classes']    = count($classes);
        $ret['blocks']     = count($blocks);

        return $ret;
    }

    /**
     * update or insert a {@link Division} following form submissal
     *
     * @return string
     */
    public function updateInsert()
    {
        if (!isset($_POST['name']) || '' == $_POST['name']) {
            return false;
        }
        if (!isset($_POST['director']) || !$_POST['director']) {
            return false;
        }
        if (isset($_POST['divisionid']) && $_POST['divisionid'] > 0) {
            $obj = $this->create(false);
            $obj->assignVar('divisionid', $_POST['divisionid']);
        } else {
            $obj = $this->create();
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
     * @param object $division {@link Division} to update permissions for
     *
     * @return int ID of the division object
     */
    public function updatePermissions($division)
    {
        global $xoopsModule;
        $gpermHandler = xoops_getHandler('groupperm');
        if ($division->getVar('divisionid') > 0) {
            $del_criteria = new CriteriaCompo(new \Criteria('gperm_modid', $xoopsModule->getVar('mid')));
            $del_criteria->add(new \Criteria('gperm_name', 'division'));
            $del_criteria->add(new \Criteria('gperm_itemid', $division->getVar('divisionid')));
            $gpermHandler->deleteAll($del_criteria);
        }
        foreach ($_POST['moderators'] as $groupid) {
            $gpermHandler->addRight('division', $division->getVar('divisionid'), $groupid, $xoopsModule->getVar('mid'));
        }

        return $division->getVar('divisionid');
    }
}
