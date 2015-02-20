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
 * @version         $Id: admin_header.php 0 2010-07-21 18:47:04Z trabis $
 */

include_once dirname(dirname(dirname(__DIR__))) . '/mainfile.php';
include_once dirname(__DIR__) . '/include/common.php';

// Include xoops admin header
include_once XOOPS_ROOT_PATH . '/include/cp_header.php';

$pathIcon16 = XOOPS_URL . '/' . $mymenus->getModule()->getInfo('icons16');
$pathIcon32 = XOOPS_URL . '/' . $mymenus->getModule()->getInfo('icons32');
$pathModuleAdmin = XOOPS_ROOT_PATH . '/' . $mymenus->getModule()->getInfo('dirmoduleadmin');
require_once $pathModuleAdmin . '/moduleadmin/moduleadmin.php';

// Load language files
xoops_loadLanguage('admin', $mymenus->getModule()->dirname());
xoops_loadLanguage('modinfo', $mymenus->getModule()->dirname());
xoops_loadLanguage('main', $mymenus->getModule()->dirname());

if (!isset($xoopsTpl) || !is_object($xoopsTpl)) {
    include_once(XOOPS_ROOT_PATH . '/class/template.php');
    $xoopsTpl = new XoopsTpl();
}

include_once $GLOBALS['xoops']->path('class/template.php');
include_once $GLOBALS['xoops']->path('modules/mymenus/include/functions.php');
include_once $GLOBALS['xoops']->path('modules/mymenus/class/registry.php');
include_once $GLOBALS['xoops']->path('modules/mymenus/class/plugin.php');

//$mymenusTpl = new XoopsTpl();

/*


//global $xoopsModule;
$moduleInfo = $module_handler->get($xoopsModule->getVar('mid'));
$pathIcon16 = '../' . $xoopsModule->getInfo('icons16');
$pathIcon32 = '../' . $xoopsModule->getInfo('icons32');

$indexAdmin = new ModuleAdmin();

include_once $GLOBALS['xoops']->path('class/template.php');
include_once $GLOBALS['xoops']->path('modules/mymenus/include/functions.php');
include_once $GLOBALS['xoops']->path('modules/mymenus/class/registry.php');
include_once $GLOBALS['xoops']->path('modules/mymenus/class/plugin.php');

$mymenusTpl = new XoopsTpl();
if (!isset($xoopsTpl) || !is_object($xoopsTpl)) {
    include_once(XOOPS_ROOT_PATH . "/class/template.php");
    $xoopsTpl = new XoopsTpl();
}
$xoopsTpl->assign('pathIcon16', $pathIcon16);


xoops_load('XoopsFormLoader');
xoops_loadLanguage('modinfo', 'mymenus');
*/
