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

require  dirname(__DIR__) . '/include/common.php';

/**
 * Class MymenusLinks
 */
class Links extends \XoopsObject
{
    /**
     * @var Links
     * @access private
     */
    private $helper;
    private $db;

    /**
     * constructor
     */
    public function __construct()
    {
        /** @var \XoopsModules\Mymenus\Helper $this->helper */
        $this->helper = \XoopsModules\Mymenus\Helper::getInstance();
        $this->db      = \XoopsDatabaseFactory::getDatabaseConnection();
        $this->initVar('id', XOBJ_DTYPE_INT);
        $this->initVar('pid', XOBJ_DTYPE_INT);
        $this->initVar('mid', XOBJ_DTYPE_INT);
        $this->initVar('title', XOBJ_DTYPE_TXTBOX);
        $this->initVar('alt_title', XOBJ_DTYPE_TXTBOX);
        $this->initVar('visible', XOBJ_DTYPE_INT, true);
        $this->initVar('link', XOBJ_DTYPE_TXTBOX);
        $this->initVar('weight', XOBJ_DTYPE_INT, 255);
        $this->initVar('target', XOBJ_DTYPE_TXTBOX);
        $this->initVar('groups', XOBJ_DTYPE_ARRAY, serialize([XOOPS_GROUP_ANONYMOUS, XOOPS_GROUP_USERS]));
        $this->initVar('hooks', XOBJ_DTYPE_ARRAY, serialize([]));
        $this->initVar('image', XOBJ_DTYPE_TXTBOX);
        $this->initVar('css', XOBJ_DTYPE_TXTBOX);
    }

    /**
     * @return bool
     */
    public function checkAccess()
    {
        $hooks              = $this->getHooks();
        $hooks['mymenus'][] = 'checkAccess';
        foreach ($hooks as $hookName => $hook) {
            if (!mymenusHook($hookName, 'checkAccess', ['links' => $this])) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return array
     */
    public function getHooks()
    {
        $ret  = [];
        $data = $this->getVar('hooks', 'n');
        if (!$data) {
            return $ret;
        }
        $lines = explode("\n", $data);
        foreach ($lines as $line) {
            $line   = trim($line);
            $line   = explode('|', $line);
            $hook   = trim($line[0]);
            $method = isset($line[1]) ? trim($line[1]) : '';
            //$info = explode(',', trim($line[0]));
            $ret[$hook][] = $method;
        }

        return $ret;
    }
}
