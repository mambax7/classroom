<?php 
include 'mainfile.php';
echo str_replace(XOOPS_URL, '', $GLOBALS['xoopsRequestUri']);
?>