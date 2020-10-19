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
 * Class handler class
 *
 * @package    modules
 * @subpackage classroom
 */
class ClassroomClassHandler extends \XoopsPersistableObjectHandler
{
    /**
     * An instance of database manipulation (INSERT, UPDATE, DELETE) queries
     * @access private
     * @var    {@link ClassroomClassDML} object
     */
    public $dml;
    /**
     * Table name
     * @access private
     * @var string
     */
    public $table;

    /**
     * Constructor sets up {@link ClassroomHandler} object
     * @param \XoopsDatabase|null $db to {@link Database} object
     */
    public function __construct(\XoopsDatabase $db = null)
    {
        $this->db    = $db;
        $this->table = $this->db->prefix('classroom_class');
        $this->dml   = new ClassroomClassDML($db, $this->table);
    }

    /**
     * create a new Class object
     *
     * @param bool $isNew flags the new object as "new"
     * @staticvar object $classroom {@link ClassroomClass} object
     *
     * @return object
     */
    public function &create($isNew = true)
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
     * @param int|null $id ID of the class
     * @param null     $blockid
     * @return mixed
     * @staticvar object reference to the {@link ClassroomClass} object
     *
     */
    public function get($id = null, $blockid = null)
    {
        if (false === $id) {
            return false;
        }
        $id = (int)$id;
        if ($id > 0) {
            $sql = 'SELECT * FROM ' . $this->table . ' WHERE classid=' . $id;
            if (!$result = $this->db->query($sql)) {
                return false;
            }
            $classroom = $this->create(false);
            $classroom->assignVars($this->db->fetchArray($result));

            return $classroom;
        }

        return false;
    }

    /**
     * Save class in database
     * @param object $obj reference to the {@link ClassroomClass} object
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
            if (!$this->dml->insertClass($obj)) {
                return false;
            }
        } else {
            if (!$this->dml->updateClass($obj)) {
                return false;
            }
        }

        return true;
    }

    /**
     * delete a {@link ClassroomClass} from the database
     *
     * @param      $obj
     * @param bool $force
     * @return bool
     */
    public function delete($obj, $force = false)
    {
        return $this->dml->deleteClass($obj);
    }

    /**
     * get {@link ClassroomClass} objects from criteria
     *
     * @param object $criteria   reference to a {@link Criteria} or {@link CriteriaCompo} object
     * @param bool   $as_objects if true, the returned array will be {@link ClassroomClass} objects
     * @param bool   $id_as_key  if true, the returned array will have the class ids as key
     *
     * @staticvar array $ret array of {@link ClassroomClass} objects
     *
     * @return array
     */
    public function &getObjects($criteria = null, $as_objects = true, $id_as_key = false)
    {
        $ret   = [];
        $start = 0;
        $limit = $start;
        $sql   = 'SELECT * FROM ' . $this->table;
        if (isset($criteria) && $criteria instanceof \CriteriaElement) {
            $sql .= ' ' . $criteria->renderWhere();
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
                $class = $this->create(false);
                $class->assignVars($myrow);
                if ($id_as_key) {
                    $ret[$myrow['classid']] =& $class;
                } else {
                    $ret[] =& $class;
                }
                unset($class);
            } else {
                if ($id_as_key) {
                    $ret[$myrow['classid']] = $myrow;
                } else {
                    $ret[] = $myrow;
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
    public function updateInsert()
    {
        if (!isset($_POST['name']) || '' == $_POST['name']) {
            return false;
        }
        if (isset($_POST['classid']) && $_POST['classid'] > 0) {
            $obj = $this->create(false);
            $obj->assignVar('classid', $_POST['classid']);
        } else {
            $obj = $this->create();
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
