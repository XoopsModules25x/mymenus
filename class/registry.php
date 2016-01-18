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
 * @version         $Id: registry.php 12944 2015-01-23 13:05:09Z beckmi $
 */

defined('XOOPS_ROOT_PATH') || exit('XOOPS root path not defined');

/**
 * Class MymenusRegistry
 */
class MymenusRegistry
{
    protected $entries;
    protected $locks;

    /**
     *
     */
    protected function __construct()
    {
        $this->entries = array();
        $this->locks   = array();
    }

    /**
     * @return object MymenusRegistry
     */
    public static function getInstance()
    {
        static $instance = false;
        if (!$instance) {
            $instance = new self();
        }

        return $instance;
    }

    /**
     * @TODO: move hard coded language string to language file
     *
     * @param $key
     * @param $item
     *
     * @return bool
     */
    public function setEntry($key, $item)
    {
        if ($this->isLocked($key) === true) {
            trigger_error("Unable to set entry `{$key}`. Entry is locked.", E_USER_WARNING);

            return false;
        }

        $this->entries[$key] = $item;

        return true;
    }

    /**
     * @param $key
     */
    public function unsetEntry($key)
    {
        unset($this->entries[$key]);
    }

    /**
     * @param $key
     *
     * @return null
     */
    public function getEntry($key)
    {
        if (false === isset($this->entries[$key])) {
            return null;
        }

        return $this->entries[$key];
    }

    /**
     * @param $key
     *
     * @return bool
     */
    public function isEntry($key)
    {
        return (null !== $this->getEntry($key));
    }

    /**
     * @param $key
     *
     * @return bool
     */
    public function lockEntry($key)
    {
        $this->locks[$key] = true;

        return true;
    }

    /**
     * @param $key
     */
    public function unlockEntry($key)
    {
        unset($this->locks[$key]);
    }

    /**
     * @param $key
     *
     * @return bool
     */
    public function isLocked($key)
    {
        return (true === isset($this->locks[$key]));
    }

    public function unsetAll()
    {
        $this->entries = array();
        $this->locks   = array();
    }
}
