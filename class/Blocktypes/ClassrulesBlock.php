<?php

namespace XoopsModules\Classroom\Blocktypes;

use XoopsModules\Classroom\{
    Block,
    Helper
};
/** @var Helper $helper */


/**
 * LinkBlock class
 *
 * @package    modules
 * @subpackage Classroom
 */
class ClassrulesBlock extends Block
{
    public function __construct()
    {
        parent::__construct();
        $this->assignVar('template', 'cr_blocktype_classrules.tpl');
        $this->assignVar('blocktypename', 'Class Rules');
        $this->assignVar('bcachetime', 2592000);
    }

    /**
     * Builds a form for the block
     *
     */
    public function buildForm()
    {
        $myts = \MyTextSanitizer::getInstance();
        require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';

        echo $this->showRules();

        if (isset($_GET['ruleid'])) {
            $rule = $this->getRule($_GET['ruleid']);
        } else {
            $rule = ['value' => '', 'weight' => 0];
        }

        $form = new \XoopsThemeForm('', 'ruleform', 'manage.php');
        $form->addElement(new \XoopsFormDhtmlTextArea(_CR_MA_RULE, 'value', htmlspecialchars($rule['value']), 10, 40), true);
        $form->addElement(new \XoopsFormText(_CR_MA_WEIGHT, 'weight', 5, 5, (int)$rule['weight']));
        if (isset($rule['value'])) {
            $form->addElement(new \XoopsFormHidden('ruleid', $rule['value']));
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
        $block['rules'] = $this->getRules();

        return $block;
    }

    /**
     * Performs actions following buildForm submissal
     *
     */
    public function updateBlock()
    {
        $myts = \MyTextSanitizer::getInstance();
        if (isset($_POST['ruleid']) && $_POST['ruleid'] > 0) {
            $sql = 'UPDATE ' . $this->table . '
                    SET weight=' . (int)$_POST['weight'] . ",
                        value='" . $myts->addSlashes($_POST['value']) . "'
                    WHERE fieldid=" . (int)$_POST['ruleid'];
        } else {
            $sql = 'INSERT INTO ' . $this->table . '
                    (blockid, value, weight)
                    VALUES
                    (' . (int)$_POST['blockid'] . ", '" . $myts->addSlashes($_POST['value']) . "', " . (int)$_POST['weight'] . ')';
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
    public function deleteItem()
    {
        $id  = (int)$_REQUEST['id'];
        $sql = 'DELETE FROM ' . $this->table . ' WHERE blockid=' . $this->getVar('blockid') . ' AND fieldid=' . $id;
        if ($this->db->queryF($sql)) {
            return true;
        }

        return false;
    }

    /**
     * /* Show rules already present in the block
     *
     * @return string
     */
    public function showRules()
    {
        $rules = $this->getRules();
        $ret   = '<table>';
        $ret   .= "<tr class='head'><td>" . _CR_MA_RULE . '</td><td>' . _CR_MA_WEIGHT . '</td><td>' . _CR_MA_EDIT . '</td><td>' . _CR_MA_DELETE . '</td></tr>';
        foreach ($rules as $ruleid => $row) {
            $class = isset($class) && 'odd' == $class ? 'even' : 'odd';
            $ret   .= "<tr class='" . $class . "'>";
            $ret   .= '<td>' . $row['value'] . '</td><td>' . $row['weight'] . '</td>';
            $ret   .= "<td><a href='manage.php?op=editblock&amp;blockid=" . $this->getVar('blockid') . '&amp;ruleid=' . $ruleid . "'>" . _CR_MA_EDIT . '</a></td>';
            $ret   .= "<td><a href='manage.php?op=deleteitem&amp;b=" . $this->getVar('blockid') . '&amp;id=' . $ruleid . "'>" . _CR_MA_DELETE . '</a></td>';
            $ret   .= '</tr>';
        }
        $ret .= '</table>';

        return $ret;
    }

    /**
     * retreive rules from database
     *
     * @param int $id rule id
     *
     * @return array
     */
    public function getRule($id)
    {
        $id     = (int)$id;
        $sql    = 'SELECT * FROM ' . $this->table . ' WHERE fieldid=' . $id;
        $result = $this->db->query($sql);
        $row    = $this->db->fetchArray($result);

        return $row;
    }

    /**
     * Retrieves all rules in the block
     *
     * @return array
     */
    public function getRules()
    {
        $ret    = [];
        $myts   = \MyTextSanitizer::getInstance();
        $sql    = 'SELECT * FROM ' . $this->table . ' WHERE blockid=' . $this->getVar('blockid') . ' ORDER BY weight';
        $result = $this->db->query($sql);
        while (false !== ($row = $this->db->fetchArray($result))) {
            $ret[$row['fieldid']] = [
                'ruleid' => $row['fieldid'],
                'value'  => $myts->displayTarea($row['value'], 1),
                'weight' => $row['weight'],
            ];
        }

        return $ret;
    }
}
