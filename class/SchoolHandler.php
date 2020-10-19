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
 * Classes for managing Schools
 *
 *
 * @author  Jan Keller Pedersen
 * @package modules
 */

/**
 * School handler class
 *
 * @package    modules
 * @subpackage School
 */
class SchoolHandler extends \XoopsPersistableObjectHandler
{
    /**
     * An instance of database manipulation (INSERT, UPDATE, DELETE) queries
     * @access private
     * @var    {@link SchoolDML} object
     */
    public $dml;
    /**
     * Table name
     * @access private
     * @var string
     */
    public $table;

    /**
     * Constructor sets up {@link SchoolHandler} object
     * @param \XoopsDatabase|null $db to {@link Database} object
     */
    public function __construct(\XoopsDatabase $db = null)
    {
        $this->db    = $db;
        $this->table = $this->db->prefix('classroom_school');
        $this->dml   = new SchoolDML($db, $this->table);
    }

    /**
     * create a new School object
     *
     * @param bool $isNew flags the new object as "new"
     * @staticvar object $School {@link School} object
     *
     * @return object
     */
    public function &create($isNew = true)
    {
        $School = new School();
        if ($isNew) {
            $School->setNew();
        }

        return $School;
    }

    /**
     * retrieve a {@link School} object
     *
     * @param int|null $id ID of the School
     * @param null     $blockid
     * @return mixed
     * @staticvar object reference to the {@link School} object
     *
     */
    public function get($id = null, $blockid = null)
    {
        if (null === $id) {
            return false;
        }
        $id = (int)$id;
        if ($id > 0) {
            $criteria = new \Criteria('schoolid', $id);
            $School   =& $this->getObjects($criteria, true, false);

            return $School[0];
        }

        return false;
    }

    /**
     * Save School in database
     * @param object $obj reference to the {@link School} object
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
            if (!$this->dml->insertSchool($obj)) {
                return false;
            }
        } else {
            if (!$this->dml->updateSchool($obj)) {
                return false;
            }
        }

        return true;
    }

    /**
     * delete a {@link School} from the database
     *
     * @param \XoopsObject $School reference to the {@link School} to delete
     * @param bool         $force
     * @return bool
     */
    public function delete($School, $force = false)
    {
        return $this->dml->deleteSchool($School);
    }

    /**
     * get {@link School} objects from criteria
     *
     * @param object $criteria   reference to a {@link Criteria} or {@link CriteriaCompo} object
     * @param bool   $as_objects if true, the returned array will be {@link School} objects
     * @param bool   $id_as_key  if true, the returned array will have the School ids as key
     *
     * @staticvar array $ret array of {@link School} objects
     *
     * @return array
     */
    public function &getObjects($criteria = null, $as_objects = true, $id_as_key = true)
    {
        $ret   = [];
        $start = 0;
        $limit = $start;
        $sql   = 'SELECT s.*, u.name AS headname FROM ' . $this->table . ' s, ' . $this->db->prefix('users') . ' u WHERE u.uid=s.head';
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
                $School = $this->create(false);
                $School->assignVars($myrow);
                if ($id_as_key) {
                    $ret[$myrow['schoolid']] =& $School;
                } else {
                    $ret[] =& $School;
                }
                unset($School);
            } else {
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
    public function getSubitemCount($schoolid)
    {
        $ret = [];
        $sql = 'SELECT d.divisionid
                FROM ' . $this->db->prefix('classroom_division') . ' d
                WHERE d.schoolid =' . (int)$schoolid;

        $result = $this->db->query($sql);
        while (false !== ($row = $this->db->fetchArray($result))) {
            $divisions[$row['divisionid']] = $row['divisionid'];
        }
        $sql    = 'SELECT cr.classroomid
                FROM ' . $this->db->prefix('classroom_classroom') . ' cr
                WHERE cr.divisionid IN (' . implode(',', array_keys($divisions)) . ')';
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
        $ret['divisions']  = count($divisions);
        $ret['classrooms'] = count($classrooms);
        $ret['classes']    = count($classes);
        $ret['blocks']     = count($blocks);

        return $ret;
    }

    /**
     * update or insert a {@link School} following form submissal
     *
     * @return string
     */
    public function updateInsert()
    {
        if (!isset($_POST['name']) || '' == $_POST['name']) {
            return false;
        }
        if (!isset($_POST['head']) || !$_POST['head']) {
            return false;
        }
        if (isset($_POST['schoolid']) && $_POST['schoolid'] > 0) {
            $post_school = $this->create(false);
            $post_school->assignVar('schoolid', $_POST['schoolid']);
        } else {
            $post_school = $this->create();
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
     * @param object $school {@link School} to update permissions for
     *
     * @return int ID of the school object
     */
    public function updatePermissions($school)
    {
        global $xoopsModule;
        $gpermHandler = xoops_getHandler('groupperm');
        if ($school->getVar('schoolid') > 0) {
            $del_criteria = new CriteriaCompo(new \Criteria('gperm_modid', $xoopsModule->getVar('mid')));
            $del_criteria->add(new \Criteria('gperm_name', 'school'));
            $del_criteria->add(new \Criteria('gperm_itemid', $school->getVar('schoolid')));
            $gpermHandler->deleteAll($del_criteria);
        }
        foreach ($_POST['moderators'] as $groupid) {
            $gpermHandler->addRight('school', $school->getVar('schoolid'), $groupid, $xoopsModule->getVar('mid'));
        }

        return $school->getVar('schoolid');
    }

    /**
     * Updates cached template for overview pages
     */
    public function updateCache()
    {
        require_once XOOPS_ROOT_PATH . '/class/template.php';
        global $xoopsModule;
        $xoopsTpl = new \XoopsTpl();
        $xoopsTpl->clear_cache('db:cr_overview.tpl', 'mod_' . $xoopsModule->getVar('dirname') . '|' . md5('/modules/classroom/index.php'));
    }
}
