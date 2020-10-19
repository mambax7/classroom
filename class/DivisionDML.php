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
 * Division database manipulation class
 *
 * @package    modules
 * @subpackage Division
 */
class DivisionDML
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
     * Constructor sets up {@link DivisionDML} object
     * @param \XoopsDatabase $db to {@link Database} object
     * @param                $table
     */
    public function __construct(\XoopsDatabase $db, $table)
    {
        $this->db    = $db;
        $this->table = $table;
    }

    /**
     * Insert a Division object into the database
     *
     * @param Reference $obj to {@link Division} object to save
     * @return bool
     * @return bool
     */
    public function insertDivision($obj)
    {
        $myts = \MyTextSanitizer::getInstance();
        $sql  = 'INSERT INTO '
                . $this->table
                . '
                    (schoolid, name, description, director, location, weight)
                VALUES ('
                . (int)$obj->getVar('schoolid')
                . ", '"
                . $myts->addSlashes($obj->getVar('name', 'n'))
                . "', '"
                . $myts->addSlashes($obj->getVar('description', 'n'))
                . "', "
                . (int)$obj->getVar('director')
                . ", '"
                . $myts->addSlashes($obj->getVar('location', 'n'))
                . "', "
                . (int)$obj->getVar('weight')
                . ')';
        if (!$this->db->query($sql)) {
            return false;
        }
        $obj->setVar('divisionid', $this->db->getInsertId());
        $obj->_isNew = false;

        return true;
    }

    /**
     * Update an existing Division object in the database
     *
     * @param Reference $obj to {@link Division} object to save
     * @return bool
     * @return bool
     */
    public function updateDivision($obj)
    {
        $myts = \MyTextSanitizer::getInstance();
        $sql  = 'UPDATE ' . $this->table . "
                SET name='" . $myts->addSlashes($obj->getVar('name', 'n')) . "',
                    schoolid=" . (int)$obj->getVar('schoolid') . ",
                    description='" . $myts->addSlashes($obj->getVar('description', 'n')) . "',
                    location='" . $myts->addSlashes($obj->getVar('location', 'n')) . "',
                    director=" . (int)$obj->getVar('director') . ',
                    weight=' . (int)$obj->getVar('weight') . '
                WHERE divisionid=' . (int)$obj->getVar('divisionid');
        if (!$this->db->query($sql)) {
            return false;
        }

        return true;
    }

    /**
     * Delete a Division object in the database
     *
     * @param Reference $obj to {@link Division} object to delete
     * @return bool
     * @return bool
     */
    public function deleteDivision($obj)
    {
        $sql = 'DELETE FROM ' . $this->table . '
                WHERE divisionid=' . (int)$obj->getVar('divisionid');
        if (!$this->db->query($sql)) {
            return false;
        }

        return true;
    }
}
