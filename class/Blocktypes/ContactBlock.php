<?php

namespace XoopsModules\Classroom\Blocktypes;

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

use XoopsModules\Classroom\{Block,
    Helper
};

/** @var Helper $helper */

/**
 * TextfieldBlocktype class
 *
 * @package    modules
 * @subpackage Classroom
 */
class ContactBlock extends Block
{
    public function __construct()
    {
        parent::__construct();
        $this->assignVar('template', 'cr_blocktype_contact.tpl');
        $this->assignVar('blocktypename', 'Contact');
        $this->assignVar('bcachetime', 0);
    }

    /**
     * Builds a form for the block
     *
     */
    public function buildForm()
    {
        global $xoopsConfig;
        require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';
        $form = new \XoopsThemeForm('', 'contactform', 'manage.php');

        $value   = $this->getValue();
        $is_edit = ('' != $value) ? 1 : 0;

        if (!$is_edit) {
            $form->addElement(new \XoopsFormLabel(_CR_MA_NOMETHODSET, _CR_MA_PLEASESETMETHOD));
        }
        require_once XOOPS_ROOT_PATH . '/language/' . $xoopsConfig['language'] . '/notification.php';
        require_once XOOPS_ROOT_PATH . '/include/notification_constants.php';
        $notify_method_select = new \XoopsFormSelect(_CR_MA_CONTACTMETHOD, 'method', $value);
        $notify_method_select->addOptionArray([XOOPS_NOTIFICATION_METHOD_PM => _NOT_METHOD_PM, XOOPS_NOTIFICATION_METHOD_EMAIL => _NOT_METHOD_EMAIL]);
        $form->addElement($notify_method_select);

        $form->addElement(new \XoopsFormHidden('op', 'editblock'));
        $form->addElement(new \XoopsFormHidden('blockid', $this->getVar('blockid')));
        $form->addElement(new \XoopsFormHidden('edit', $is_edit));
        $form->addElement(new \XoopsFormButton('', 'submit', _CR_MA_SUBMIT, 'submit'));
        $form->display();
    }

    /**
     * Builds a classroomblock
     *
     * @return array
     */
    public function buildBlock()
    {
        global $xoopsUser;

        $helper           = Helper::getInstance();
        $classroomHandler = $helper->getHandler('Classroom');
        $classroom        = $classroomHandler->get($this->getVar('classroomid'));
        $teacherid        = $classroom->getVar('owner');

        if ($xoopsUser) {
            $visitor = ['name' => $xoopsUser->getVar('name'), 'email' => $xoopsUser->getVar('email')];
        } else {
            $visitor = ['name' => '', 'email' => ''];
        }
        require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';
        $form = new \XoopsThemeForm('', 'contactform', 'interact.php');

        $form->addElement(new \XoopsFormHidden('blockid', $this->getVar('blockid')));
        $form->addElement(new \XoopsFormText(_CR_MA_NAME, 'name', 30, 70, $visitor['name']), true);
        $form->addElement(new \XoopsFormText(_CR_MA_EMAIL, 'email', 40, 90, $visitor['email']), true);
        $form->addElement(new \XoopsFormDhtmlTextArea(_CR_MA_MESSAGE, 'text', '', 12, 35), true);
        $submit_btn = new \XoopsFormButton('', 'submit', _CR_MA_SUBMIT, 'submit');

        $button_tray = new \XoopsFormElementTray('', null, 'button_tray');
        $button_tray->addElement($submit_btn);
        $form->addElement($button_tray);

        foreach ($form->getElements() as $ele) {
            $n                       = $ele->getName();
            $elements[$n]['name']    = $ele->getName();
            $elements[$n]['caption'] = $ele->getCaption();
            $elements[$n]['body']    = $ele->render();
            $elements[$n]['hidden']  = $ele->isHidden();
            $elements[$n]['extra']   = $ele->getExtra();
        }
        $js                      = $form->renderValidationJS();
        $block[$form->getName()] = [
            'title'      => $form->getTitle(),
            'name'       => $form->getName(),
            'action'     => $form->getAction(),
            'method'     => $form->getMethod(),
            'extra'      => 'onsubmit="return xoopsFormValidate_' . $form->getName() . '();"' . $form->getExtra(),
            'javascript' => $js,
            'elements'   => $elements,
        ];

        return $block;
    }

    /**
     * Performs actions following buildForm submissal
     *
     * @return bool
     */
    public function updateBlock()
    {
        if (1 == $_POST['edit']) {
            $sql  = 'UPDATE ';
            $sql2 = 'WHERE blockid=' . $this->getVar('blockid');
        } else {
            $sql  = 'INSERT INTO ';
            $sql2 = ', blockid=' . $this->getVar('blockid');
        }
        $sql .= $this->table . " SET value='" . (int)$_POST['method'] . "'";
        $sql .= $sql2;
        if ($this->db->query($sql)) {
            return true;
        }

        return false;
    }

    /**
     * Retrieves the value of the one field used in cr_value for this block
     *
     * @return string
     */
    public function getValue()
    {
        $sql = 'SELECT * FROM ' . $this->table . ' WHERE blockid=' . $this->getVar('blockid');
        if (!$result = $this->db->query($sql, 1, 0)) {
            return '';
        }

        $row = $this->db->fetchArray($result);

        return $row['value'];
    }

    /**
     * Performs actions following user interaction in the displayed block
     *
     */
    public function interact()
    {
        global $xoopsTpl, $xoopsUser, $xoopsConfig, $xoopsOption;
        $myts = \MyTextSanitizer::getInstance();
        $helper = Helper::getInstance();

        $classroomHandler = $helper->getHandler('Classroom');
        $classroom        =& $classroomHandler->get($this->getVar('classroomid'));
        $teacherid        = $classroom->getVar('owner');

        $memberHandler = xoops_getHandler('member');
        $teacher       = $memberHandler->getUser($teacherid);

        $value = $this->getValue();
        if ('' == $value) {
            $value = $teacher->getVar('notify_method');
        }

        $xoopsMailer = getMailer();
        require_once XOOPS_ROOT_PATH . '/include/notification_constants.php';
        switch ($value) {
            case XOOPS_NOTIFICATION_METHOD_PM:
                $xoopsMailer->usePM();
                if ($xoopsUser) {
                    $xoopsMailer->setFromUser($xoopsUser);
                } else {
                    $configHandler     = xoops_getHandler('config');
                    $xoopsMailerConfig = $configHandler->getConfigsByCat(XOOPS_CONF_MAILER);
                    $xoopsMailer->setFromUser($memberHandler->getUser($xoopsMailerConfig['fromuid']));
                }
                break;

            default:
            case XOOPS_NOTIFICATION_METHOD_EMAIL:
                $xoopsMailer->useMail();
                $xoopsMailer->setFromEmail($_POST['email']);
                $xoopsMailer->setFromName($_POST['name']);
                break;
        }

        // Set up the mailer
        $xoopsMailer->setTemplateDir(XOOPS_ROOT_PATH . '/modules/classroom/language/' . $xoopsConfig['language'] . '/mail_template/');
        $xoopsMailer->setTemplate('contact.tpl');
        $xoopsMailer->setToUsers($teacher);
        $xoopsMailer->setSubject(_CR_MA_CONTACTFORMSUBJECT);
        $xoopsMailer->assign('NAME', $_POST['name']);
        $xoopsMailer->assign('EMAIL', $_POST['email']);
        $xoopsMailer->assign('TEXT', $myts->displayTarea($_POST['text']));
        $success = $xoopsMailer->send(true);
        if ($success) {
            $xoopsTpl->assign('block', ['text' => '<a href="classroom.php?cr=' . $this->getVar('classroomid') . '">' . _CR_MA_BACKTOCLASSROOM . '</a><br>' . $xoopsMailer->getErrors() . '<br>' . $xoopsMailer->getSuccess()]);
        } else {
            $xoopsTpl->assign('block', ['text' => '<a href="classroom.php?cr=' . $this->getVar('classroomid') . '">' . _CR_MA_BACKTOCLASSROOM . '</a><br>' . $xoopsMailer->getErrors() . '<br>' . $xoopsMailer->getSuccess()]);
        }
        $GLOBALS['xoopsOption']['template_main'] = 'cr_blocktype_textfield.tpl';
    }
}
