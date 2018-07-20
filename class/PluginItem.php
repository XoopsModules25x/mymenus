<?php namespace XoopsModules\Mymenus;

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

defined('XOOPS_ROOT_PATH') || die('Restricted access');

//require  dirname(__DIR__) . '/include/common.php';
xoops_load('XoopsLists');


/**
 * Class PluginItem
 */
class PluginItem
{

    /**
     * @param string $name
     *
     * @return mixed
     */
    public static function loadLanguage($name)
    {
        /** @var \XoopsModules\Mymenus\Helper $helper */
        $helper  = \XoopsModules\Mymenus\Helper::getInstance();
        $language = $GLOBALS['xoopsConfig']['language'];
        //        $path     = $GLOBALS['xoops']->path("modules/{$mymenus->dirname}/plugins/{$name}/language");
        //        if (!($ret = @require "{$path}/{$language}/{$name}.php")) {
        //            $ret = @require "{$path}/english/{$name}.php";
        //        }
        //        return $ret;

        $path2 = "{$helper->getDirname()}/plugins/{$name}/{$language}/";
        xoops_loadLanguage($name, $path2);

        return true;
    }
}
