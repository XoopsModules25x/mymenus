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
 * @version         $Id: plugin.php 12944 2015-01-23 13:05:09Z beckmi $
 */

defined('XOOPS_ROOT_PATH') || exit('XOOPS root path not defined');

include_once dirname(__DIR__) . '/include/common.php';
xoops_load('XoopsLists');
include_once $GLOBALS['xoops']->path("modules/{$mymenus->dirname}/class/registry.php");

/**
 * Class MymenusPlugin
 */
class MymenusPlugin
{

    protected $registry;
    protected $plugins;
    protected $events;
    public    $mymenus;

    /**
     *
     */
    public function __construct()
    {
        $this->plugins  = array();
        $this->events   = array();
        $this->registry =& MymenusRegistry::getInstance();
        $this->mymenus  = MymenusMymenus::getInstance();
        $this->setPlugins();
        $this->setEvents();
    }

    /**
     * @return MymenusPlugin
     */
    public static function &getInstance()
    {
        static $instance = false;
        if (!$instance) {
            $instance = new MymenusPlugin();
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
            include_once $GLOBALS['xoops']->path("modules/{$this->mymenus->dirname}/plugins/{$plugin}/{$plugin}.php");
            $className = ucfirst($plugin) . 'MymenusPluginItem';
            if (!class_exists($className)) {
                continue;
            }
            $classMethods = get_class_methods($className);
            foreach ($classMethods as $method) {
                if (0 === strpos($method, 'event')) {
                    $eventName                  = strtolower(str_replace('event', '', $method));
                    $event                      = array('className' => $className, 'method' => $method);
                    $this->events[$eventName][] = $event;
                }
            }
        }
    }

    /**
     * @param       $eventName
     * @param array $args
     */
    public function triggerEvent($eventName, $args = array())
    {
        $eventName = mb_strtolower(str_replace('.', '', $eventName));
        if (isset($this->events[$eventName])) {
            foreach ($this->events[$eventName] as $event) {
                call_user_func(array($event['className'], $event['method']), $args);
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
     * @param $name
     *
     * @return mixed
     */
    public function loadLanguage($name)
    {
        $mymenus  = MymenusMymenus::getInstance();
        $language = $GLOBALS['xoopsConfig']['language'];
//        $path     = $GLOBALS['xoops']->path("modules/{$mymenus->dirname}/plugins/{$name}/language");
//        if (!($ret = @include_once "{$path}/{$language}/{$name}.php")) {
//            $ret = @include_once "{$path}/english/{$name}.php";
//        }
//        return $ret;

        $path2     = "{$mymenus->dirname}/plugins/{$name}/{$language}/";
        xoops_loadLanguage($name, $path2);
        return true;
    }
}
