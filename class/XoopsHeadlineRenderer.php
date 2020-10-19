<?php

namespace XoopsModules\Classroom;

// $Id: headlinerenderer.php,v 1.1 2004/01/29 14:45:48 buennagel Exp $
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
// Author: Kazumi Ono (AKA onokazu)                                          //
// URL: http://www.myweb.ne.jp/, https://xoops.org/, http://jp.xoops.org/ //
// Project: The XOOPS Project                                                //
// ------------------------------------------------------------------------- //
require_once XOOPS_ROOT_PATH . '/class/template.php';

/**
 * Class XoopsHeadlineRenderer
 */
class XoopsHeadlineRenderer
{
    // holds reference to xoopsheadline class object
    public $_hl;
    // XoopTemplate object
    public $_tpl;
    public $_feed;
    public $_block;
    public $_errors = [];
    // RSS2 SAX parser
    public $_parser;

    /**
     * XoopsHeadlineRenderer constructor.
     * @param $headline
     */
    public function __construct(&$headline)
    {
        $this->_hl  =& $headline;
        $this->_tpl = new \XoopsTpl();
    }

    /**
     * @return false
     */
    public function updateCache()
    {
        $helper             = Helper::getInstance();
        $error_level_stored = error_reporting();
        error_reporting($error_level_stored & ~E_NOTICE);
        // includes Snoopy class for remote file access
        require_once XOOPS_ROOT_PATH . '/class/snoopy.php';
        $snoopy = new Snoopy();
        //TIMEOUT 5 second
        $snoopy->read_timeout = 5;                    // timeout on read operations, in seconds
        //URL fetch
        if (!$snoopy->fetch($this->_hl->getVar('headline_rssurl')) || !$snoopy->results) {
            if (!empty($snoopy->error)) {
                $this->_setErrors('Could not open file: ' . $this->_hl->getVar('headline_rssurl') . 'snoopy status=' . $snoopy->error);

                return false;
            }

            $this->_setErrors('Could not open file: ' . $this->_hl->getVar('headline_rssurl'));

            return false;
        }
        $data = $snoopy->results;
        error_reporting($error_level_stored);
        $this->_hl->setVar('headline_xml', $this->convertToUtf8($data));
        $this->_hl->setVar('headline_updated', time());
        $headlineHandler = $helper->getHandler('Headline');

        return $headlineHandler->insert($this->_hl);
    }

    /**
     * @param false $force_update
     * @return bool
     */
    public function renderBlock($force_update = false)
    {
        if ($force_update || $this->_hl->cacheExpired()) {
            if (!$this->updateCache()) {
                return false;
            }
        }
        if (!$this->_parse()) {
            return false;
        }
        $this->_tpl->clear_all_assign();
        $this->_tpl->assign('xoops_url', XOOPS_URL);
        $channel_data =& $this->_parser->getChannelData();
        array_walk($channel_data, [$this, 'convertFromUtf8']);
        $this->_tpl->assign_by_ref('channel', $channel_data);
        $image_data =& $this->_parser->getImageData();
        array_walk($image_data, [$this, 'convertFromUtf8']);
        $this->_tpl->assign_by_ref('image', $image_data);
        $items =& $this->_parser->getItems();
        $count = count($items);
        $max   = ($count > $this->_hl->getVar('headline_blockmax')) ? $this->_hl->getVar('headline_blockmax') : $count;
        for ($i = 0; $i < $max; $i++) {
            array_walk($items[$i], [$this, 'convertFromUtf8']);
            $this->_tpl->append_by_ref('items', $items[$i]);
        }
        $this->_tpl->assign(['site_name' => $this->_hl->getVar('headline_name'), 'site_url' => $this->_hl->getVar('headline_url'), 'site_id' => $this->_hl->getVar('headline_id'), 'title_length' => $this->_hl->getVar('headline_titlelength')]);
        $this->_block = $this->_tpl->fetch('file:' . XOOPS_ROOT_PATH . '/modules/classroom/templates/cr_blocktype_rss_file.tpl');

        return true;
    }

    /**
     * @return bool
     */
    public function _parse()
    {
        if (isset($this->_parser)) {
            return true;
        }
        require_once XOOPS_ROOT_PATH . '/class/xml/rss/xmlrss2parser.php';
        $this->_parser = new \XoopsXmlRss2Parser($this->_hl->getVar('headline_xml'));
        switch ($this->_hl->getVar('headline_encoding')) {
            case 'utf-8':
                $this->_parser->useUtfEncoding();
                break;
            case 'us-ascii':
                $this->_parser->useAsciiEncoding();
                break;
            default:
                $this->_parser->useIsoEncoding();
                break;
        }
        $result = $this->_parser->parse();
        if (!$result) {
            $this->_setErrors($this->_parser->getErrors(false));
            unset($this->_parser);

            return false;
        }

        return true;
    }

    public function &getFeed()
    {
        return $this->_feed;
    }

    public function &getBlock()
    {
        return $this->_block;
    }

    /**
     * @param $err
     */
    public function _setErrors($err)
    {
        $this->_errors[] = $err;
    }

    /**
     * @param bool $ashtml
     * @return array|string
     */
    public function &getErrors($ashtml = true)
    {
        if (!$ashtml) {
            return $this->_errors;
        }

        $ret = '';
        if (count($this->_errors) > 0) {
            foreach ($this->_errors as $error) {
                $ret .= $error . '<br>';
            }
        }

        return $ret;
    }

    // abstract
    // overide this method in /language/your_language/headlinerenderer.php
    // this method is called by the array_walk function
    // return void
    /**
     * @param $value
     * @param $key
     */
    public function convertFromUtf8(&$value, $key)
    {
    }

    // abstract
    // overide this method in /language/your_language/headlinerenderer.php
    // return string
    /**
     * @param $xmlfile
     * @return mixed
     */
    public function &convertToUtf8(&$xmlfile)
    {
        return $xmlfile;
    }
}
