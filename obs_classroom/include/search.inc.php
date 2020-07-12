<?php
// $Id: search.inc.php,v 1.2 2004/01/29 17:15:54 mithyt2 Exp $
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

function cr_search($queryarray, $andor, $limit, $offset, $userid=0, $storyid = false){
    global $xoopsDB;
    
    if ($userid > 0) {
        $criteria = new Criteria('owner', intval($userid));
        $criteria->setLimit($limit);
        $criteria->setStart($offset);
        $classroom_handler =& xoops_getmodulehandler('classroom', 'obs_classroom');
        $classrooms = $classroom_handler->getObjects($criteria);
        $ret = array();
        foreach ($classrooms as $i => $classroom) {
            $ret[$i]['image'] = "images/cr_slogo.png";
            $ret[$i]['link'] = "classroom.php?cr=".$classroom->getVar('classroomid');
            $ret[$i]['title'] = $classroom->getVar('name');
            $ret[$i]['uid'] = $classroom->getVar('owner');
            $ret[$i]['id'] = $classroom->getVar('classroomid');
        }
    }
    else {
        $sql = "SELECT 
                   DISTINCT b.name AS blockname, b.blockid, cr.owner, cr.name as crname
                FROM
                    ".$xoopsDB->prefix('cr_block')." b, ".$xoopsDB->prefix('cr_classroom')." cr, ".$xoopsDB->prefix('cr_value')." v, ".$xoopsDB->prefix('cr_classblock')." cb 
                WHERE
                    b.classroomid = cr.classroomid AND b.blockid=v.blockid AND b.blockid=cb.blockid AND cb.visible=1";
        // because count() returns 1 even if a supplied variable
        // is not an array, we must check if $querryarray is really an array
        if ( is_array($queryarray) && $count = count($queryarray) ) {
            $sql .= " AND ((v.value LIKE '%$queryarray[0]%')";
            for($i=1;$i<$count;$i++){
                $sql .= " $andor ";
                $sql .= "(v.value LIKE '%$queryarray[$i]%')";
            }
            $sql .= ")";
        }
        $sql .= " ORDER BY b.name ASC";
        $result = $xoopsDB->query($sql,$limit,$offset);
        $ret = array();
        $i = 0;
        while($myrow = $xoopsDB->fetchArray($result)){
            $ret[$i]['image'] = "images/cr_slogo.png";
            $ret[$i]['link'] = "block.php?b=".$myrow['blockid']."";
            $ret[$i]['title'] = $myrow['crname']."::".$myrow['blockname'];
            $ret[$i]['uid'] = $myrow['owner'];
            $ret[$i]['id'] = $myrow['blockid'];
            $i++;
        }
    }
    return $ret;
}
?>