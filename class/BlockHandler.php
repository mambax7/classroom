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
 * Classes for managing Blocks
 *
 *
 * @author  Jan Keller Pedersen
 * @package modules
 */

use XoopsModules\Classroom\Blocktypes;

/**
 * Block handler class
 *
 * @package    modules
 * @subpackage Block
 */
class BlockHandler extends \XoopsPersistableObjectHandler
{
    /**
     * An instance of database manipulation (INSERT, UPDATE, DELETE) queries
     * @access private
     * @var    {@link BlockDML} object
     */
    public $dml;
    /**
     * Table name
     * @access private
     * @var string
     */
    public $table;

    /**
     * Constructor sets up {@link BlockHandler} object
     * @param \XoopsDatabase|null $db to {@link Database} object
     */
    public function __construct(\XoopsDatabase $db = null)
    {
        $this->db    = $db;
        $this->table = $this->db->prefix('classroom_block');
        $this->dml   = new BlockDML($db, $this->table);
    }

    /**
     * create a new Block object
     *
     * @param bool $isNew flags the new object as "new"
     * @param int  $blocktypeid
     * @return object
     * @staticvar object $Block {@link Block} object
     *
     */
    public function &create($isNew = true, $blocktypeid = 1)
    {
        global $xoopsModule;
        $blocktypes = $xoopsModule->getInfo('blocktypes');
        //        require_once XOOPS_ROOT_PATH . '/modules/classroom/class/blocktypeloader.php';
        //        $classname      = 'Blocktypes\\' . ucfirst($blocktypes[$blocktypeid]['name']) . 'Block';
        $classname      = __NAMESPACE__ . '\Blocktypes\\' . ucfirst($blocktypes[$blocktypeid]['name']) . 'Block';
        $classroomBlock = new $classname();
        if ($isNew) {
            $classroomBlock->setNew();
        }

        return $classroomBlock;
    }

    /**
     * retrieve a {@link Block} object
     *
     * @param int|null $id ID of the Block
     * @param null     $blockid
     * @return mixed
     * @staticvar object reference to the {@link Block} object
     *
     */
    public function get($id = null, $blockid = null)
    {
        if (null === $id) {
            return false;
        }
        $id = (int)$id;
        if ($id > 0) {
            $sql = 'SELECT * FROM ' . $this->table . ' WHERE blockid=' . $id;
            if (!$result = $this->db->query($sql)) {
                return false;
            }
            $values = $this->db->fetchArray($result);
            $Block  = $this->create(false, $values['blocktypeid']);
            $Block->assignVars($values);

            return $Block;
        }

        return false;
    }

    /**
     * Save Block in database
     * @param object $obj reference to the {@link Block} object
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
            if (!$this->dml->insertBlock($obj)) {
                return false;
            }
        } else {
            if (!$this->dml->updateBlock($obj)) {
                return false;
            }
        }

        return true;
    }

    /**
     * delete a {@link Block} from the database
     *
     * @param \XoopsObject $block
     * @param bool         $force
     * @return bool
     */
    public function delete($block, $force = false)
    {
        if ($block->delete()) {
            return $this->dml->deleteBlock($block);
        }

        return false;
    }

    /**
     * get {@link Block} objects from criteria
     *
     * @param object $criteria   reference to a {@link Criteria} or {@link CriteriaCompo} object
     * @param bool   $as_objects if true, the returned array will be {@link Block} objects
     * @param bool   $id_as_key  if true, the returned array will have the Block ids as key
     *
     * @staticvar array $ret array of {@link Block} objects
     *
     * @return array
     */
    public function &getObjects($criteria = null, $as_objects = true, $id_as_key = false)
    {
        $ret   = [];
        $start = 0;
        $limit = $start;
        $sql   = 'SELECT * FROM ' . $this->table . ' b';
        if (isset($criteria) && $criteria instanceof \CriteriaElement) {
            $sql .= ' ' . $criteria->renderWhere();
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
        while (false !== ($myrow = $this->db->fetchArray($result))) {
            if ($as_objects) {
                $Block = $this->create(false, $myrow['blocktypeid']);
                $Block->assignVars($myrow);
                if ($id_as_key) {
                    $ret[$myrow['blockid']] =& $Block;
                } else {
                    $ret[] =& $Block;
                }
                unset($Block);
            } else {
                if ($id_as_key) {
                    $ret[$myrow['blockid']] = $myrow;
                } else {
                    $ret[] = $myrow;
                }
            }
        }

        return $ret;
    }

    /**
     * update or insert a {@link ClassroomBase} following form submissal
     *
     * @return string
     */
    public function updateInsert()
    {
        if (isset($_POST['blockid']) && $_POST['blockid'] > 0) {
            $obj = $this->create(false);
            $obj->assignVar('blockid', $_POST['blockid']);
        } else {
            $obj = $this->create();
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
    public function listBlocks($classroomid)
    {
        $criteria = new \Criteria('classroomid', (int)$classroomid);
        $criteria->setSort('name');
        $blocks = $this->getObjects($criteria);
        $return = '<table>';
        if (count($blocks) > 0) {
            foreach ($blocks as $key => $block) {
                $class  = isset($class) && 'odd' == $class ? 'even' : 'odd';
                $return .= "<tr class='" . $class . "'>
                            <td>" . $block->getVar('blockid') . "</td>
                            <td><a href='manage.php?op=editblock&amp;blockid=" . $block->getVar('blockid') . "'>" . $block->getVar('name') . '</a></td>
                            <td>' . $block->getVar('blocktypename') . "</td>
                            <td><form action='manage.php' method='POST'>
                                <input type='hidden' name='op' value='classblock'>
                                <input type='hidden' name='b' value='" . $block->getVar('blockid') . "'>
                                <input type='hidden' name='cr' value='" . $block->getVar('classroomid') . "'>
                                <input type='submit' name='edit' id='edit' value='" . _CR_MA_EDITPOSITIONS . "'></form></td>
                            <td><form action='manage.php' method='POST'>
                                <input type='hidden' name='op' value='block'>
                                <input type='hidden' name='b' value='" . $block->getVar('blockid') . "'>
                                <input type='hidden' name='cr' value='" . $block->getVar('classroomid') . "'>
                                <input type='submit' name='delete' id='delete' value='" . _CR_MA_DELETE . "'></form></td>
                            </tr>";
            }
        }
        $return .= '</table>';

        return $return;
    }
}
