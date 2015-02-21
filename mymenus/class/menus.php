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
 * @version         $Id: menus.php 12940 2015-01-21 17:33:38Z zyspec $
 */

defined("XOOPS_ROOT_PATH") || exit("Restricted access");

/**
 * Class MymenusMenus
 */
class MymenusMenus extends XoopsObject
{
    /**
     * constructor
     */
    function __construct()
    {
        $this->initVar("id", XOBJ_DTYPE_INT);
        $this->initVar('title', XOBJ_DTYPE_TXTBOX);
        //
        $this->initVar('css', XOBJ_DTYPE_TXTBOX);
        //
    }
}

/**
 * Class MymenusMenusHandler
 */
class MymenusMenusHandler extends XoopsPersistableObjectHandler
{
    /**
     * @param null|object $db
     */
    function __construct(&$db)
    {
        parent::__construct($db, 'mymenus_menus', 'MymenusMenus', 'id', 'title', 'css');
    }
}
