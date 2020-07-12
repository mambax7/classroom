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

class ClassrulesBlock extends ClassroomBlock {
    
    function ClassrulesBlock() {
        $this->ClassroomBlock();
        $this->assignVar('template', 'cr_blocktype_classrules.html');
        $this->assignVar('blocktypename', 'Class Rules');
        $this->assignVar('bcachetime', 2592000);
    }
    
    /**
    * Builds a form for the block
    *
    */
    function buildForm() {
        $myts =& MyTextSanitizer::getInstance();
        include_once XOOPS_ROOT_PATH."/class/xoopsformloader.php";
        
        echo $this->showRules();
        
        if (isset($_GET['ruleid'])) {
            $rule = $this->getRule($_GET['ruleid']);
        }
        else {
            $rule = array('value' => '', 'weight' => 0);
        }
        
        $form = new XoopsThemeForm('', 'ruleform', 'manage.php');
        $form->addElement(new XoopsFormDhtmlTextArea(_CR_MA_RULE, 'value', $myts->htmlSpecialChars($rule['value']), 10, 40), true);
        $form->addElement(new XoopsFormText(_CR_MA_WEIGHT, 'weight', 5, 5, intval($rule['weight'])));
        if (isset($rule['value'])) {
            $form->addElement(new XoopsFormHidden('ruleid', $rule['value']));
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
        $block['rules'] = $this->getRules();
        return $block;
    }
    
    /**
    * Performs actions following buildForm submissal
    *
    */
    function updateBlock() {
        $myts = MyTextSanitizer::getInstance();
        if (isset($_POST['ruleid']) && $_POST['ruleid'] > 0) {
            $sql = "UPDATE ".$this->table." 
                    SET weight=".intval($_POST['weight']).",
                        value='".$myts->addSlashes($_POST['value'])."'
                    WHERE fieldid=".intval($_POST['ruleid']);
        }
        else {
            $sql = "INSERT INTO ".$this->table." 
                    (blockid, value, weight)
                    VALUES
                    (".intval($_POST['blockid']).", '".$myts->addSlashes($_POST['value'])."', ".intval($_POST['weight']).")";
        }
        if (!$this->db->query($sql)) {
            return false;
        }
        return true;
    }
    
    /** Deletes an item in a rule block
    * 
    * @return bool
    */
    function deleteItem() {
        $id = intval($_REQUEST['id']);
        $sql = "DELETE FROM ".$this->table." WHERE blockid=".$this->getVar('blockid')." AND fieldid=".$id;
        if ($this->db->queryF($sql)) {
            return true;
        }
        return false;
    }
    
    /**
    /* Show rules already present in the block
    *
    * @return string
    */
    function showRules() {
        $rules = $this->getRules();
        $ret = "<table>";
        $ret .= "<tr class='head'><td>"._CR_MA_RULE."</td><td>"._CR_MA_WEIGHT."</td><td>"._CR_MA_EDIT."</td><td>"._CR_MA_DELETE."</td></tr>";
        foreach ($rules as $ruleid => $row) {
            $class = isset($class) && $class == "odd" ? "even" : "odd";
            $ret .= "<tr class='".$class."'>";
            $ret .= "<td>".$row['value']."</td><td>".$row['weight']."</td>";
            $ret .= "<td><a href='manage.php?op=editblock&amp;blockid=".$this->getVar('blockid')."&amp;ruleid=".$ruleid."'>"._CR_MA_EDIT."</a></td>";
            $ret .= "<td><a href='manage.php?op=deleteitem&amp;b=".$this->getVar('blockid')."&amp;id=".$ruleid."'>"._CR_MA_DELETE."</a></td>";
            $ret .= "</tr>";
        }
        $ret .= "</table>";
        return $ret;
    }
    
    /**
    * retreive rules from database
    *
    * @param int $id rule id
    *
    * @return array
    */
    function getRule($id) {
        $id = intval($id);
        $sql = "SELECT * FROM ".$this->table." WHERE fieldid=".$id;
        $result = $this->db->query($sql);
        $row = $this->db->fetchArray($result);
        return $row;
    }
    
    /**
    * Retrieves all rules in the block
    *
    * @return array
    */
    function getRules() {
        $ret = array();
        $myts =& MyTextSanitizer::getInstance();
        $sql = "SELECT * FROM ".$this->table." WHERE blockid=".$this->getVar('blockid')." ORDER BY weight";
        $result = $this->db->query($sql);
        while ($row = $this->db->fetchArray($result)) {
            $ret[$row['fieldid']] = array('ruleid' => $row['fieldid'], 
                                         'value' => $myts->displayTarea($row['value'], 1),
                                         'weight' => $row['weight']);
        }
        return $ret;
    }
}
?>
