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
 * @copyright       XOOPS Project (https://xoops.org)
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU Public License
 * @package         Mymenus
 * @since           1.0
 * @author          trabis <lusopoemas@gmail.com>
 */

defined('XOOPS_ROOT_PATH') || die('Restricted access');

use XoopsModules\Mymenus;

// require_once __DIR__ . '/../class/Helper.php';
//require_once __DIR__ . '/../include/common.php';
$helper = Mymenus\Helper::getInstance();

$pathIcon32 = \Xmf\Module\Admin::menuIconPath('');
$pathModIcon32 = $helper->getModule()->getInfo('modicons32');

$adminmenu = [
    [
        'title' => _MI_MYMENUS_ADMMENU0,
        'link'  => 'admin/index.php',
        'icon'  => "{$pathIcon32}/home.png"
    ],
    [
        'title' => _MI_MYMENUS_MENUSMANAGER,
        'link'  => 'admin/menus.php',
        'icon'  => "{$pathIcon32}/manage.png"
    ],
    [
        'title' => _MI_MYMENUS_MENUMANAGER,
        'link'  => 'admin/links.php',
        'icon'  => "{$pathIcon32}/insert_table_row.png"
    ],
    [
        'title' => _MI_MYMENUS_ABOUT,
        'link'  => 'admin/about.php',
        'icon'  => "{$pathIcon32}/about.png"
    ]
];

//$mymenus_adminmenu = $adminmenu;
