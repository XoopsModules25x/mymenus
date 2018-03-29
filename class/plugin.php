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

require_once __DIR__ . '/../include/common.php';
xoops_load('XoopsLists');
require_once $GLOBALS['xoops']->path("modules/{$mymenus->dirname}/class/registry.php");

/**
 * Class MymenusPlugin
 */
class MymenusPlugin
{
    protected $registry;
    protected $plugins;
    protected $events;
    public $mymenus;

    /**
     *
     */
    public function __construct()
    {
        $this->plugins  = [];
        $this->events   = [];
        $this->registry = MymenusRegistry::getInstance();
        $this->mymenus  = MymenusMymenus::getInstance();
        $this->setPlugins();
        $this->setEvents();
    }

    /**
     * @return MymenusPlugin
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
        if (is_dir($dir = $GLOBALS['xoops']->path("modules/{$this->mymenus->dirname}/plugins/"))) {
            $pluginsList = XoopsLists::getDirListAsArray($dir);
            foreach ($pluginsList as $plugin) {
                if (file_exists($GLOBALS['xoops']->path("modules/{$this->mymenus->dirname}/plugins/{$plugin}/{$plugin}.php"))) {
                    $this->plugins[] = $plugin;
                }
            }
        }
    }

    public function setEvents()
    {
        foreach ($this->plugins as $plugin) {
            require_once $GLOBALS['xoops']->path("modules/{$this->mymenus->dirname}/plugins/{$plugin}/{$plugin}.php");
            $className = ucfirst($plugin) . 'MymenusPluginItem';
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
        if (isset($this->events[$eventName])) {
            foreach ($this->events[$eventName] as $event) {
                call_user_func([$event['className'], $event['method']], $args);
            }
        }
    }
}

/**
 * Class MymenusPluginItem
 */
class MymenusPluginItem
{

    /**
     * @param string $name
     *
     * @return mixed
     */
    public static function loadLanguage($name)
    {
        $mymenus  = MymenusMymenus::getInstance();
        $language = $GLOBALS['xoopsConfig']['language'];
        //        $path     = $GLOBALS['xoops']->path("modules/{$mymenus->dirname}/plugins/{$name}/language");
        //        if (!($ret = @require_once "{$path}/{$language}/{$name}.php")) {
        //            $ret = @require_once "{$path}/english/{$name}.php";
        //        }
        //        return $ret;

        $path2 = "{$mymenus->dirname}/plugins/{$name}/{$language}/";
        xoops_loadLanguage($name, $path2);

        return true;
    }
}
