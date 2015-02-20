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
 * @version         $Id: mymenus.php 0 2010-07-21 18:47:04Z trabis $
 */

defined("XOOPS_ROOT_PATH") or die("XOOPS root path not defined");

/**
 * Class DynamicMymenusPluginItem
 */
class DynamicMymenusPluginItem extends MymenusPluginItem
{

    function eventEnd()
    {
        $newmenus='';
        $registry =& MymenusRegistry::getInstance();
        $menus = $registry->getEntry('menus');
        foreach ($menus as $menu) {
            if (!preg_match('/{(MODULE\|.*)}/i', $menu['title'], $reg)) {
                $newmenus[] = $menu;
                continue;
            }
            $result = array_map('strtolower', explode('|', $reg[1]));
            $moduleMenus = self::_getModuleMenus($result[1], $menu['pid']);
            foreach ($moduleMenus as $mMenu) {
                $newmenus[] = $mMenu;
            }
        }
        $registry->setEntry('menus', $newmenus);
    }

    /**
     * @param $module
     * @param $pid
     *
     * @return array
     */
    function _getModuleMenus($module, $pid)
    {
        global $xoopsDB, $xoopsUser, $xoopsConfig, $xoopsModule, $xoopsModuleConfig;
        static $id = -1;

        $ret = array();
        //Sanitizing $module
        if (preg_match('/[^a-z0-9\\/\\\\_.:-]/i', $module)) {
            return $ret;
        }

        $path = "modules/{$module}";
        $file = $GLOBALS['xoops']->path("{$path}/xoops_version.php");

        if (!file_exists($file)) {
            return $ret;
        }

        xoops_loadLanguage('modinfo', $module);

        $force = true;
        $overwrite = false;
        if ($force && (!is_object($xoopsModule) || $xoopsModule->getVar('dirname') != $module)) {
            $_xoopsModule = is_object($xoopsModule) ? $xoopsModule : $xoopsModule;
            $_xoopsModuleConfig = is_object($xoopsModuleConfig) ? $xoopsModuleConfig : $xoopsModuleConfig;
            $module_handler =& xoops_gethandler('module');
            $xoopsModule =& $module_handler->getByDirname($module);
            $GLOBALS['xoopsModule'] =& $xoopsModule;
            if (is_object($xoopsModule)) {
                $config_handler =& xoops_gethandler('config');
                $xoopsModuleConfig =& $config_handler->getConfigsByCat(0, $xoopsModule->getVar('mid'));
                $GLOBALS['xoopsModuleConfig'] =& $xoopsModuleConfig;
            }
            $overwrite = true;
        }

        $modversion['sub'] = array();
        include $file;

        $handler = xoops_getModuleHandler('links', 'mymenus');
        foreach ($modversion['sub'] as $links) {
            $obj = $handler->create();
            $obj->setVar('title', $links['name']);
            $obj->setVar('alt_title', $links['name']);
            $obj->setVar('link', $GLOBALS['xoops']->url("{$path}/{$links['url']}"));
            $obj->setVar('id', $id);
            $obj->setVar('pid', $pid);
            $ret[] = $obj->getValues();
            $id--;
        }

        if ($overwrite) {
            $xoopsModule =& $_xoopsModule;
            $GLOBALS['xoopsModule'] =& $xoopsModule;
            $xoopsModuleConfig =& $_xoopsModuleConfig;
            $GLOBALS['xoopsModuleConfig'] =& $xoopsModuleConfig;
        }

        return $ret;
    }

}
