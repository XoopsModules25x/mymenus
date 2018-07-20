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

use XoopsModules\Mymenus;

require dirname(__DIR__) . '/preloads/autoloader.php';

//defined('XOOPS_ROOT_PATH') || die('Restricted access');

$moduleDirName = basename(dirname(__DIR__));
$moduleDirNameUpper   = strtoupper($moduleDirName); //$capsDirName

/** @var \XoopsDatabase $db */
/** @var \XoopsModules\Mymenus\Helper $helper */
/** @var \XoopsModules\Mymenus\Utility $utility */
$db      = \XoopsDatabaseFactory::getDatabaseConnection();
$debug   = false;
$helper  = \XoopsModules\Mymenus\Helper::getInstance($debug);
$utility = new \XoopsModules\Mymenus\Utility();

$helper->loadLanguage('common');

$pathIcon16    = \Xmf\Module\Admin::iconUrl('', 16);
$pathIcon32    = \Xmf\Module\Admin::iconUrl('', 32);
if (is_object($helper->getModule())) {
    $pathModIcon16 = $helper->getModule()->getInfo('modicons16');
    $pathModIcon32 = $helper->getModule()->getInfo('modicons32');
}

// This must contain the name of the folder in which reside mymenus
define('MYMENUS_DIRNAME', basename(dirname(__DIR__)));
define('MYMENUS_URL', XOOPS_URL . '/modules/' . MYMENUS_DIRNAME);
define('MYMENUS_ROOT_PATH', XOOPS_ROOT_PATH . '/modules/' . MYMENUS_DIRNAME);
define('MYMENUS_IMAGES_URL', MYMENUS_URL . '/assets/images');
define('MYMENUS_ADMIN_URL', MYMENUS_URL . '/admin');
define('MYMENUS_ICONS_URL', MYMENUS_URL . '/assets/images/icons');


//require MYMENUS_ROOT_PATH . '/include/config.php'; // IN PROGRESS
require MYMENUS_ROOT_PATH . '/include/constants.php';

xoops_load('XoopsUserUtility');
xoops_load('XoopsFormLoader');

// module information
$moduleImageUrl      = MYMENUS_URL . '/assets/images/mymenus.png';
$moduleCopyrightHtml = ''; //"<br><br><a href='' title='' target='_blank'><img src='{$moduleImageUrl}' alt=''></a>";

// MyTextSanitizer object
$myts = \MyTextSanitizer::getInstance();




//This is needed or it will not work in blocks.
global $mymenusIsAdmin;

// Load only if module is installed
if (is_object($helper->getModule())) {
    // Find if the user is admin of the module
    $mymenusIsAdmin = Mymenus\Helper::getInstance()->isUserAdmin();
}
$xoopsModule = $helper->getModule();

// Load Xoops handlers
/** @var XoopsModuleHandler $moduleHandler */
$moduleHandler       = xoops_getHandler('module');
/** @var XoopsMemberHandler $memberHandler */
$memberHandler       = xoops_getHandler('member');
/** @var XoopsNotificationHandler $notificationHandler */
$notificationHandler = xoops_getHandler('notification');
/** @var XoopsGroupPermHandler $grouppermHandler */
$grouppermHandler        = xoops_getHandler('groupperm');
/** @var XoopsConfigHandler $configHandler */
$configHandler       = xoops_getHandler('config');
