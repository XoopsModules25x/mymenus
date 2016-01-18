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
 * @version         $Id: dynamic.php 12944 2015-01-23 13:05:09Z beckmi $
 */

defined('XOOPS_ROOT_PATH') || exit('XOOPS root path not defined');

/**
 * Class DynamicMymenusPluginItem
 */
class DynamicMymenusPluginItem extends MymenusPluginItem
{

    public function eventEnd()
    {
        $newmenus = '';
        $registry =& MymenusRegistry::getInstance();
        $menus    = $registry->getEntry('menus');
        foreach ($menus as $menu) {
            if (!preg_match('/{(MODULE\|.*)}/i', $menu['title'], $reg)) {
                $newmenus[] = $menu;
                continue;
            }
            $result      = array_map('mb_strtolower', explode('|', $reg[1]));
            $moduleMenus = self::getModuleMenus($result[1], $menu['pid']);
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
    protected function getModuleMenus($module, $pid)
    {
        global $xoopsModule;
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

        $overwrite = false;
        if ($force === true) {  //can set to false for debug
            if (!($xoopsModule instanceof XoopsModule) || ($xoopsModule->getVar('dirname') != $module)) {
                // @TODO: check the following 2 statements, they're basically just assigns - is this intended?
                $_xoopsModule           = ($xoopsModule instanceof XoopsModule) ? $xoopsModule : $xoopsModule;
                $_xoopsModuleConfig     = is_object($xoopsModuleConfig) ? $xoopsModuleConfig : $xoopsModuleConfig;
                $moduleHandler          =& xoops_gethandler('module');
                $xoopsModule            =& $moduleHandler->getByDirname($module);
                $GLOBALS['xoopsModule'] =& $xoopsModule;
                if ($xoopsModule instanceof XoopsModule) {
                    $configHandler                =& xoops_gethandler('config');
                    $xoopsModuleConfig            =& $configHandler->getConfigsByCat(0, $xoopsModule->getVar('mid'));
                    $GLOBALS['xoopsModuleConfig'] =& $xoopsModuleConfig;
                }
                $overwrite = true;
            }
        }
        $modversion['sub'] = array();
        include $file;

        $handler = xoops_getModuleHandler('links', 'mymenus');
        foreach ($modversion['sub'] as $links) {
            $obj = $handler->create();
            $obj->setVars(array('title'     => $links['name'],
                                'alt_title' => $links['name'],
                                'link'      => $GLOBALS['xoops']->url("{$path}/{$links['url']}"),
                                'id'        => $id,
                                'pid'       => (int)$pid)
            );
            $ret[] = $obj->getValues();
            $id--;
        }

        if ($overwrite) {
            $xoopsModule                  =& $_xoopsModule;
            $GLOBALS['xoopsModule']       =& $xoopsModule;
            $xoopsModuleConfig            =& $_xoopsModuleConfig;
            $GLOBALS['xoopsModuleConfig'] =& $xoopsModuleConfig;
        }

        return $ret;
    }
}
