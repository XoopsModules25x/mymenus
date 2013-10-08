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
 * @version         $Id: registry.php 0 2010-07-21 18:47:04Z trabis $
 */

defined("XOOPS_ROOT_PATH") or die("XOOPS root path not defined");

class MymenusRegistry
{
    protected $_entries;
    protected $_locks;

    protected function __construct()
    {
        $this->_entries = array();
        $this->_locks = array();
    }

    public function getInstance()
    {
        static $instance = false;
        if (!$instance) {
            $instance = new self();
        }
        return $instance;
    }

    public function setEntry($key, $item)
    {
        if ($this->isLocked($key) == true) {
            trigger_error('Unable to set entry `' . $key . '`. Entry is locked.', E_USER_WARNING);
            return false;
        }

        $this->_entries[$key] = $item;
        return true;
    }

    public function unsetEntry($key)
    {
        unset($this->_entries[$key]);
    }

    public function getEntry($key)
    {
        if (isset($this->_entries[$key]) == false) {
            return null;
        }

        return $this->_entries[$key];
    }

    public function isEntry($key)
    {
        return ($this->getEntry($key) !== null);
    }

    public function lockEntry($key)
    {
        $this->_locks[$key] = true;
        return true;
    }

    public function unlockEntry($key)
    {
        unset($this->_locks[$key]);
    }

    public function isLocked($key)
    {
        return (isset($this->_locks[$key]) == true);
    }

    public function unsetAll()
    {
        $this->_entries = array();
        $this->_locks = array();
    }

}

?>