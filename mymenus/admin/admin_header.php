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
 * @version         $Id: admin_header.php
 */

include_once dirname(dirname(dirname(__DIR__))) . '/mainfile.php';
defined('XOOPS_ROOT_PATH') || exit('XOOPS root path not defined');
include_once $GLOBALS['xoops']->path('www/include/cp_functions.php');
// Include xoops admin header
include_once $GLOBALS['xoops']->path('www/include/cp_header.php');
include_once $GLOBALS['xoops']->path('www/class/xoopsformloader.php');

xoops_load('XoopsRequest');

//$moduleDirName = $GLOBALS['xoopsModule']->getVar('dirname');
include_once dirname(__DIR__) . '/include/common.php';
//$mymenus = MymenusMymenus::getInstance($debug);

$pathIcon16      = $GLOBALS['xoops']->url('www/' . $GLOBALS['xoopsModule']->getInfo('systemIcons16'));
$pathIcon32      = $GLOBALS['xoops']->url('www/' . $GLOBALS['xoopsModule']->getInfo('systemIcons32'));
$xoopsModuleAdminPath = $GLOBALS['xoops']->path('www/' . $GLOBALS['xoopsModule']->getInfo('dirmoduleadmin'));
require_once "{$xoopsModuleAdminPath}/moduleadmin/moduleadmin.php";

$myts =& MyTextSanitizer::getInstance();
if (!isset($GLOBALS['xoopsTpl']) || !($GLOBALS['xoopsTpl'] instanceof XoopsTpl)) {
    include_once $GLOBALS['xoops']->path("/class/template.php");
    $xoopsTpl = new XoopsTpl();
}

$GLOBALS['xoopsTpl']->assign('pathIcon16', $pathIcon16);
$GLOBALS['xoopsTpl']->assign('pathIcon32', $pathIcon32);

// Load language files
xoops_loadLanguage('admin', $mymenus->dirname);
xoops_loadLanguage('modinfo', $mymenus->dirname);
xoops_loadLanguage('main', $mymenus->dirname);

include_once $GLOBALS['xoops']->path('class/template.php');
include_once $GLOBALS['xoops']->path("modules/{$mymenus->dirname}/include/functions.php");
include_once $GLOBALS['xoops']->path("modules/{$mymenus->dirname}/class/registry.php");
include_once $GLOBALS['xoops']->path("modules/{$mymenus->dirname}/class/plugin.php");

//Module specific elements
//include_once $GLOBALS['xoops']->path("modules/{$mymenus->dirname}/include/functions.php");
//include_once $GLOBALS['xoops']->path("modules/{$mymenus->dirname}/include/config.php");

//Handlers
//$XXXHandler =& xoops_getModuleHandler('XXX', $mymenus->dirname);

//$mymenusTpl = new XoopsTpl();

/*


//global $xoopsModule;
$moduleInfo = $moduleHandler->get($xoopsModule->getVar('mid'));
$pathIcon16 = '../' . $xoopsModule->getInfo('icons16');
$pathIcon32 = '../' . $xoopsModule->getInfo('icons32');

$indexAdmin = new ModuleAdmin();

include_once $GLOBALS['xoops']->path('class/template.php');
include_once $GLOBALS['xoops']->path("modules/{$mymenus->dirname}/include/functions.php");
include_once $GLOBALS['xoops']->path("modules/{$mymenus->dirname}/class/registry.php");
include_once $GLOBALS['xoops']->path("modules/{$mymenus->dirname}/class/plugin.php");

$mymenusTpl = new XoopsTpl();
if (!isset($xoopsTpl) || !is_object($xoopsTpl)) {
    include_once(XOOPS_ROOT_PATH . "/class/template.php");
    $xoopsTpl = new XoopsTpl();
}
$xoopsTpl->assign('pathIcon16', $pathIcon16);


xoops_load('XoopsFormLoader');
xoops_loadLanguage('modinfo', 'mymenus');
*/
