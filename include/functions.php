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

defined('XOOPS_ROOT_PATH') || die('Restricted access');

require_once __DIR__ . '/common.php';

/**
 * Checks if a user is admin of Mymenus
 *
 * @return boolean
 */
function mymenusUserIsAdmin()
{
    $mymenus = MymenusMymenus::getInstance();

    static $mymenusIsAdmin;
    if (isset($mymenusIsAdmin)) {
        return $mymenusIsAdmin;
    }

    $mymenusIsAdmin = (!is_object($GLOBALS['xoopsUser'])) ? false : $GLOBALS['xoopsUser']->isAdmin($mymenus->getModule()->getVar('mid'));

    return $mymenusIsAdmin;
}

/**
 * @param string  $moduleSkin
 * @param boolean $useThemeSkin
 * @param string  $themeSkin
 *
 * @return array
 */
function mymenusGetSkinInfo($moduleSkin = 'default', $useThemeSkin = false, $themeSkin = '')
{
    require_once __DIR__ . '/common.php';
    $mymenus = MymenusMymenus::getInstance();
    $error   = false;
    if ($useThemeSkin) {
        $path = 'themes/' . $GLOBALS['xoopsConfig']['theme_set'] . '/menu';
        if (!file_exists($GLOBALS['xoops']->path("{$path}/skin_version.php"))) {
            $path = 'themes/' . $GLOBALS['xoopsConfig']['theme_set'] . "/modules/{$mymenus->dirname}/skins/{$themeSkin}";
            if (!file_exists($GLOBALS['xoops']->path("{$path}/skin_version.php"))) {
                $error = true;
            }
        }
    }

    if ($error || !$useThemeSkin) {
        $path = "modules/{$mymenus->dirname}/skins/{$moduleSkin}";
    }

    $file = $GLOBALS['xoops']->path("{$path}/skin_version.php");
    $info = [];

    if (file_exists($file)) {
        include $file;
        $info = $skinVersion;
    }

    $info['path'] = $GLOBALS['xoops']->path($path);
    $info['url']  = $GLOBALS['xoops']->url($path);

    if (!isset($info['template'])) {
        $info['template'] = $GLOBALS['xoops']->path("modules/{$mymenus->dirname}/templates/static/blocks/mymenus_block.tpl");
    } else {
        $info['template'] = $GLOBALS['xoops']->path("{$path}/" . $info['template']);
    }

    if (!isset($info['prefix'])) {
        $info['prefix'] = $moduleSkin;
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
        $info['config'] = [];
    }

    return $info;
}
