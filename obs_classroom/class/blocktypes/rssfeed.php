<?
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
//  Author:  Mithrandir                                                      //

/**
* LinkBlock class
*
* @package modules
* @subpackage Classroom
*/

class RssBlock extends ClassroomBlock {
    
    function RssBlock() {
        $this->ClassroomBlock();
        $this->assignVar('template', 'cr_blocktype_rss.html');
        $this->assignVar('blocktypename', 'RSS Feed');
        $this->assignVar('bcachetime', 0);
    }
    
    /**
    * Builds a form for the block
    *
    */
    function buildForm() {
        include_once XOOPS_ROOT_PATH."/class/xoopsformloader.php";
        
        echo $this->showFeeds();
        
        if (isset($_GET['feedid'])) {
            $feed = $this->getFeed($_GET['feedid']);
        }
        else {
            $headline_handler =& xoops_getmodulehandler('headline', 'obs_classroom');
            $feed =& $headline_handler->create();
        }
        
        $form = new XoopsThemeForm('', 'rssform', 'manage.php');
        
        $form->addElement(new XoopsFormText(_CR_MA_SITENAME, 'headline_name', 50, 255, $feed->getVar('headline_name')), true);
        $form->addElement(new XoopsFormText(_CR_MA_URL, 'headline_url', 50, 255, $feed->getVar('headline_url')), true);
        $form->addElement(new XoopsFormText(_CR_MA_URLEDFXML, 'headline_rssurl', 50, 255, $feed->getVar('headline_rssurl')), true);
        $form->addElement(new XoopsFormText(_CR_MA_WEIGHT, 'headline_weight', 4, 3, $feed->getVar('headline_weight')));
        $enc_sel = new XoopsFormSelect(_CR_MA_ENCODING, 'headline_encoding', $feed->getVar('headline_encoding'));
        $enc_sel->addOptionArray(array('utf-8' => 'UTF-8', 'iso-8859-1' => 'ISO-8859-1', 'us-ascii' => 'US-ASCII'));
        $form->addElement($enc_sel);
        $cache_sel = new XoopsFormSelect(_CR_MA_CACHETIME, 'headline_cachetime', $feed->getVar('headline_cachetime'));
        $cache_sel->addOptionArray(array('3600' => _HOUR, '18000' => sprintf(_HOURS, 5), '86400' => _DAY, '259200' => sprintf(_DAYS, 3), '604800' => _WEEK, '2592000' => _MONTH));
        $form->addElement($cache_sel);
        
        $form->addElement(new XoopsFormRadioYN(_CR_MA_DISPLAY, 'headline_display', $feed->getVar('headline_display'), _YES, _NO));
        $bmax_sel = new XoopsFormSelect(_CR_MA_DISPMAX, 'headline_blockmax', $feed->getVar('headline_blockmax'));
        $bmax_sel->addOptionArray(array('1' => 1, '5' => 5, '10' => 10, '15' => 15, '20' => 20, '25' => 25, '30' => 30));
        $form->addElement($bmax_sel);
        
        $form->addElement(new XoopsFormText(_CR_MA_TITLELENGTH, 'headline_titlelength', 4, 3, $feed->getVar('headline_titlelength')), true);
        
        if ($feed->getVar('headline_id') > 0) {
            $form->addElement(new XoopsFormHidden('feedid', $feed->getVar('headline_id')));
        }
        $form->addElement(new XoopsFormHidden('op', 'editblock'));
        $form->addElement(new XoopsFormHidden('blockid', $this->getVar('blockid')));
        $form->addElement(new XoopsFormButton('', 'submit', _CR_MA_SUBMIT, 'submit'));
        $form->display();
    }
    
    /**
    * Builds a classroomblock
    *
    * @return array
    */
    function buildBlock() {
        global $xoopsConfig;
        $block = array();
        $headlines =& $this->getFeeds(true);
        $count = count($headlines);
        for ($i = 0; $i < $count; $i++) {
            include_once XOOPS_ROOT_PATH.'/modules/obs_classroom/class/headlinerenderer.php';
            $renderer = new XoopsHeadlineRenderer($headlines[$i]);
            if (!$renderer->renderBlock()) {
                if ($xoopsConfig['debug_mode'] == 2) {
                    $block['feeds'][] = sprintf(_HL_FAILGET, $headlines[$i]->getVar('headline_name')).'<br />'.$renderer->getErrors();
                }
                continue;
            }
            $block['feeds'][] = $renderer->getBlock();
        }
        return $block;
    }
    
    /**
    * Performs actions following buildForm submissal
    *
    */
    function updateBlock() {
        $hlman =& xoops_getmodulehandler('headline', 'obs_classroom');
        $obj =& $hlman->create();
        if (isset($_POST['feedid'])) {
            $obj->setVar('headline_id', intval($_POST['feedid']));
        }
        $obj->setVar('headline_blockid', $this->getVar('blockid'));
        $obj->setVar('headline_name', $_POST['headline_name']);
        $obj->setVar('headline_url', $_POST['headline_url']);
        $obj->setVar('headline_rssurl', $_POST['headline_rssurl']);
        $obj->setVar('headline_weight', intval($_POST['headline_weight']));
        $obj->setVar('headline_encoding', $_POST['headline_encoding']);
        $obj->setVar('headline_cachetime', intval($_POST['headline_cachetime']));
        $obj->setVar('headline_display', intval($_POST['headline_display']));
        $obj->setVar('headline_blockmax', intval($_POST['headline_blockmax']));
        $obj->setVar('headline_titlelength', intval($_POST['headline_titlelength']));
        return $hlman->insert($obj);
    }
    
    /** Deletes an item in a rule block
    *
    * @return bool
    */
    function deleteItem() {
        $id = intval($_REQUEST['id']);
        $hlman =& xoops_getmodulehandler('headline', 'obs_classroom');
        $obj =& $hlman->get($id);
        return $hlman->delete($obj);
    }

    /**
    * Performs maintenance deletions following block delete
    *
    * @return bool
    */
    function delete() {
        $sql = sprintf("DELETE FROM %s WHERE headline_blockid = %u", $this->db->prefix('cr_rss'), $this->getVar('blockid'));
		if (!$this->db->query($sql)) {
			return false;
		}
		return true;
    }
    /**
    /* Show rules already present in the block
    *
    * @return string
    */
    function showFeeds() {
        $feeds = $this->getFeeds();
        $ret = "<table>";
        $ret .= "<tr class='head'><td>"._CR_MA_FEEDNAME."</td><td>"._CR_MA_ENCODING."</td><td>"._CR_MA_DISPLAY."</td><td>"._CR_MA_WEIGHT."</td><td>"._CR_MA_EDIT."</td><td>"._CR_MA_DELETE."</td></tr>";
        foreach ($feeds as $key => $feed) {
            $class = isset($class) && $class == "odd" ? "even" : "odd";
            $display = $feed->getVar('headline_display') ? _YES : _NO;
            $ret .= "<tr class='".$class."'>";
            $ret .= "<td>".$feed->getVar('headline_name')."</td>";
            $ret .= "<td>".$feed->getVar('headline_encoding')."</td>";
            $ret .= "<td>".$display."</td>";
            $ret .= "<td>".$feed->getVar('headline_weight')."</td>";
            $ret .= "<td><a href='manage.php?op=editblock&amp;blockid=".$this->getVar('blockid')."&amp;feedid=".$feed->getVar('headline_id')."'>"._CR_MA_EDIT."</a></td>";
            $ret .= "<td><a href='manage.php?op=deleteitem&amp;b=".$this->getVar('blockid')."&amp;id=".$feed->getVar('headline_id')."'>"._CR_MA_DELETE."</a></td>";
            $ret .= "</tr>";
        }
        $ret .= "</table>";
        return $ret;
    }
    
    /**
    * Retrieves single feed
    *
    * @return object
    */
    function getFeed($id) {
        $id = intval($id);
        $headline_handler =& xoops_getmodulehandler('headline', 'obs_classroom');
        return $headline_handler->get($id);
    }
    
    /**
    * Retrieves all feeds
    *
    * @param bool $visible_only whether to only fetch visible feeds
    *
    * @return object
    */
    function getFeeds($visible_only = false) {
        $criteria = new CriteriaCompo(new Criteria('headline_blockid', $this->getVar('blockid')));
        if (false != $visible_only){
            $criteria->add(new Criteria('headline_display', 1));
        }
        $hlman =& xoops_getmodulehandler('headline', 'obs_classroom');
        return $hlman->getObjects($criteria);
    }
    
}
?>
