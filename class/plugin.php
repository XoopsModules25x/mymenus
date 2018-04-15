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

require_once  dirname(__DIR__) . '/include/common.php';
xoops_load('XoopsLists');

/**
 * Class Plugin
 */
class Plugin
{
    protected $registry;
    protected $plugins;
    protected $events;
    public $helper;

    /**
     *
     */
    public function __construct()
    {
        $this->plugins  = [];
        $this->events   = [];
        $this->registry = Mymenus\Registry::getInstance();
        $this->helper  = Mymenus\Helper::getInstance();
        $this->setPlugins();
        $this->setEvents();
    }

    /**
     * @return \XoopsModules\Mymenus\Plugin
     */
    public static function getInstance()
    {
        static $instance;
        if (null === $instance) {
            $instance = new static();
        }

        return $instance;
    }

    public function setPlugins()
    {
        if (is_dir($dir = $GLOBALS['xoops']->path("modules/{$this->helper->getDirname()}/plugins/"))) {
            $pluginsList = \XoopsLists::getDirListAsArray($dir);
            foreach ($pluginsList as $plugin) {
                if (file_exists($GLOBALS['xoops']->path("modules/{$this->helper->getDirname()}/plugins/{$plugin}/{$plugin}.php"))) {
                    $this->plugins[] = $plugin;
                }
            }
        }
    }

    public function setEvents()
    {
        foreach ($this->plugins as $plugin) {
            require_once $GLOBALS['xoops']->path("modules/{$this->helper->getDirname()}/plugins/{$plugin}/{$plugin}.php");
            $className = ucfirst($plugin) . 'PluginItem';
            if (!class_exists($className)) {
                continue;
            }
            $classMethods = get_class_methods($className);
            foreach ($classMethods as $method) {
                if (0 === strpos($method, 'event')) {
                    $eventName                  = strtolower(str_replace('event', '', $method));
                    $event                      = ['className' => $className, 'method' => $method];
                    $this->events[$eventName][] = $event;
                }
            }
        }
    }

    /**
     * @param string $eventName
     * @param array $args
     */
    public function triggerEvent($eventName, $args = [])
    {
        $eventName = mb_strtolower(str_replace('.', '', $eventName));
        if (isset($this->events[(string)$eventName])) {
            foreach ($this->events[(string)$eventName] as $event) {
                call_user_func([$event['className'], $event['method']], $args);
            }
        }
    }
}
