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

use XoopsModules\Mymenus;

require_once __DIR__ . '/../../../include/cp_header.php';
require_once $GLOBALS['xoops']->path('www/class/xoopsformloader.php');
xoops_load('XoopsFormLoader');

// require_once __DIR__ . '/../class/Utility.php';
require_once __DIR__ . '/../include/common.php';

$moduleDirName = basename(dirname(__DIR__));
//$mymenus = MymenusMymenus::getInstance($debug);
$helper = Mymenus\Helper::getInstance();
/** @var Xmf\Module\Admin $adminObject */
$adminObject = Xmf\Module\Admin::getInstance();

$myts = \MyTextSanitizer::getInstance();

if (!isset($GLOBALS['xoopsTpl']) || !($GLOBALS['xoopsTpl'] instanceof XoopsTpl)) {
    require_once $GLOBALS['xoops']->path('class/template.php');
    $xoopsTpl = new \XoopsTpl();
}

$pathIcon16      = Xmf\Module\Admin::iconUrl('', 16);
$pathIcon32      = Xmf\Module\Admin::iconUrl('', 32);
$pathModIcon32 = $helper->getModule()->getInfo('modicons32');

$GLOBALS['xoopsTpl']->assign('pathIcon16', $pathIcon16);
$GLOBALS['xoopsTpl']->assign('pathIcon32', $pathIcon32);

// Load language files
$helper->loadLanguage('admin');
$helper->loadLanguage('modinfo');
$helper->loadLanguage('main');

require_once $GLOBALS['xoops']->path('class/template.php');
require_once $GLOBALS['xoops']->path("modules/{$mymenus->dirname}/include/functions.php");
require_once $GLOBALS['xoops']->path("modules/{$mymenus->dirname}/class/registry.php");
require_once $GLOBALS['xoops']->path("modules/{$mymenus->dirname}/class/plugin.php");

//Module specific elements
//require_once $GLOBALS['xoops']->path("modules/{$mymenus->dirname}/include/functions.php");
//require_once $GLOBALS['xoops']->path("modules/{$mymenus->dirname}/include/config.php");

//Handlers
//$XXXHandler = xoops_getModuleHandler('XXX', $mymenus->dirname);

$mymenusTpl = new \XoopsTpl();
