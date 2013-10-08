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

require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/include/cp_header.php';

include_once $GLOBALS['xoops']->path('class/template.php');
include_once $GLOBALS['xoops']->path('modules/mymenus/include/functions.php');
include_once $GLOBALS['xoops']->path('modules/mymenus/class/registry.php');
include_once $GLOBALS['xoops']->path('modules/mymenus/class/plugin.php');

xoops_load('XoopsFormLoader');
xoops_loadLanguage('modinfo', 'mymenus');

$mymenusTpl = new XoopsTpl();

if ( file_exists($GLOBALS['xoops']->path('/Frameworks/moduleclasses/moduleadmin/moduleadmin.php'))){
        include_once $GLOBALS['xoops']->path('/Frameworks/moduleclasses/moduleadmin/moduleadmin.php');
        //return true;
    }else{
        redirect_header("../../../admin.php", 5, _AM_MODULEADMIN_MISSING, false);
        //return false;
    }

global $xoopsModule;
$pathIcon16 = '../'.$xoopsModule->getInfo('icons16');
$pathIcon32 = '../'.$xoopsModule->getInfo('icons32');

if (!isset($xoopsTpl) || !is_object($xoopsTpl)) {
	include_once(XOOPS_ROOT_PATH."/class/template.php");
	$xoopsTpl = new XoopsTpl();
}
$xoopsTpl->assign('pathIcon16', $pathIcon16);