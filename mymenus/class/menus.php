<?php
/*
 You may not change or alter any portion of this comment or credits
 of supporting developers from this source code or any supporting source code
 which is considered copyrighted (c) material of the original comment or credit authors.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

/**
 * @copyright       The XOOPS Project http://sourceforge.net/projects/xoops/
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU Public License
 * @package         Mymenus
 * @since           1.0
 * @author          trabis <lusopoemas@gmail.com>
 * @version         $Id: menus.php 0 2010-07-21 18:47:04Z trabis $
 */

defined('XOOPS_ROOT_PATH') || die('XOOPS root path not defined');
include_once dirname(__DIR__) . '/include/common.php';

/**
 * Class MymenusMenus
 */
class MymenusMenus extends XoopsObject
{
    /**
     * @var Module_skeletonModule_skeleton
     * @access private
     */
    private $mymenus = null;

    /**
     * constructor
     */
    function __construct()
    {
        $this->mymenus = MymenusMymenus::getInstance();
        $this->db = XoopsDatabaseFactory::getDatabaseConnection();
        $this->initVar("id", XOBJ_DTYPE_INT);
        $this->initVar('title', XOBJ_DTYPE_TXTBOX);
        //
        $this->initVar('css', XOBJ_DTYPE_TXTBOX);
        //
    }

    /**
     * Get {@link XoopsThemeForm} for adding/editing items
     *
     * @param bool          $action
     * @return object       {@link XoopsThemeForm}
     */
    public function getForm($action = false)
    {
        global $xoopsUser;
        $groupperm_handler = xoops_gethandler('groupperm');
        //
        xoops_load('XoopsFormLoader');
        //
        if ($action === false) {
            $action = $_SERVER['REQUEST_URI'];
        }
        //
        $isAdmin = mymenus_userIsAdmin();
        $groups = is_object($xoopsUser) ? $xoopsUser->getGroups() : array(0 => XOOPS_GROUP_ANONYMOUS);
        //
        $title = $this->isNew() ? _AM_MYMENUS_MENUS_ADD : _AM_MYMENUS_MENUS_EDIT;
        //
        $form = new XoopsThemeForm($title, 'moneusform', $action, 'post', true);
        $form->setExtra('enctype="multipart/form-data"');
        // menus: title
        $menus_title_text = new XoopsFormText(_AM_MYMENUS_MENU_TITLE, 'title', 50, 255, $this->getVar('title', 'e'));
        $menus_title_text->setDescription(_AM_MYMENUS_MENU_TITLE_DESC);
        $form->addElement($menus_title_text, true);
        // menus: css
        $menus_css_text = new XoopsFormText(_AM_MYMENUS_MENU_CSS, 'css', 50, 255, $this->getVar('css', 'e'));
        $menus_css_text->setDescription(_AM_MYMENUS_MENU_CSS_DESC);
        $form->addElement($menus_css_text, false);
        // form: button tray
        $button_tray = new XoopsFormElementTray('', '');
        $button_tray->addElement(new XoopsFormHidden('op', 'save'));
        //
        $button_submit = new XoopsFormButton('', '', _SUBMIT, 'submit');
        $button_submit->setExtra('onclick="this.form.elements.op.value=\'save\'"');
        $button_tray->addElement($button_submit);
        if ($this->isNew()) {
            // NOP
        } else {
            $form->addElement(new XoopsFormHidden('id', (int) $this->getVar('id')));
            //
            $button_delete = new XoopsFormButton('', '', _DELETE, 'submit');
            $button_delete->setExtra('onclick="this.form.elements.op.value=\'delete\'"');
            $button_tray->addElement($button_delete);
        }
        $button_reset = new XoopsFormButton('', '', _RESET, 'reset');
        $button_tray->addElement($button_reset);
        //
        $button_cancel = new XoopsFormButton('', '', _CANCEL, 'button');
        $button_cancel->setExtra('onclick="history.go(-1)"');
        $button_tray->addElement($button_cancel);
        //
        $form->addElement($button_tray);
        //
        return $form;
    }
}

/**
 * Class MymenusMenusHandler
 */
class MymenusMenusHandler extends XoopsPersistableObjectHandler
{
    /**
     * @var MymenusMymenus
     * @access private
     */
    private $mymenus = null;

    /**
     * @param null|object   $db
     */
    function __construct($db)
    {
        parent::__construct($db, 'mymenus_menus', 'MymenusMenus', 'id', 'title', 'css');
        $this->mymenus = MymenusMymenus::getInstance();
    }
}
