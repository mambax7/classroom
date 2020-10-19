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
 * Block class
 *
 * @package    modules
 * @subpackage Block
 */
class Block extends \XoopsObject
{
    /**
     * Database object
     */
    public $db;
    /**
     * Database table used for storing searchable values
     */
    public $table;

    /**
     * Constructor sets up {@link Block} object
     */
    public function __construct()
    {
        $this->db    = \XoopsDatabaseFactory::getDatabaseConnection();
        $this->table = $this->db->prefix('classroom_value');

        $this->initVar('blockid', \XOBJ_DTYPE_INT);
        $this->initVar('classroomid', \XOBJ_DTYPE_INT);
        $this->initVar('name', \XOBJ_DTYPE_TXTBOX);
        $this->initVar('blocktypeid', \XOBJ_DTYPE_INT);

        $this->initVar('template', \XOBJ_DTYPE_TXTBOX);
        $this->initVar('blocktypename', \XOBJ_DTYPE_TXTBOX);
        $this->initVar('bcachetime', \XOBJ_DTYPE_INT);
    }

    /**
     * Builds a Block of this type for display
     *
     * @abstract
     */
    public function buildBlock()
    {
    }

    /**
     * Builds a form for adding or editing a Block
     *
     * @abstract
     */
    public function buildForm()
    {
    }

    /**
     * Updates the specific block and its cache
     *
     */
    public function update()
    {
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
    public function updateBlock()
    {
    }

    /**
     * Updates the block's cache
     */
    public function updateCache()
    {
        require_once XOOPS_ROOT_PATH . '/class/template.php';
        $xoopsTpl = new \XoopsTpl();
        $xoopsTpl->clear_cache('db:' . $this->getVar('template'), 'blk_' . $this->getVar('blockid'));
    }

    /**
     * Deletes an item of the object (not always applicable)
     *
     * @abstract
     */
    public function deleteItem()
    {
        return true;
    }

    /**
     * Deletes block-specific database values (Not applicable for blocks without database interaction or blocks that only use cr_value table)
     *
     * @abstract
     */
    public function delete()
    {
        return true;
    }
}
