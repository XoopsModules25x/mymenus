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
 * @version         $Id: menu.php 0 2010-07-21 18:47:04Z trabis $
 */

defined("XOOPS_ROOT_PATH") or die("XOOPS root path not defined");

$dirname = basename(dirname(dirname(__FILE__)));
$module_handler = xoops_gethandler('module');
$module = $module_handler->getByDirname($dirname);
$pathIcon32 = $module->getInfo('icons32');

xoops_loadLanguage('admin', $dirname); 
 
$i = -1;
$i++;
$adminmenu[$i]["title"] = _MI_MYMENUS_ADMMENU0;
$adminmenu[$i]["link"] = 'admin/index.php';
$adminmenu[$i]["icon"] = $pathIcon32.'/home.png';
$i++;
$adminmenu[$i]['title'] = _MI_MYMENUS_MENUSMANAGER;
$adminmenu[$i]['link'] = "admin/admin_menus.php";
$adminmenu[$i]["icon"] = $pathIcon32.'/manage.png';
$i++;
$adminmenu[$i]['title'] = _MI_MYMENUS_MENUMANAGER;
$adminmenu[$i]['link'] = "admin/admin_menu.php";
$adminmenu[$i]["icon"] = $pathIcon32.'/insert_table_row.png';
$i++;
$adminmenu[$i]['title'] = _MI_MYMENUS_ABOUT;
$adminmenu[$i]['link'] = "admin/about.php";
$adminmenu[$i]["icon"] = $pathIcon32.'/about.png';

//$mymenus_adminmenu = $adminmenu;

