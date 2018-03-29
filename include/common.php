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
 * @copyright       XOOPS Project (https://xoops.org)
 * @license         GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package         mymenus
 * @since           1.5
 * @author          Xoops Development Team
 */

defined('XOOPS_ROOT_PATH') || die('Restricted access');

// This must contain the name of the folder in which reside mymenus
define('MYMENUS_DIRNAME', basename(dirname(__DIR__)));
define('MYMENUS_URL', XOOPS_URL . '/modules/' . MYMENUS_DIRNAME);
define('MYMENUS_ROOT_PATH', XOOPS_ROOT_PATH . '/modules/' . MYMENUS_DIRNAME);
define('MYMENUS_IMAGES_URL', MYMENUS_URL . '/assets/images');
define('MYMENUS_ADMIN_URL', MYMENUS_URL . '/admin');
define('MYMENUS_ICONS_URL', MYMENUS_URL . '/assets/images/icons');

xoops_loadLanguage('common', MYMENUS_DIRNAME);

require_once MYMENUS_ROOT_PATH . '/class/mymenus.php'; // MymenusMymenus class
require_once MYMENUS_ROOT_PATH . '/include/config.php'; // IN PROGRESS
require_once MYMENUS_ROOT_PATH . '/include/functions.php';
require_once MYMENUS_ROOT_PATH . '/include/constants.php';

xoops_load('XoopsUserUtility');
xoops_load('XoopsFormLoader');

// MyTextSanitizer object
$myts = \MyTextSanitizer::getInstance();

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
/** @var XoopsModuleHandler $moduleHandler */
$moduleHandler       = xoops_getHandler('module');
/** @var XoopsMemberHandler $memberHandler */
$memberHandler       = xoops_getHandler('member');
/** @var XoopsNotificationHandler $notificationHandler */
$notificationHandler = xoops_getHandler('notification');
/** @var XoopsGroupPermHandler $gpermHandler */
$gpermHandler        = xoops_getHandler('groupperm');
/** @var XoopsConfigHandler $configHandler */
$configHandler       = xoops_getHandler('config');
