<?php

require __DIR__ . '/mainfile.php';
echo str_replace(XOOPS_URL, '', $GLOBALS['xoopsRequestUri']);
