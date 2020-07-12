<?php
// $Id: headline.php,v 1.1 2004/01/29 14:45:48 buennagel Exp $
//  ------------------------------------------------------------------------ //
//                XOOPS - PHP Content Management System                      //
//                    Copyright (c) 2000 XOOPS.org                           //
//                       <http://www.xoops.org/>                             //
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
// Author: Kazumi Ono (AKA onokazu)                                          //
// URL: http://www.myweb.ne.jp/, http://www.xoops.org/, http://jp.xoops.org/ //
// Project: The XOOPS Project                                                //
// ------------------------------------------------------------------------- //
class Obs_classroomHeadline extends XoopsObject
{

	function Obs_classroomHeadline()
	{
		$this->XoopsObject();
		$this->initVar('headline_id', XOBJ_DTYPE_INT, null, false);
		$this->initVar('headline_name', XOBJ_DTYPE_TXTBOX, null, true, 255);
		$this->initVar('headline_url', XOBJ_DTYPE_TXTBOX, 'http://', true, 255);
		$this->initVar('headline_rssurl', XOBJ_DTYPE_TXTBOX, 'http://', true, 255);
		$this->initVar('headline_cachetime', XOBJ_DTYPE_INT, 86400, false);
		$this->initVar('headline_encoding', XOBJ_DTYPE_OTHER, 'utf-8', false);
		$this->initVar('headline_xml', XOBJ_DTYPE_SOURCE, null, false);
		$this->initVar('headline_updated', XOBJ_DTYPE_INT, 0, false);
		$this->initVar('headline_display', XOBJ_DTYPE_INT, 1, false);
		$this->initVar('headline_weight', XOBJ_DTYPE_INT, 0, false);
		$this->initVar('headline_blockmax', XOBJ_DTYPE_INT, 10, false);
		$this->initVar('headline_blockid', XOBJ_DTYPE_INT, null, true);
		$this->initVar('headline_titlelength', XOBJ_DTYPE_INT, 25, true);
	}

	function cacheExpired()
	{
		if (time() - $this->getVar('headline_updated') > $this->getVar('headline_cachetime')) {
			return true;
		}
		return false;
	}
}

class Obs_classroomHeadlineHandler
{
	var $db;

	function Obs_classroomHeadlineHandler(&$db)
	{
		$this->db =& $db;
	}

	function &getInstance(&$db)
	{
		static $instance;
		if (!isset($instance)) {
			$instance = new Obs_classroomHeadlineHandler($db);
		}
		return $instance;
	}

	function &create()
	{
		return new Obs_classroomHeadline();
	}

	function &get($id)
	{
		$id = intval($id);
		if ($id > 0) {
			$sql = 'SELECT * FROM '.$this->db->prefix('cr_rss').' WHERE headline_id='.$id;
			if (!$result = $this->db->query($sql)) {
				return false;
			}
			$numrows = $this->db->getRowsNum($result);
			if ($numrows == 1) {
				$headline = new Obs_classroomHeadline();
				$headline->assignVars($this->db->fetchArray($result));
				return $headline;
			}
		}
		return false;
	}

	function insert(&$headline)
	{
		if (!$headline->cleanVars()) {
			return false;
		}
		foreach ($headline->cleanVars as $k => $v) {
			${$k} = $v;
		}
		if (empty($headline_id)) {
			$headline_id = $this->db->genId('cr_rss_id_seq');
			$sql = 'INSERT INTO '.$this->db->prefix('cr_rss').' (headline_id, headline_name, headline_url, headline_rssurl, headline_encoding, headline_cachetime, headline_xml, headline_updated, headline_weight, headline_display, headline_blockid, headline_blockmax, headline_titlelength) VALUES ('.$headline_id.', '.$this->db->quoteString($headline_name).', '.$this->db->quoteString($headline_url).', '.$this->db->quoteString($headline_rssurl).', '.$this->db->quoteString($headline_encoding).', '.$headline_cachetime.', '.$this->db->quoteString($headline_xml).', '.time().', '.$headline_weight.', '.$headline_display.', '.$headline_blockid.', '.$headline_blockmax.', '.$headline_titlelength.')';
		} else {
			$sql = 'UPDATE '.$this->db->prefix('cr_rss').' SET headline_name='.$this->db->quoteString($headline_name).', headline_url='.$this->db->quoteString($headline_url).', headline_rssurl='.$this->db->quoteString($headline_rssurl).', headline_encoding='.$this->db->quoteString($headline_encoding).', headline_cachetime='.$headline_cachetime.', headline_xml = '.$this->db->quoteString($headline_xml).', headline_updated='.$headline_updated.', headline_weight='.$headline_weight.', headline_display='.$headline_display.', headline_blockmax='.$headline_blockmax.', headline_titlelength='.$headline_titlelength.' WHERE headline_id='.$headline_id;
		}
		if (!$this->db->queryF($sql)) {
			return false;
		}
		if (empty($headline_id)) {
			$headline_id = $this->db->getInsertId();
		}
		$headline->assignVar('headline_id', $headline_id);
		return $headline_id;
	}

	function delete(&$headline)
	{
		$sql = sprintf("DELETE FROM %s WHERE headline_id = %u", $this->db->prefix('cr_rss'), $headline->getVar('headline_id'));
		if (!$this->db->queryF($sql)) {
			return false;
		}
		return true;
	}

	function &getObjects($criteria = null)
	{
		$ret = array();
		$limit = $start = 0;
		$sql = 'SELECT * FROM '.$this->db->prefix('cr_rss');
		if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
			$sql .= ' '.$criteria->renderWhere();
			$sql .= ' ORDER BY headline_weight '.$criteria->getOrder();
			$limit = $criteria->getLimit();
			$start = $criteria->getStart();
		}
		$result = $this->db->query($sql, $limit, $start);
		if (!$result) {
			return $ret;
		}
		while ($myrow = $this->db->fetchArray($result)) {
			$headline = new Obs_classroomHeadline();
			$headline->assignVars($myrow);
			$ret[] =& $headline;
			unset($headline);
		}
		return $ret;
	}

	function getCount($criteria = null)
	{
		$sql = 'SELECT COUNT(*) FROM '.$this->db->prefix('cr_rss');
		if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
			$sql .= ' '.$criteria->renderWhere();
		}
		if (!$result =& $this->db->query($sql)) {
			return 0;
		}
		list($count) = $this->db->fetchRow($result);
		return $count;
	}
}
?>