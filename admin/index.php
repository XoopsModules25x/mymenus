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
 * @version         $Id: index.php
 */

include_once __DIR__ . '/admin_header.php';
$currentFile = basename(__FILE__);

// admin navigation
xoops_cp_header();
$indexAdmin = new ModuleAdmin();

echo $indexAdmin->addNavigation('index.php');
echo $indexAdmin->renderIndex();

include __DIR__ . '/admin_footer.php';
