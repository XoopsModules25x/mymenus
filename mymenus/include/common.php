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
 * Mymenus module
 *
 * @copyright       The XOOPS Project http://sourceforge.net/projects/xoops/
 * @license         GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package         mymenus
 * @since           1.5
 * @author          Xoops Development Team
 * @version         svn:$id$
 */

defined('XOOPS_ROOT_PATH') || exit('XOOPS root path not defined');

// This must contain the name of the folder in which reside mymenus
define("MYMENUS_DIRNAME", basename(dirname(__DIR__)));
define("MYMENUS_URL", XOOPS_URL . '/modules/' . MYMENUS_DIRNAME);
define("MYMENUS_ROOT_PATH", XOOPS_ROOT_PATH . '/modules/' . MYMENUS_DIRNAME);
define("MYMENUS_IMAGES_URL", MYMENUS_URL . '/assets/images');
define("MYMENUS_ADMIN_URL", MYMENUS_URL . '/admin');
define("MYMENUS_ICONS_URL", MYMENUS_URL . '/assets/images/icons');

xoops_loadLanguage('common', MYMENUS_DIRNAME);

include_once MYMENUS_ROOT_PATH . '/class/mymenus.php'; // MymenusMymenus class
include_once MYMENUS_ROOT_PATH . '/include/config.php'; // IN PROGRESS
include_once MYMENUS_ROOT_PATH . '/include/functions.php';
include_once MYMENUS_ROOT_PATH . '/include/constants.php';

xoops_load('XoopsUserUtility');
xoops_load('XoopsRequest');
xoops_load('XoopsFormLoader');

// MyTextSanitizer object
$myts = MyTextSanitizer::getInstance();

$debug   = false;
$mymenus = MymenusMymenus::getInstance($debug);

//This is needed or it will not work in blocks.
global $mymenusIsAdmin;

// Load only if module is installed
if (is_object($mymenus->getModule())) {
    // Find if the user is admin of the module
    $mymenusIsAdmin = mymenusUserIsAdmin();
}
$xoopsModule = $mymenus->getModule();

// Load Xoops handlers
$moduleHandler       = xoops_gethandler('module');
$memberHandler       = xoops_gethandler('member');
$notificationHandler = xoops_gethandler('notification');
$gpermHandler        = xoops_gethandler('groupperm');
$configHandler       = xoops_gethandler('config');
