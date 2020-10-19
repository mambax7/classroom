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

/**
 * Block database manipulation class
 *
 * @package    modules
 * @subpackage Block
 */
class BlockDML
{
    /**
     * Instance of database class
     * @access private
     * @var    {@link Database} object
     */
    public $db;
    /**
     * Table name
     * @access private
     * @var string
     */
    public $table;

    /**
     * Constructor sets up {@link BlockDML} object
     * @param \XoopsDatabase $db to {@link Database} object
     * @param string         $table
     */
    public function __construct(\XoopsDatabase $db, $table)
    {
        $this->db    = $db;
        $this->table = $table;
    }

    /**
     * Insert a Block object into the database
     *
     * @param Reference $obj to {@link Block} object to save
     * @return bool
     * @return bool
     */
    public function insertBlock($obj)
    {
        $myts = \MyTextSanitizer::getInstance();
        $sql  = 'INSERT INTO ' . $this->table . '
                    (classroomid, name, blocktypeid)
                VALUES (' . (int)$obj->getVar('classroomid') . ",
                       '" . $myts->addSlashes($obj->getVar('name', 'n')) . "',
                       " . (int)$obj->getVar('blocktypeid') . '
                       )';
        if (!$this->db->query($sql)) {
            return false;
        }
        $obj->setVar('blockid', $this->db->getInsertId());
        $obj->_isNew = false;

        return true;
    }

    /**
     * Update an existing Block object in the database
     *
     * @param Reference $obj to {@link Block} object to save
     * @return bool
     * @return bool
     */
    public function updateBlock($obj)
    {
        $myts = \MyTextSanitizer::getInstance();
        $sql  = 'UPDATE ' . $this->table . '
                SET classroomid=' . (int)$obj->getVar('classroomid') . ",
                    name='" . $myts->addSlashes($obj->getVar('name', 'n')) . "',
                    blocktypeid=" . (int)$obj->getVar('blocktypeid') . '
                WHERE blockid=' . (int)$obj->getVar('blockid');
        if (!$this->db->query($sql)) {
            return false;
        }

        return true;
    }

    /**
     * Delete a Block object in the database
     *
     * @param Reference $obj to {@link Block} object to delete
     * @return bool
     * @return bool
     */
    public function deleteBlock($obj)
    {
        $sql = 'DELETE FROM ' . $this->db->prefix('classroom_value') . ' WHERE blockid=' . (int)$obj->getVar('blockid');
        if (!$this->db->query($sql)) {
            return false;
        }
        $sql = 'DELETE FROM ' . $this->table . '
                WHERE blockid=' . (int)$obj->getVar('blockid');
        if (!$this->db->query($sql)) {
            return false;
        }
        $sql = 'DELETE FROM ' . $this->db->prefix('classroom_classblock') . '
                WHERE blockid=' . (int)$obj->getVar('blockid');
        if (!$this->db->query($sql)) {
            return false;
        }

        return true;
    }
}
