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
 * @version         $Id: mymenus_block.php 0 2010-07-21 18:47:04Z trabis $
 */

defined('XOOPS_ROOT_PATH') || die('XOOPS root path not defined');
include_once dirname(__DIR__) . '/include/common.php';

/**
 * @param $options
 *
 * @return array|bool
 */
function mymenus_block_show($options)
{
    global $xoopsTpl, $xoTheme, $xoopsUser, $xoopsConfig, $xoopsLogger;
    $mymenus = MymenusMymenus::getInstance();

    $block = array();
    $xoopsLogger->startTime('My Menus Block');
    $myts = MyTextSanitizer::getInstance();

    include_once $GLOBALS['xoops']->path('modules/mymenus/include/functions.php');
    include_once $GLOBALS['xoops']->path('modules/mymenus/class/registry.php');
    include_once $GLOBALS['xoops']->path('modules/mymenus/class/plugin.php');
    include_once $GLOBALS['xoops']->path('modules/mymenus/class/builder.php');

    $registry = MymenusRegistry::getInstance();
    $plugin = MymenusPlugin::getInstance();
    $plugin->triggerEvent('Boot');

    $menu_id = $options[0];

    $links_handler = xoops_getModuleHandler('links', 'mymenus');
    $criteria = new CriteriaCompo(new Criteria('mid', $menu_id));
    $criteria->setSort('weight');
    $criteria->setOrder('ASC');
    //get menus as an array with ids as keys
    $menus = $links_handler->getAll($criteria, null, false, false);
    unset($criteria);

    foreach ($menus as $key => $links) {
        $registry->setEntry('menu', $links);
        $registry->setEntry('has_access', 'yes');
        $plugin->triggerEvent('HasAccess');
        if ($registry->getEntry('has_access') == 'no') {
            unset($menus[$key]);
        }
    }

    $count = count($menus);
    if ($count == 0) return $block;

    foreach ($menus as $key => $links) {
        $registry->setEntry('link_array', $links);
        $plugin->triggerEvent('TitleDecoration');
        $plugin->triggerEvent('AlttitleDecoration');
        $plugin->triggerEvent('LinkDecoration');
        $plugin->triggerEvent('ImageDecoration');
        $menus[$key] = $registry->getEntry('link_array');
    }

    $registry->setEntry('menus', $menus);
    $plugin->triggerEvent('End');
    $menus = $registry->getEntry('menus');

    $builder = new MymenusBuilder($menus);
    $block = $builder->render();

    /*--------------------------------------------------------------*/
    //default files to load
    $css = array();
    $js = array();

    //get extra files from skins
    $skin = $options[1];
    $skin_info = mymenus_getSkinInfo($skin, $options[2], $options[3]);

    if (isset($skin_info['css'])) {
        $css = array_merge($css, $skin_info['css']);
    }

    if (isset($skin_info['js'])) {
        $js = array_merge($js, $skin_info['js']);

    }

    if ($mymenus->getConfig('assign_method') == 'xoopstpl') {
        $tpl_vars = '';
        foreach ($css as $file) {
            $tpl_vars .= "\n" . '<link rel="stylesheet" type="text/css" media="all" href="' . $file . '" />';
        }

        foreach ($js as $file) {
            $tpl_vars .= "\n" . '<script type="text/javascript" src="' . $file . '"></script>';
        }

        if (isset($skin_info['header'])) {
            $tpl_vars .= "\n" . $skin_info['header'];
        }

        $xoopsTpl->assign('xoops_module_header' , $tpl_vars . @$xoopsTpl->get_template_vars("xoops_module_header"));
    } else {
        foreach ($css as $file) {
            $xoTheme->addStylesheet($file);
        }

        foreach ($js as $file) {
            $xoTheme->addScript($file);
        }

        if (isset($skin_info['header'])) {
            $xoopsTpl->assign('xoops_footer' , @$xoopsTpl->get_template_vars("xoops_footer") . "\n" . $skin_info['header']);
        }
    }

    $blockTpl = new XoopsTpl();
    $blockTpl->assign('block', $block);
    $blockTpl->assign('config', $skin_info['config']);
    $blockTpl->assign('skinurl', $skin_info['url']);
    $blockTpl->assign('skinpath', $skin_info['path']);

    // xLanguage check
    if (xoops_isActiveModule('xlanguage')) {
        $blockTpl->assign('xlanguage', true);
    }

    // assign ul class
    $menusCss = '' ;
    $menusCriteria = new Criteria('id', $menu_id);
    $menusObjs = $mymenus->getHandler('menus')->getAll($menusCriteria, null, false, false); // as array
    foreach ($menusObjs as $menusObj) {
           $menusCss = isset($menusObj['css']) ? $menusObj['css']: '';
    }
    if (!$menusCss) {$menusCss = '';}
    $blockTpl->assign('menucss', $menusCss);

    $block['content'] = $blockTpl->fetch($skin_info['template']);

    if ($options[3] == 'template') {
        $xoopsTpl->assign($options[4] , $block['content']);
        $block = false;
    }

    $registry->unsetAll();
    unset($registry, $plugin);
    $xoopsLogger->stopTime('My Menus Block');

    return $block;
}

/**
 * @param $options
 *
 * @return string
 */
function mymenus_block_edit($options)
{
    $mymenus = MymenusMymenus::getInstance();

    // Unique ID
    if (!$options[3] || (isset($_GET['op']) && $_GET['op'] == 'clone')) $options[3] = time();
    $i = 0;

    xoops_loadLanguage('admin', 'mymenus');

    $menusCriteria = new CriteriaCompo();
    $menusCriteria->setSort('title');
    $menusCriteria->setOrder('ASC');
    $menus = $mymenus->getHandler('menus')->getList($menusCriteria);
    unset($menusCriteria);
    if (count($menus) == 0) {
        $form = "<a href='" . $GLOBALS['xoops']->url('modules/mymenus/admin/menus.php') . "'>" . _AM_MYMENUS_MSG_NOMENUS . "</a>";
        return $form;
    }

    xoops_load('XoopsFormLoader');

    // Menu 0
    $form = "<b>" . _MB_MYMENUS_SELECT_MENU . "</b>&nbsp;";
    $element = new XoopsFormSelect('', 'options[' . $i . ']', $options[$i], 1);
    $element->addOptionArray($menus);
    $form .= $element->render();
    $form .= "</select>\n&nbsp;&nbsp;<i>" . _MB_MYMENUS_SELECT_MENU_DSC . "</i><br /><br />";

    // Skin 1
    ++$i;
    xoops_load('XoopsLists');
    $temp_skins = XoopsLists::getDirListAsArray(XOOPS_ROOT_PATH . "/modules/mymenus/skins/", "");
    $skins_options = array();
    foreach ($temp_skins as $key => $skin) {
        if (file_exists($GLOBALS['xoops']->path('modules/mymenus/skins/' . $skin . '/skin_version.php'))) {
            $skins_options[$skin] = $skin;
        }
    }
    $form .= "<b>" . _MB_MYMENUS_SELECT_SKIN . "</b>&nbsp;";
    $element = new XoopsFormSelect('', 'options[' . $i . ']', $options[$i], 1);
    $element->addOptionArray($skins_options);
    $form .= $element->render();
    $form .= "</select>\n&nbsp;&nbsp;<i>" . _MB_MYMENUS_SELECT_SKIN_DSC . "</i><br /><br />";

    // Use skin from theme 2
    ++$i;
    $form .= "<b>" . _MB_MYMENUS_USE_THEME_SKIN . "</b>&nbsp;";
    $element = new XoopsFormRadioYN('', 'options[' . $i . ']', $options[$i]);
    $form .= $element->render();
    $form .= "</select>\n&nbsp;&nbsp;<i>" . _MB_MYMENUS_USE_THEME_SKIN_DSC . "</i><br /><br />";

    // Skin from theme 3
    if (file_exists(XOOPS_ROOT_PATH . '/themes/' . $GLOBALS['xoopsConfig']['theme_set'] . '/modules/mymenus/skins/')) {
        ++$i;
        xoops_load('XoopsLists');
        $temp_theme_skins = XoopsLists::getDirListAsArray(XOOPS_ROOT_PATH . "/themes/" . $GLOBALS['xoopsConfig']['theme_set'] . "/modules/mymenus/skins/","");
        if (isset($temp_theme_skins)) {
            $theme_skins_options = array();
            foreach ($temp_theme_skins as $key => $theme_skin) {
                if (file_exists(XOOPS_ROOT_PATH . '/themes/' . $GLOBALS['xoopsConfig']['theme_set'] . '/modules/mymenus/skins/' . $theme_skin . '/skin_version.php')) {
                    $theme_skins_options[$theme_skin] = '/themes/' . $GLOBALS['xoopsConfig']['theme_set'] . '/modules/mymenus/skins/' . $theme_skin . '';
                }
            }
            $form .= "<b>" . _MB_MYMENUS_SELECT_SKIN_FROM_THEME . "</b>&nbsp;";
            $element = new XoopsFormSelect('', 'options[' . $i . ']', $options[$i], 1);
            $element->addOption('', '/themes/' . $GLOBALS['xoopsConfig']['theme_set'] . '/menu');
            $element->addOptionArray($theme_skins_options);
            $form .= $element->render();
            $form .= "</select>\n&nbsp;&nbsp;<i>" . _MB_MYMENUS_SELECT_SKIN_FROM_THEME_DSC . "</i><br /><br />";
        }
    }

    // Display method 4
    ++$i;
    $display_options = array(
        'block'    => _MB_MYMENUS_DISPLAY_METHOD_BLOCK,
        'template' => _MB_MYMENUS_DISPLAY_METHOD_TEMPLATE
    );
    $form .= "<b>" . _MB_MYMENUS_DISPLAY_METHOD . "</b>&nbsp;";
    $element = new XoopsFormSelect('', 'options[' . $i . ']', $options[$i], 1);
    $element->addOptionArray($display_options);
    $form .= $element->render();
    $form .= "</select>\n&nbsp;&nbsp;<i>" . _MB_MYMENUS_DISPLAY_METHOD_DSC . "</i><br /><br />";

    //Unique ID 5
    ++$i;
    $form .= "<b>" . _MB_MYMENUS_UNIQUEID . "</b>&nbsp;";
    $element = new XoopsFormText('', 'options[' . $i . ']', 10, 50, $options[$i]);
    $form .= $element->render();
    $form .= "\n&nbsp;&nbsp;<i>" . _MB_MYMENUS_UNIQUEID_DSC . "</i><br /><br />";

    return $form;
}
