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
 * @version         $Id: menu.php
 */

defined('XOOPS_ROOT_PATH') || exit('XOOPS root path not defined');

$moduleDirName = basename(dirname(__DIR__));
$moduleHandler = &xoops_gethandler("module");
$xoopsModule   = &XoopsModule::getByDirname($moduleDirName);
$moduleInfo    = &$moduleHandler->get($xoopsModule->getVar("mid"));
$pathIcon32    = '../../' . $moduleInfo->getInfo('systemIcons32');

xoops_loadLanguage('admin', $moduleDirName);

$adminmenu = array(
    array(
        'title' => _MI_MYMENUS_ADMMENU0,
        'link'  => 'admin/index.php',
        'icon'  => "{$pathIcon32}/home.png"
    ),
    array(
        'title' => _MI_MYMENUS_MENUSMANAGER,
        'link'  => "admin/menus.php",
        'icon'  => "{$pathIcon32}/manage.png"
    ),
    array(
        'title' => _MI_MYMENUS_MENUMANAGER,
        'link'  => "admin/links.php",
        'icon'  => "{$pathIcon32}/insert_table_row.png"
    ),
    array(
        'title' => _MI_MYMENUS_ABOUT,
        'link'  => "admin/about.php",
        'icon'  => "{$pathIcon32}/about.png"
    )
);

//$mymenus_adminmenu = $adminmenu;
