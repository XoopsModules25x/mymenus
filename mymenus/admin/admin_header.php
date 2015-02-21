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
 * @version         $Id: admin_header.php 13003 2015-02-20 04:45:42Z zyspec $
 */

require_once dirname(dirname(dirname(__DIR__))) . '/include/cp_header.php';

if ( !@include_once $GLOBALS['xoops']->path('/Frameworks/moduleclasses/moduleadmin/moduleadmin.php') ) {
    redirect_header("../../../admin.php", 5, _AM_MODULEADMIN_MISSING, false);
    exit();
}

//global $xoopsModule;
$moduleInfo = $module_handler->get($xoopsModule->getVar('mid'));
$pathIcon16 = $GLOBALS['xoops']->url('www/' . $GLOBALS['xoopsModule']->getInfo('icons16'));
$pathIcon32 = $GLOBALS['xoops']->url('www/' . $GLOBALS['xoopsModule']->getInfo('icons32'));

$indexAdmin = new ModuleAdmin();

include_once $GLOBALS['xoops']->path('class/template.php');
include_once $GLOBALS['xoops']->path('modules/mymenus/include/functions.php');
include_once $GLOBALS['xoops']->path('modules/mymenus/class/registry.php');
include_once $GLOBALS['xoops']->path('modules/mymenus/class/plugin.php');

$mymenusTpl = new XoopsTpl();
if (!isset($GLOBALS['xoopsTpl']) || !($GLOBALS['xoopsTpl'] instanceof XoopsTpl)) {
    include_once $GLOBALS['xoops']->path("/class/template.php");
    $GLOBALS['xoopsTpl'] = new XoopsTpl();
}
$GLOBALS['xoopsTpl']->assign('pathIcon16', $pathIcon16);

xoops_load('XoopsFormLoader');
xoops_load('XoopsRequest');
xoops_loadLanguage('modinfo', 'mymenus');
