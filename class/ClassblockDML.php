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
 * Classblock database manipulation class
 *
 * @package    modules
 * @subpackage Classroom
 */
class ClassblockDML
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
     * Constructor sets up {@link ClassblockDML} object
     * @param \XoopsDatabase $db to {@link Database} object
     * @param                $table
     */
    public function __construct(\XoopsDatabase $db, $table)
    {
        $this->db    = $db;
        $this->table = $table;
    }

    /**
     * Insert a Classblock object into the database
     *
     * @param Reference $obj to {@link Classblock} object to save
     * @return bool
     * @return bool
     */
    public function insertClassblock($obj)
    {
        $sql = 'INSERT INTO ' . $this->table . '
                    (blockid, classid, side, weight, visible)
                VALUES (' . (int)$obj->getVar('blockid') . ',
                       ' . (int)$obj->getVar('classid') . ",
                       '" . $obj->getVar('side') . "',
                       " . (int)$obj->getVar('weight') . ',
                       ' . (int)$obj->getVar('visible') . '
                       )';
        if (!$this->db->query($sql)) {
            return false;
        }
        $obj->setVar('classblockid', $this->db->getInsertId());
        $obj->_isNew = false;

        return true;
    }

    /**
     * Update an existing Classblock object in the database
     *
     * @param Reference $obj to {@link Classblock} object to save
     * @return bool
     * @return bool
     */
    public function updateClassblock($obj)
    {
        $sql = 'UPDATE ' . $this->table . '
                SET weight=' . (int)$obj->getVar('weight') . ',
                    visible=' . (int)$obj->getVar('visible') . '
                    side=' . (int)$obj->getVar('side') . '
                WHERE classid=' . (int)$obj->getVar('classid') . ' AND blockid=' . (int)$obj->getVar('blockid');
        if (!$this->db->query($sql)) {
            return false;
        }

        return true;
    }

    /**
     * Delete a Classblock object in the database
     *
     * @param Reference $obj to {@link Classblock} object to delete
     * @return bool
     * @return bool
     */
    public function deleteClassblock($obj)
    {
        $sql = 'DELETE FROM ' . $this->table . '
                WHERE classid=' . (int)$obj->getVar('classid') . ' AND blockid=' . (int)$obj->getVar('blockid');
        if (!$this->db->query($sql)) {
            return false;
        }

        return true;
    }

    /**
     * Clears a block's settings in a classroom
     * @param $id
     * @return mixed
     */
    public function clearClassBlocks($id)
    {
        $id  = (int)$id;
        $sql = 'DELETE FROM ' . $this->table . '
               WHERE blockid=' . $id;

        return $this->db->query($sql);
    }
}
