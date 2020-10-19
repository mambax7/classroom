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
 * QuizBlock class
 *
 * @package    modules
 * @subpackage Classroom
 */
class QuizBlock extends Block
{
    public function __construct()
    {
        parent::__construct();
        $this->assignVar('template', 'cr_blocktype_quiz.tpl');
        $this->assignVar('blocktypename', 'Quiz');
        $this->assignVar('bcachetime', 2592000);
    }

    /**
     * Builds a form for the block
     *
     */
    public function buildForm()
    {
        require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';

        echo $this->showQuestions();

        $helper          = Helper::getInstance();
        $questionHandler = $helper->getHandler('Question');
        if (isset($_GET['id'])) {
            $question =& $questionHandler->get($_GET['id']);
        } else {
            $question = $questionHandler->create();
        }

        $form = new \XoopsThemeForm('', 'quizform', 'manage.php');

        $form->addElement(new \XoopsFormText(_CR_MA_QUESTION, 'question', 40, 50, $question->getVar('question')));
        $form->addElement(new \XoopsFormText(_CR_MA_OPTIONA, 'optiona', 40, 50, $question->getVar('optiona')));
        $form->addElement(new \XoopsFormText(_CR_MA_OPTIONB, 'optionb', 40, 50, $question->getVar('optionb')));
        $form->addElement(new \XoopsFormText(_CR_MA_OPTIONC, 'optionc', 40, 50, $question->getVar('optionc')));
        $form->addElement(new \XoopsFormText(_CR_MA_OPTIOND, 'optiond', 40, 50, $question->getVar('optiond')));

        $correct_select = new \XoopsFormSelect(_CR_MA_CORRECT, 'correct', $question->getVar('correct'));
        $correct_select->addOption('optiona', _CR_MA_OPTIONA);
        $correct_select->addOption('optionb', _CR_MA_OPTIONB);
        $correct_select->addOption('optionc', _CR_MA_OPTIONC);
        $correct_select->addOption('optiond', _CR_MA_OPTIOND);
        $form->addElement($correct_select);
        $form->addElement(new \XoopsFormText(_CR_MA_WEIGHT, 'weight', 40, 50, $question->getVar('weight')));
        if (isset($_GET['id']) && $_GET['id'] > 0) {
            $form->addElement(new \XoopsFormHidden('questionid', $_GET['id']));
        }
        $form->addElement(new \XoopsFormHidden('op', 'editblock'));
        $form->addElement(new \XoopsFormHidden('blockid', $this->getVar('blockid')));
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
        $helper          = Helper::getInstance();
        $questionHandler = $helper->getHandler('Question');
        //Fetch all questions in this block
        $criteria = new \Criteria('blockid', $this->getVar('blockid'));
        $criteria->setSort('weight');
        $questions = $questionHandler->getObjects($criteria);

        require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';
        $form = new \XoopsThemeForm('', 'quizform', 'interact.php');
        $form->setExtra('target="_blank"');
        foreach ($questions as $questionid => $question) {
            $quest = new \XoopsFormRadio($question->getVar('question'), 'answer[' . $questionid . ']');
            $quest->addOption('optiona', $question->getVar('optiona'));
            $quest->addOption('optionb', $question->getVar('optionb'));
            $quest->addOption('optionc', $question->getVar('optionc'));
            $quest->addOption('optiond', $question->getVar('optiond'));
            $form->addElement($quest);
            unset($quest);
        }
        $form->addElement(new \XoopsFormHidden('blockid', $this->getVar('blockid')));

        $submit_btn = new \XoopsFormButton('', 'grade', _CR_MA_GRADE, 'submit');
        //$submit_btn->setExtra("onclick='javascript:openWithSelfMain(\"interact.php?op=grade\", \"results\", 625, 380);'");

        $answers_btn = new \XoopsFormButton('', 'showanswers', _CR_MA_ANSWERS, 'submit');
        //$answers_btn->setExtra("onclick='javascript:openWithSelfMain(\"interact.php?op=answers\", \"results\", 625, 380);'");

        $button_tray = new \XoopsFormElementTray('', null, 'button_tray');
        $button_tray->addElement($submit_btn);
        $button_tray->addElement($answers_btn);
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
     */
    public function updateBlock()
    {
        $questionHandler = $helper->getHandler('Question');
        $obj             =& $questionHandler->updateInsert();
        $errors          = $obj->getErrors();
        if (count($errors) > 0) {
            foreach ($errors as $errorstring) {
                $this->setErrors($errorstring);
            }

            return false;
        }

        return true;
    }

    /**
     * Performs actions following user interaction in the displayed block
     *
     */
    public function interact()
    {
        global $xoopsTpl;
        $helper          = Helper::getInstance();
        $answers         = $_POST['answer'] ?? [];
        $correct         = 0;
        $questionHandler = $helper->getHandler('Question');
        //Fetch all questions in this block
        $criteria = new \Criteria('blockid', $this->getVar('blockid'));
        $criteria->setSort('weight');
        $questions = $questionHandler->getObjects($criteria);
        if (isset($_POST['showanswers'])) {
            $xoopsTpl->assign('show_answer', 1);
        }
        foreach ($questions as $questionid => $question) {
            $results[$questionid]['question']      = $question->getVar('question');
            $results[$questionid]['answer']        = isset($answers[$questionid]) ? $question->getVar($answers[$questionid]) : _CR_MA_NOANSWER;
            $results[$questionid]['correctanswer'] = $question->getVar($question->getVar('correct'));
            if (isset($answers[$questionid]) && $question->getVar('correct') == $answers[$questionid]) {
                $results[$questionid]['correct'] = 1;
                $correct++;
            } elseif (isset($answers[$questionid])) {
                $results[$questionid]['correct'] = 0;
            } else {
                $results[$questionid]['correct'] = -1;
            }
        }
        $xoopsTpl->assign('results', $results);
        $xoopsTpl->assign('correct', $correct);
        $xoopsTpl->assign('questionno', count($questions));
        $xoopsTpl->assign('percentage', round($correct / count($questions) * 100), 2);
        $xoopsTpl->display('db:cr_interact_quiz.tpl');
        exit();
    }

    /**
     * Deletes a single item in this block
     *
     * @return bool
     */
    public function deleteItem()
    {
        $id              = (int)$_REQUEST['id'];
        $questionHandler = $helper->getHandler('Question');
        $thisQuestion    =& $questionHandler->get($id);

        return $questionHandler->delete($thisQuestion);
    }

    /**
     * Deletes all block-specific items in block tables prior to block deletion
     * It is not necessary to delete items in the cr_value table as this is done automatically
     *
     * @return bool
     */
    public function delete()
    {
        $questionHandler = $helper->getHandler('Question');
        //Fetch all questions in this block
        $questions = $questionHandler->getObjects(new \Criteria('blockid', $this->getVar('blockid')));

        return $questionHandler->deleteAll(new \Criteria('questionid', '(' . implode(',', array_keys($questions)) . ')', 'IN'));
    }

    /**
     * Shows questions already in this block
     *
     * @return string
     */
    public function showQuestions()
    {
        $ret             = '';
        $questionHandler = $helper->getHandler('Question');
        //Fetch all questions in this block
        $criteria = new \Criteria('blockid', $this->getVar('blockid'));
        $criteria->setSort('weight');
        $questions = $questionHandler->getObjects($criteria);
        if (count($questions) > 0) {
            $ret = '<table>';
            $ret .= "<tr class='head'><td>" . _CR_MA_QUESTION . '</td><td>' . _CR_MA_OPTIONA . '</td><td>' . _CR_MA_OPTIONB . '</td><td>' . _CR_MA_OPTIONC . '</td><td>' . _CR_MA_OPTIOND . '</td><td>' . _CR_MA_WEIGHT . '</td><td>' . _CR_MA_EDIT . '</td><td>' . _CR_MA_DELETE . '</td></tr>';
            foreach ($questions as $questionid => $question) {
                $class = isset($class) && 'odd' == $class ? 'even' : 'odd';
                $ret   .= "<tr class='" . $class . "'><td>" . $question->getVar('question') . '</td>';
                $ret   .= '<td>' . $question->getVar('optiona') . '</td>';
                $ret   .= '<td>' . $question->getVar('optionb') . '</td>';
                $ret   .= '<td>' . $question->getVar('optionc') . '</td>';
                $ret   .= '<td>' . $question->getVar('optiond') . '</td>';
                $ret   .= '<td>' . $question->getVar('weight') . '</td>';
                $ret   .= "<td><a href='manage.php?op=editblock&amp;blockid=" . $this->getVar('blockid') . '&amp;id=' . $questionid . "'>" . _CR_MA_EDIT . '</a></td>';
                $ret   .= "<td><a href='manage.php?op=deleteitem&amp;b=" . $this->getVar('blockid') . '&amp;id=' . $questionid . "'>" . _CR_MA_DELETE . '</a></td>';
                $ret   .= '</tr>';
            }
            $ret .= '</table>';
        }

        return $ret;
    }
}
