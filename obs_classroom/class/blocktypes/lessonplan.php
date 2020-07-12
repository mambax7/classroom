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
 * LessonplanBlocktype class
 *
 * @package modules
 * @subpackage Classroom
 */

class LessonplanBlock extends ClassroomBlock {

    var $plantable;
    var $blocktable;
    
    function LessonplanBlock() {
        $this->ClassroomBlock();
        $this->plantable = $this->db->prefix('cr_lessonplan');
        $this->blocktable = $this->db->prefix('cr_lessonplanblock');
        $this->assignVar('template', 'cr_blocktype_lessonplan.html');
        $this->assignVar('blocktypename', 'Lessonplan');
        $this->assignVar('bcachetime', 2592000);
    }
    
    /**
    * Builds a form for the block
    *
    */
    function buildForm() {
        $day = 1;
        $text = "";
        $weight = 0;
        $date = time();
        
        $day_array = $this->getDaysOfWeek();
        
        $myts = MyTextSanitizer::getInstance();
        $currententries = "<table>";
        $entries = $this->getItems();
        if (count($entries) > 0) {
            foreach ($entries as $thisday => $dayentries) {
                $class = "";
                $currententries .= "<tr><th>".$day_array[$thisday]."</th><th>"._CR_MA_WEIGHT."</th><th>"._CR_MA_EDIT."</th><th>"._CR_MA_DELETE."</th></tr>";
                foreach ($dayentries as $id => $entry) {
                    $class = isset($class) && $class == "odd" ? "even" : "odd";
                    $currententries .= "<tr class='".$class."'><td>".$myts->htmlSpecialChars($entry['value'])."</td>
                                                                <td>".$entry['weight']."</td>
                                                                <td><a href='manage.php?op=editblock&amp;blockid=".$this->getVar('blockid')."&amp;entryid=".$id."'>"._CR_MA_EDIT."</a></td>
                                                                <td><a href='manage.php?op=deleteitem&amp;b=".$this->getVar('blockid')."&amp;id=".$id."'>"._CR_MA_DELETE."</a></td></tr>";
                    if (isset($_GET['entryid']) && $id == $_GET['entryid']) {
                        $day = $thisday;
                        $text = $entry['value'];
                        $weight = $entry['weight'];
                    }
                    $date = $entry['date'];
                }
            }
        }
        $currententries .= "</table>";
        
        include_once XOOPS_ROOT_PATH."/class/xoopsformloader.php";
        $form = new XoopsThemeForm('', 'lessonplanform', 'manage.php');
        $form->addElement(new XoopsFormTextDateSelect(_CR_MA_WEEKOF, 'date', 15, $date));
        if (count($entries) > 0) {
            $form->addElement(new XoopsFormLabel(_CR_MA_CURRENTENTRIES, $currententries));
        }
        
        $day_select = new XoopsFormSelect('', 'day', $day);
        $day_select->addOptionArray($day_array);
        
        $tray = new XoopsFormElementTray(_CR_MA_TEXT);
        $tray->addElement(new XoopsFormText('', 'text', 40, 60, $text), true);
        $tray->addElement($day_select);
        $tray->addElement(new XoopsFormText(_CR_MA_WEIGHT, 'weight', 5, 5, $weight));
        
        $form->addElement($tray);
        
        if (isset($_GET['entryid']) && $_GET['entryid'] > 0) {
            $form->addElement(new XoopsFormHidden('entryid', $_GET['entryid']));
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
        $myts = MyTextSanitizer::getInstance();
        $block = array();
        $days = $this->getDaysOfWeek();
        $entries = $this->getItems();
        foreach ($days as $day => $name) {
            $block['days'][$day]['name'] = $name;
            if (isset($entries[$day]) && count($entries[$day]) > 0) {
                foreach ($entries[$day] as $id => $entry) {
                    $block['days'][$day]['entries'][$id] = $myts->htmlSpecialChars($entry['value']);
                    if (!isset($date)) {
                        $date = $entry['date'];
                    }
                }
            }
        }
        if (isset($date)) {
            $dayofweek = date('w', $date);
            if ($dayofweek != 1) {
                if ($dayofweek == 0) {
                    $date = $date - (6 * 24 * 60 * 60);
                }
                else {
                    $date = $date - (($dayofweek-1) * 24 * 60 * 60);
                }
            }
            $block['monday'] = date('F j', $date);
            $block['tuesday'] = date('F j', $date+(1*24*60*60));
            $block['wednesday'] = date('F j', $date+(2*24*60*60));
            $block['thursday'] = date('F j', $date+(3*24*60*60));
            $block['friday'] = date('F j', $date+(4*24*60*60));
        }
        return $block;
    }
    
    /**
    * Performs actions following buildForm submissal
    *
    */
    function updateBlock() {
        $myts = MyTextSanitizer::getInstance();
        $sql = "SELECT * FROM ".$this->blocktable." WHERE blockid=".$this->getVar('blockid');
        $result = $this->db->query($sql);
        $block = $this->db->fetchArray($result);
        if ($block) {
            $sql = "UPDATE ".$this->blocktable." SET date=".strtotime($_POST['date'])." WHERE blockid=".$this->getVar('blockid');
            if (!$this->db->query($sql)) {
                return false;
            }
        }
        else {
            $sql = "INSERT INTO ".$this->blocktable." SET date=".strtotime($_POST['date']).", blockid=".$this->getVar('blockid');
            if (!$this->db->query($sql)) {
                return false;
            }
        }
        if (isset($_POST['entryid']) && $_POST['entryid'] > 0) {
            if ($_POST['text'] != "") {
                $sql = "UPDATE ".$this->table." SET value='".$myts->addSlashes($_POST['text'])."', weight=".intval($_POST['weight'])." WHERE blockid=".$this->getVar('blockid')." AND fieldid=".intval($_POST['entryid']);
                $this->db->query($sql);
                $sql = "UPDATE ".$this->plantable." SET day=".intval($_POST['day'])." WHERE entryid=".intval($_POST['entryid']);
                $this->db->query($sql);
            }
            else {
                xoops_error('No text input');
            }
        }
        else {
            if ($_POST['text'] != "") {
                $sql = "INSERT INTO ".$this->table." (blockid, value, weight) VALUES (".intval($this->getVar('blockid')).", '".$myts->addSlashes($_POST['text'])."', ".intval($_POST['weight']).")";
                $this->db->query($sql);
                $entryid = $this->db->getInsertId();
                $sql = "INSERT INTO ".$this->plantable." (entryid, blockid, day) VALUES (".$entryid.", ".intval($this->getVar('blockid')).", ".intval($_POST['day']).")";
                $this->db->query($sql);
            }
            else {
                xoops_error('No text input');
            }
        }
        return true;                
    }
    
    /** Deletes an item in a lesson plan
    * 
    * @return bool
    */
    function deleteItem() {
        $id = intval($_REQUEST['id']);
        $sql = "DELETE FROM ".$this->table." WHERE blockid=".$this->getVar('blockid')." AND fieldid=".$id;
        if ($this->db->queryF($sql)) {
            $sql = "DELETE FROM ".$this->plantable." WHERE blockid=".$this->getVar('blockid')." AND entryid=".$id;
            if ($this->db->queryF($sql)) {
                return true;
            }
        }
        return false;
    }
    
    /** Deletes all block-specific items prior to block deletion
    * 
    * @return bool
    */
    function delete() {
        $sql = "DELETE FROM ".$this->plantable." WHERE blockid=".$this->getVar('blockid');
        if ($this->db->query($sql)) {
            $sql = "DELETE FROM ".$this->blocktable." WHERE blockid=".$this->getVar('blockid');
            return $this->db->query($sql);
        }
        return false;
    }
    
    /**
    * Retrieves all lesson plan items for a block
    *
    * @staticvar array entries ordered by day
    * @return array
    */
    function getItems() {
        $ret = array();
        $sql = "SELECT * FROM ".$this->table." v, ".$this->plantable." p, ".$this->blocktable." b WHERE p.blockid = v.blockid AND p.blockid=b.blockid AND p.entryid = v.fieldid AND p.blockid=".$this->getVar('blockid')." ORDER BY p.day, v.weight";
        $result = $this->db->query($sql);
        while ($row = $this->db->fetchArray($result)) {
            $ret[$row['day']][$row['fieldid']] = $row;
        }
        return $ret;
    }
    
    function getDaysOfWeek() {
        return array(1 => _CR_MA_MONDAY,
                     2 => _CR_MA_TUESDAY,
                     3 => _CR_MA_WEDNESDAY,
                     4 => _CR_MA_THURSDAY,
                     5 => _CR_MA_FRIDAY);
    }
}
?>