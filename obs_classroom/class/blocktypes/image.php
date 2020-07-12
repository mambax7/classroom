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
 * ImageBlock class
 *
 * @package modules
 * @subpackage Classroom
 */

class ImageBlock extends ClassroomBlock {
    
    function ImageBlock() {
        $this->ClassroomBlock();
        $this->assignVar('template', 'cr_blocktype_image.html');
        $this->assignVar('blocktypename', 'Image');
        //One day cache time
        $this->assignVar('bcachetime', 86400);
    }
    
    /**
    * Builds a form for the block
    *
    */
    function buildForm() {
        $myts =& MyTextSanitizer::getInstance();
        include_once XOOPS_ROOT_PATH."/class/xoopsformloader.php";
        
        echo $this->showImages();
        
        if (isset($_GET['imageid'])) {
            $image = $this->getImage($_GET['imageid']);
        }
        else {
            $image = array('image' => '');
        }
        
        $form = new XoopsThemeForm('', 'imageform', 'manage.php');
        $image_input = new XoopsFormText(_CR_MA_IMAGE, 'image', 60, 255, $myts->htmlSpecialChars($image['image']));
        $image_input->setExtra(" /><img align='middle' onmouseover='style.cursor=\"hand\"' onclick='javascript:openWithSelfMain(\"".XOOPS_URL."/imagemanager.php?nocode=1&amp;target=image\",\"imgmanager\",400,430);' src='".XOOPS_URL."/images/image.gif' alt='image' title='image'");
        $form->addElement($image_input, true);
        if (isset($image['fieldid'])) {
            $form->addElement(new XoopsFormHidden('imageid', $image['fieldid']));
        }
        $form->addElement(new XoopsFormHidden('op', 'editblock'));
        $form->addElement(new XoopsFormHidden('blockid', $this->getVar('blockid')));
        $form->addElement(new XoopsFormButton('', 'submit', _CR_MA_SUBMIT, 'submit'));
        $form->display();
        if (isset($image['fieldid'])) {
            $img = @imagecreatefromjpeg($image['image']);
            if ($img) {
                $img_height = imagesy($img) + 10;
                ImageDestroy($img);
            }

            echo '<div>
                    <iframe src="'.$image['image'].'" width="99%" height="'.$img_height.'px" frameborder="0">
                        <a href="'.$image['image'].'">Image</a>
                    </iframe>
                  </div>';
        }
    }
    
    /**
    * Builds a classroomblock
    *
    * @return array
    */
    function buildBlock() {
        $block['image'] = $this->getRandomImage();
        $img = @imagecreatefromjpeg($block['image']['image']);
        if ($img) {
            $block['img_height'] = imagesy($img) + 10;
            ImageDestroy($img);
        }
        return $block;
    }
    
    /**
    * Performs actions following buildForm submissal
    *
    */
    function updateBlock() {
        $myts = MyTextSanitizer::getInstance();
        if (isset($_POST['imageid']) && $_POST['imageid'] > 0) {
            $sql = "UPDATE ".$this->table." 
                    SET value='".$myts->addSlashes($_POST['image'])."',
                        weight=0,
                        updated=".time()."
                    WHERE blockid=".$this->getVar('blockid')." AND fieldid=".intval($_POST['imageid']);
        }
        else {
            $sql = "INSERT INTO ".$this->table." (blockid, weight, value, updated) VALUES
                    (".$this->getVar('blockid').", 0, '".$myts->addSlashes($_POST['image'])."', ".time().")";
        }
        return $this->db->query($sql);
    }
    
    /** Deletes a single item
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
    /* Show items already present in the block
    *
    * @return string
    */
    function showImages() {
        $images = $this->getImages();
        $ret = "<table>";
        $ret .= "<tr class='head'><td>"._CR_MA_IMAGE."</td><td>"._CR_MA_EDIT."</td><td>"._CR_MA_DELETE."</td></tr>";
        foreach ($images as $imageid => $row) {
            $class = isset($class) && $class == "odd" ? "even" : "odd";
            $ret .= "<tr class='".$class."'>";
            $ret .= "<td>".$row['image']."</td>";
            $ret .= "<td><a href='manage.php?op=editblock&amp;blockid=".$this->getVar('blockid')."&amp;imageid=".$imageid."'>"._CR_MA_EDIT."</a></td>";
            $ret .= "<td><a href='manage.php?op=deleteitem&amp;b=".$this->getVar('blockid')."&amp;id=".$imageid."'>"._CR_MA_DELETE."</a></td>";
            $ret .= "</tr>";
        }
        $ret .= "</table>";
        return $ret;
    }
    
    /**
    * retrieve item from database
    *
    * @param int $id imageid
    *
    * @return array
    */
    function getImage($id) {
        $id = intval($id);
        $sql = "SELECT * FROM ".$this->table." WHERE fieldid=".$id;
        $result = $this->db->query($sql);
        $row = $this->db->fetchArray($result);
        $row['image'] = $row['value'];
        return $row;
    }
    
    /**
    * Retrieves all items in a block
    *
    * @return array
    */
    function getImages() {
        $ret = array();
        $myts =& MyTextSanitizer::getInstance();
        $sql = "SELECT * FROM ".$this->table." WHERE blockid=".$this->getVar('blockid')." ORDER BY updated DESC";
        $result = $this->db->query($sql);
        while ($row = $this->db->fetchArray($result)) {
            $ret[$row['fieldid']] = array('imageid' => $row['fieldid'], 
                                         'image' => $myts->htmlSpecialChars($row['value']),
                                         'weight' => $row['weight']);
        }
        return $ret;
    }
    
    /**
    * retrieve random item from database
    *
    * @return array
    */
    function getRandomImage() {
        $myts =& MyTextSanitizer::getInstance();
        $sel_time = time() - (86400 * 2);
        $sql = "SELECT * FROM ".$this->table." WHERE blockid=".$this->getVar('blockid')." AND updated > ".$sel_time;
        $result = $this->db->query($sql);
        $images = array();
        while ($row = $this->db->fetchArray($result)) {
            $images[] = $row;
        }
        $num_images = count($images);
        if ($num_images == 0) {
            $sql = "SELECT * FROM ".$this->table." WHERE blockid=".$this->getVar('blockid')." LIMIT 0,1";
            $result = $this->db->query($sql);
            $images[] = $this->db->fetchArray($result);
        }
        $random_number = rand(0, $num_images-1);
        $row = $images[$random_number];
        //$row['image'] = $myts->displayTarea($row['value']);
        $row['image'] = $row['value'];
        $sql = "UPDATE ".$this->table." SET updated=".time()." WHERE fieldid=".$row['fieldid'];
        $this->db->queryF($sql);
        return $row;
    }
}
?>
