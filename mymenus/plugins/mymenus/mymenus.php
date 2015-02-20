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
 * Class MymenusMymenusPluginItem
 */
class MymenusMymenusPluginItem extends MymenusPluginItem
{

    function eventBoot()
    {
        $registry =& MymenusRegistry::getInstance();
        $member_handler =& xoops_getHandler('member');

        $user = $GLOBALS['xoopsUser'] ? $GLOBALS['xoopsUser'] : null;
        if (!$user) {
            $user = $member_handler->createUser();
            $user->setVar('uid', 0);
            $user->setVar('uname', $GLOBALS['xoopsConfig']['anonymous']);
        }

        $ownerid = isset($_GET['uid']) ? intval($_GET['uid']) : null;
        $owner = $member_handler->getUser($ownerid);
        //if uid > 0 but user does not exists
        if (!is_object($owner)) {
            //create new user
            $owner = $member_handler->createUser();
        }
        if ($owner->isNew()) {
            $owner->setVar('uid', 0);
            $owner->setVar('uname', $GLOBALS['xoopsConfig']['anonymous']);
        }
        $registry->setEntry('user', $user->getValues());
        $registry->setEntry('owner', $owner->getValues());
        $registry->setEntry('user_groups', $GLOBALS['xoopsUser'] ? $GLOBALS['xoopsUser']->getGroups() : array(XOOPS_GROUP_ANONYMOUS));
        $registry->setEntry('user_uid',  $GLOBALS['xoopsUser'] ? $GLOBALS['xoopsUser']->getVar('uid') : 0);
        $registry->setEntry('get_uid', isset($_GET['uid']) ? intval($_GET['uid']) : 0);

    }

    function eventLinkDecoration()
    {
        $registry =& MymenusRegistry::getInstance();
        $linkArray = $registry->getEntry('link_array');
        $linkArray['link'] = self::_doDecoration($linkArray['link']);
        //if (!eregi('mailto:', $linkArray['link']) && !eregi('://', $linkArray['link'])) {
        if (!preg_match('/mailto:/i', $linkArray['link']) && !preg_match('#://#i', $linkArray['link'])) {
            $linkArray['link'] = XOOPS_URL . '/' . $linkArray['link'];  //Do not do this in other decorators
        }
        $registry->setEntry('link_array', $linkArray);
    }

    function eventImageDecoration()
    {

        $registry =& MymenusRegistry::getInstance();
        $linkArray = $registry->getEntry('link_array');
        if (!empty($linkArray['image']) && !filter_var($linkArray['image'], FILTER_VALIDATE_URL)) {
            $linkArray['image'] = XOOPS_URL . '/' . $linkArray['image'];
              //Do not do this in other decorators
            $linkArray['image'] = self::_doDecoration($linkArray['image']);
            $registry->setEntry('link_array', $linkArray);
        }
    }

    function eventTitleDecoration()
    {
        $registry =& MymenusRegistry::getInstance();
        $linkArray = $registry->getEntry('link_array');
        $linkArray['title'] = self::_doDecoration($linkArray['title']);
        $registry->setEntry('link_array', $linkArray);
    }

    function eventAlttitleDecoration()
    {
        $registry =& MymenusRegistry::getInstance();
        $linkArray = $registry->getEntry('link_array');
        if (empty($linkArray['alt_title'])) {
            $linkArray['alt_title'] = $linkArray['title'];
        }
        $linkArray['alt_title'] = self::_doDecoration($linkArray['alt_title']);
        $registry->setEntry('link_array', $linkArray);
    }

    /**
     * @param $string
     *
     * @return mixed
     */
    function _doDecoration($string)
    {
        $registry =& MymenusRegistry::getInstance();
        //if (!eregi("{(.*\|.*)}", $string, $reg)) {
        if (!preg_match('/{(.*\|.*)}/i', $string, $reg)) {
            return $string;
        }

        $expression = $reg[0];
        list($validator, $value) = array_map('strtolower', explode('|', $reg[1]));

        //just to prevent any bad admin to get easy passwords
        if ($value == 'pass') return $string;

        if ($validator == 'user') {
            $user = $registry->getEntry('user');
            $value = isset($user[$value]) ? $user[$value] : self::getExtraValue('user', $value);
            $string = str_replace($expression, $value, $string);
        }

        if ($validator == 'uri') {
            $value = isset($_GET[$value]) ? $_GET[$value] : 0;
            $string = str_replace($expression, $value, $string);
        }

        if ($validator == 'owner') {
            $owner = $registry->getEntry('owner');
            $value = isset($owner[$value]) ? $owner[$value] : self::getExtraValue('owner', $value);
            $string = str_replace($expression, $value, $string);
        }

        return $string;
    }

    function eventFormLinkDescription()
    {
        $registry =& MymenusRegistry::getInstance();
        $description = $registry->getEntry('form_link_description');
    }

    function eventHasAccess()
    {
        $registry =& MymenusRegistry::getInstance();
        $menu   = $registry->getEntry('menu');
        $groups = $registry->getEntry('user_groups');
        if ($menu['visible'] == 0 || !array_intersect($menu['groups'], $groups)) {
            $registry->setEntry('has_access', 'no');

            return;
        }
        $hooks = array_intersect($menu['hooks'], get_class_methods(__CLASS__));

        foreach ($hooks as $method) {
            if (!self::$method()) {
                $registry->setEntry('has_access', 'no');

                return;
            }
        }

    }

    function eventAccessFilter()
    {
        self::loadLanguage('mymenus');
        $registry =& MymenusRegistry::getInstance();
        $access_filter = $registry->getEntry('access_filter');
        $access_filter['is_owner']['name'] = _PL_MYMENUS_MYMENUS_ISOWNER;
        $access_filter['is_owner']['method'] = 'isOwner';
        $access_filter['is_not_owner']['name'] = _PL_MYMENUS_MYMENUS_ISNOTOWNER;
        $access_filter['is_not_owner']['method'] = 'isNotOwner';
        $registry->setEntry('access_filter', $access_filter);
    }

    /**
     * @return bool
     */
    function isOwner()
    {
        $registry =& MymenusRegistry::getInstance();

        return ($registry->getEntry('user_uid') != 0 && $registry->getEntry('user_uid') == $registry->getEntry('get_uid')) ? true : false;
    }

    /**
     * @return bool
     */
    function isNotOwner()
    {
        return !self::isOwner();
    }

    /**
     * @param string $type
     * @param        $value
     *
     * @return int
     */
    function getExtraValue($type = 'user', $value)
    {
        $registry =& MymenusRegistry::getInstance();
        $ret = 0;
        $values = array('pm_new', 'pm_readed', 'pm_total');
        if (!in_array($value, $values)) return $ret;

        $entry = $registry->getEntry($type);
        if (empty($entry)) return $ret;

        $pm_handler =& xoops_gethandler('privmessage');

        if ($value == 'pm_new') {
            $criteria = new CriteriaCompo(new Criteria('read_msg', 0));
            $criteria->add(new Criteria('to_userid', $entry['uid']));
        }

        if ($value == 'pm_readed') {
            $criteria = new CriteriaCompo(new Criteria('read_msg', 1));
            $criteria->add(new Criteria('to_userid', $entry['uid']));
        }

        if ($value == 'pm_total') {
            $criteria = new Criteria('to_userid', $entry['uid']);
        }

        $entry[$value] = $pm_handler->getCount($criteria);

        $registry->setEntry($type, $entry);

        unset($criteria);

        return $entry[$value];
    }
}
