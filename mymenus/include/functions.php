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
 * @version         $Id: functions.php 0 2010-07-21 18:47:04Z trabis $
 */

defined("XOOPS_ROOT_PATH") or die("XOOPS root path not defined");

function mymenus_adminMenu($currentoption = 0, $breadcrumb = '')
{
    include_once $GLOBALS['xoops']->path('class/template.php');
    include $GLOBALS['xoops']->path('modules/mymenus/admin/menu.php');

    xoops_loadLanguage('admin', 'mymenus');
    xoops_loadLanguage('modinfo', 'mymenus');

    $tpl = new XoopsTpl();
    $tpl->assign(array(
        'modurl'          => $GLOBALS['xoops']->url('modules/mymenus'),
        'headermenu'      => ((isset($mymenus_headermenu)) ? $mymenus_headermenu : ''),
        'adminmenu'       => ((isset($mymenus_adminmenu)) ? $mymenus_adminmenu : ''),
        'current'         => $currentoption,
        'breadcrumb'      => $breadcrumb,
        'headermenucount' => ((isset($mymenus_headermenu)) ? count($mymenus_headermenu) : 0)));
    $tpl->display($GLOBALS['xoops']->path('modules/mymenus/templates/static/mymenus_admin_adminmenu.html'));
}

function mymenus_getModuleConfig($dirname = 'mymenus')
{
    static $config;
    if (!$config) {
        global $xoopsModule;
        if (isset($xoopsModule) && is_object($xoopsModule) && $xoopsModule->getVar('dirname') == $dirname) {
            global $xoopsModuleConfig;
            $config =& $xoopsModuleConfig;
        } else {
            $hModule =& xoops_gethandler('module');
            $module = $hModule->getByDirname($dirname);
            $hConfig =& xoops_gethandler('config');
            $config = $hConfig->getConfigsByCat(0, $module->getVar('mid'));
        }
    }
    return $config;
}

function mymenus_getSkinInfo($skin, $skin_from_theme)
{
    $error = false;
    if ($skin_from_theme) {
        $path = "themes/" . $GLOBALS['xoopsConfig']['theme_set'] . "/menu";
        if (!file_exists($GLOBALS['xoops']->path("{$path}/skin_version.php"))) {
            $error = true;
        }
    }

    if ($error || !$skin_from_theme) {
        $path = "modules/mymenus/skins/{$skin}";
    }

    $file = $GLOBALS['xoops']->path("{$path}/skin_version.php");
    $info = array();

    if (file_exists($file)) {
        include $file;
        $info =& $skinversion;
    }

    $info['path'] = $GLOBALS['xoops']->path($path);
    $info['url'] = $GLOBALS['xoops']->url($path);

    if (!isset($info['template'])) {
        $info['template'] = $GLOBALS['xoops']->path("modules/mymenus/templates/static/blocks/mymenus_block.html");
    } else {
        $info['template'] = $GLOBALS['xoops']->path("{$path}/" . $info['template']);
    }

    if (!isset($info['prefix'])) {
        $info['prefix'] = $skin;
    }

    if (isset($info['css'])) {
        $info['css'] = (array)$info['css'];
        foreach ($info['css'] as $key => $value) {
            $info['css'][$key] = $GLOBALS['xoops']->url("{$path}/{$value}");
        }
    }

    if (isset($info['js'])) {
        $info['js'] = (array)$info['js'];
        foreach ($info['js'] as $key => $value) {
            $info['js'][$key] = $GLOBALS['xoops']->url("{$path}/{$value}");
        }
    }

    if (!isset($info['config'])) {
        $info['config'] = array();
    }

    return $info;
}

?>