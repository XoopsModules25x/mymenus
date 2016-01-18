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
 * @version         $Id: constant.php 12944 2015-01-23 13:05:09Z beckmi $
 */

defined('XOOPS_ROOT_PATH') || exit('XOOPS root path not defined');

/**
 * Class ConstantMymenusPluginItem
 */
class ConstantMymenusPluginItem extends MymenusPluginItem
{

    public function eventLinkDecoration()
    {
        $registry          =& MymenusRegistry::getInstance();
        $linkArray         = $registry->getEntry('link_array');
        $linkArray['link'] = self::doDecoration($linkArray['link']);
        $registry->setEntry('link_array', $linkArray);
    }

    public function eventImageDecoration()
    {
        $registry           =& MymenusRegistry::getInstance();
        $linkArray          = $registry->getEntry('link_array');
        $linkArray['image'] = self::doDecoration($linkArray['image']);
        $registry->setEntry('link_array', $linkArray);
    }

    public function eventTitleDecoration()
    {
        $registry           =& MymenusRegistry::getInstance();
        $linkArray          = $registry->getEntry('link_array');
        $linkArray['title'] = self::doDecoration($linkArray['title']);
        $registry->setEntry('link_array', $linkArray);
    }

    public function eventAlttitleDecoration()
    {
        $registry               =& MymenusRegistry::getInstance();
        $linkArray              = $registry->getEntry('link_array');
        $linkArray['alt_title'] = self::doDecoration($linkArray['alt_title']);
        $registry->setEntry('link_array', $linkArray);
    }

    /**
     * @param $string
     *
     * @return mixed
     */
    protected function doDecoration($string)
    {
        $registry =& MymenusRegistry::getInstance();
//        $string = '';

        if (!preg_match('/{(.*\|.*)}/i', $string, $reg)) {
            return $string;
        }

        $expression = $reg[0];
        list($validator, $value) = array_map('strtoupper', explode('|', $reg[1]));

        if ('CONSTANT' == $validator) {
            if (defined($value)) {
                $string = str_replace($expression, constant($value), $string);
            }
        }

        return isset($string) ? $string : null;
    }
}
