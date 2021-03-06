<?php namespace XoopsModules\Mymenus\Plugins\Smarty;

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

/**
 * Class PluginItem
 */
class PluginItem extends Mymenus\PluginItem
{
    public static function eventLinkDecoration()
    {
        $registry          = Mymenus\Registry::getInstance();
        $linkArray         = $registry->getEntry('link_array');
        $linkArray['link'] = self::doDecoration($linkArray['link']);
        $registry->setEntry('link_array', $linkArray);
    }

    public static function eventImageDecoration()
    {
        $registry           = Mymenus\Registry::getInstance();
        $linkArray          = $registry->getEntry('link_array');
        $linkArray['image'] = self::doDecoration($linkArray['image']);
        $registry->setEntry('link_array', $linkArray);
    }

    public static function eventTitleDecoration()
    {
        $registry           = Mymenus\Registry::getInstance();
        $linkArray          = $registry->getEntry('link_array');
        $linkArray['title'] = self::doDecoration($linkArray['title']);
        $registry->setEntry('link_array', $linkArray);
    }

    public static function eventAltTitleDecoration()
    {
        $registry               = Mymenus\Registry::getInstance();
        $linkArray              = $registry->getEntry('link_array');
        $linkArray['alt_title'] = self::doDecoration($linkArray['alt_title']);
        $registry->setEntry('link_array', $linkArray);
    }

    /**
     * @param $string
     *
     * @return mixed
     */
    protected static function doDecoration($string)
    {
        $registry = Mymenus\Registry::getInstance();
        if (!preg_match('/{(.*\|.*)}/i', $string, $reg)) {
            return $string;
        }

        $expression = $reg[0];
        list($validator, $value) = array_map('mb_strtolower', explode('|', $reg[1]));

        if ('smarty' === $validator) {
            if (isset($GLOBALS['xoopsTpl']->_tpl_vars[$value])) {
                $string = str_replace($expression, $GLOBALS['xoopsTpl']->_tpl_vars[$value], $string);
            }
        }

        return $string;
    }
}
