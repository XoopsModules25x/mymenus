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
 * @copyright       {@link http://sourceforge.net/projects/xoops/ The XOOPS Project}
 * @license         {@link http://www.gnu.org/licenses/gpl-2.0.html GNU Public License}
 * @package         Mymenus
 * @since           1.0
 * @author          trabis <lusopoemas@gmail.com>
 * @version         $Id: mymenus_block.php 13003 2015-02-20 04:45:42Z zyspec $
 */

defined('XOOPS_ROOT_PATH') || die('XOOPS root path not defined');
include_once dirname(__DIR__) . '/include/common.php';

/**
 * @param array $options array(0 => menu, 1 => module_skin, 2 => use_theme_skin, 3 => display_method, 4 => unique_id, 5 => theme_skin)
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

    $mid = $options[0];

    $linksCriteria = new CriteriaCompo(new Criteria('mid', $mid));
    $linksCriteria->setSort('weight');
    $linksCriteria->setOrder('ASC');
    //get menu links as an array with ids as keys
    $linksArray = $mymenus->getHandler('links')->getAll($linksCriteria, null, false, false); // as array
    unset($linksCriteria);

    foreach ($linksArray as $key => $links) {
        $registry->setEntry('menu', $links);
        $registry->setEntry('has_access', 'yes');
        $plugin->triggerEvent('HasAccess');
        if ('no' == $registry->getEntry('has_access')) {
            unset($linksArray[$key]);
        }
    }

    $linksCount = count($linksArray);
    if (0 == $linksCount) {
        return $block;
    }

    foreach ($linksArray as $key => $links) {
        $registry->setEntry('link_array', $links);
        $plugin->triggerEvent('TitleDecoration');
        $plugin->triggerEvent('AlttitleDecoration');
        $plugin->triggerEvent('LinkDecoration');
        $plugin->triggerEvent('ImageDecoration');
        $linksArray[$key] = $registry->getEntry('link_array');
    }
    $registry->setEntry('menus', $linksArray);
    $plugin->triggerEvent('End');
    $linksArray = $registry->getEntry('menus');

    $menuBuilder = new MymenusBuilder($linksArray);
    $block = $menuBuilder->render();

    /*--------------------------------------------------------------*/
    // Default files to load
    $cssArray = array();
    $jsArray  = array();

    // Get extra files from skins
    $skinInfo = mymenus_getSkinInfo($options[1], $options[2], $options[5]);

    //
    if (isset($skinInfo['css'])) {
        $cssArray = array_merge($cssArray, $skinInfo['css']);
    }
    if (isset($skinInfo['js'])) {
        $jsArray = array_merge($jsArray, $skinInfo['js']);
    }
    //
    if ($mymenus->getConfig('assign_method') == 'xoopstpl') {
        $tpl_vars = '';
        foreach ($cssArray as $file) {
            $tpl_vars .= "\n<link rel='stylesheet' type='text/css' media='all' href='{$file}' />";
        }
        foreach ($jsArray as $file) {
            $tpl_vars .= "\n<script type='text/javascript' src='{$file}'></script>";
        }
        if (isset($skinInfo['header'])) {
            $tpl_vars .= "\n{$skinInfo['header']}";
        }
        $GLOBALS['xoopsTpl']->assign('xoops_module_header' , $tpl_vars . @$GLOBALS['xoopsTpl']->get_template_vars("xoops_module_header"));
    } else {
        foreach ($cssArray as $file) {
            $xoTheme->addStylesheet($file);
        }
        foreach ($jsArray as $file) {
            $xoTheme->addScript($file);
        }
        if (isset($skinInfo['header'])) {
            $GLOBALS['xoopsTpl']->assign('xoops_footer' , @$GLOBALS['xoopsTpl']->get_template_vars("xoops_footer") . "\n" . $skinInfo['header']);
        }
    }
    //
    $blockTpl = new XoopsTpl();
    $blockTpl->assign(
        array(
            'block' => $block,
            'config' => $skinInfo['config'],
            'skinurl' => $skinInfo['url'],
            'skinpath' => $skinInfo['path'],
            'xlanguage' => xoops_isActiveModule('xlanguage') ? true : false // xLanguage check
        )
    );
    // Assign ul class
    $menusObj = $mymenus->getHandler('menus')->get($mid);
    $blockTpl->assign('menucss', $menusObj->getVar('css'));
/*
    $menuCss      = '';
    $menusHandler = xoops_getModuleHandler('menus', 'mymenus');
    $menuCriteria = new CriteriaCompo(new Criteria('id', $mid));
    $menuArray    = $menusHandler->getAll($menuCriteria, null, false, false);

    if (is_array($menuArray) && (count($menuArray) > 0)) {
        foreach ($menuArray as $menu) {
               $menuCss = isset($menu['css']) ? "{$menu['css']} " : '';
        }
        $menuCss = trim($menuCss);
    }
    if (empty($menuCss)) {
         $menuCss = "";
    } else {
        $menuCss = implode(' ', $menuCss);
    }
    $blockTpl->assign('menucss', $menuCss);
*/
    $block['content'] = $blockTpl->fetch($skinInfo['template']);

    if ('template' == $options[3]) {
        $GLOBALS['xoopsTpl']->assign($mymenus->getConfig('unique_id_prefix') . $options[4] , $block['content']);
        $block = false;
    }

    $registry->unsetAll();
    unset($registry, $plugin);
    $xoopsLogger->stopTime('My Menus Block');
    return $block;
}

/**
 * @param array $options array(0 => menu, 1 => module_skin, 2 => use_theme_skin, 3 => display_method, 4 => unique_id, 5 => theme_skin)
 *
 * @return string
 */
function mymenus_block_edit($options)
{
    $mymenus = MymenusMymenus::getInstance();
    //
    xoops_loadLanguage('admin', 'mymenus');
    xoops_load('XoopsFormLoader');
    // option 0: menu
    $menusCriteria = new CriteriaCompo();
    $menusCriteria->setSort('title');
    $menusCriteria->setOrder('ASC');
    $menusList = $mymenus->getHandler('menus')->getList($menusCriteria);
    unset($menusCriteria);
    if (0 == count($menusList)) {
        $form = "<a href='" . $GLOBALS['xoops']->url('modules/mymenus/admin/menus.php') . "'>" . _AM_MYMENUS_MSG_NOMENUS . "</a>\n";
        return $form;
    }
    $form = "<b>" . _MB_MYMENUS_SELECT_MENU . "</b>&nbsp;";
    $form_menus_select = new XoopsFormSelect('', "options[0]", $options[0], 1, false);
    $form_menus_select->addOptionArray($menusList);
    $form .= $form_menus_select->render();
    $form .= "</select>\n&nbsp;<i>" . _MB_MYMENUS_SELECT_MENU_DSC . "</i>\n<br /><br />\n";
    // option 1: module_skin
    xoops_load('XoopsLists');
    $tempModuleSkinsList = XoopsLists::getDirListAsArray($GLOBALS['xoops']->path('/modules/mymenus/skins/'), '');
    $moduleSkinsList = array();
    foreach ($tempModuleSkinsList as $key => $module_skin) {
        if (file_exists($GLOBALS['xoops']->path("modules/mymenus/skins/{$module_skin}/skin_version.php"))) {
            $moduleSkinsList[$module_skin] = $module_skin;
        }
    }
    $form .= "<b>" . _MB_MYMENUS_SELECT_SKIN . "</b>&nbsp;";
    $form_module_skin_select = new XoopsFormSelect('', "options[1]", $options[1], 1, false);
    $form_module_skin_select->addOptionArray($moduleSkinsList);
    $form .= $form_module_skin_select->render();
    $form .= "\n&nbsp;<i>" . _MB_MYMENUS_SELECT_SKIN_DSC . "</i>\n<br /><br />\n";
    // option 2: use_theme_skin
    $form .= "<b>" . _MB_MYMENUS_USE_THEME_SKIN . "</b>&nbsp;";
    $form_use_theme_skin_radio = new XoopsFormRadioYN('', "options[2]", $options[2]);
    $form .= $form_use_theme_skin_radio->render();
    $form .= "\n&nbsp;<i>" . _MB_MYMENUS_USE_THEME_SKIN_DSC . "</i>\n<br /><br />\n";
    // option 3: display_method
    $displayMethodsList = array(
        'block' => _MB_MYMENUS_DISPLAY_METHOD_BLOCK,
        'template' => _MB_MYMENUS_DISPLAY_METHOD_TEMPLATE
    );
    $form .= "<b>" . _MB_MYMENUS_DISPLAY_METHOD . "</b>&nbsp;";
    $form_display_method_select = new XoopsFormSelect('', "options[3]", $options[3], 1);
    $form_display_method_select->addOptionArray($displayMethodsList);
    $form .= $form_display_method_select->render();
    $form .= "\n&nbsp;<i>" . sprintf(_MB_MYMENUS_DISPLAY_METHOD_DSC, $mymenus->getConfig('unique_id_prefix')) . "</i>\n<br /><br />\n";
    // option 4: unique_id
    if (!$options[4] || (isset($_GET['op']) && 'clone' == $_GET['op'])) {
        $options[4] = time();
    }
    $form .= "<b>" . _MB_MYMENUS_UNIQUEID . "</b>&nbsp;";
    $form_unique_id_text = new XoopsFormText('', "options[4]", 50, 255, $options[4]);
    $form .= $form_unique_id_text->render();
    $form .= "\n&nbsp;<i>" . _MB_MYMENUS_UNIQUEID_DSC . "</i>\n<br /><br />\n";
    // option 5: theme_skin
    if (file_exists($GLOBALS['xoops']->path('/themes/' . $GLOBALS['xoopsConfig']['theme_set'] . '/modules/mymenus/skins/'))) {
        xoops_load('XoopsLists');
        $tempThemeSkinsList = XoopsLists::getDirListAsArray($GLOBALS['xoops']->path('/themes/' . $GLOBALS['xoopsConfig']['theme_set'] . '/modules/mymenus/skins/'), '');
        if (isset($tempThemeSkinsList)) {
            $themeSkinsList = array();
            foreach ($tempThemeSkinsList as $key => $theme_skin) {
                if (file_exists($GLOBALS['xoops']->path('/themes/' . $GLOBALS['xoopsConfig']['theme_set'] . "/modules/mymenus/skins/{$theme_skin}/skin_version.php"))) {
                    $themeSkinsList[$theme_skin] = '/themes/' . $GLOBALS['xoopsConfig']['theme_set'] . "/modules/mymenus/skins/{$theme_skin}";
                }
            }
            $form .= "<b>" . _MB_MYMENUS_SELECT_SKIN_FROM_THEME . "</b>&nbsp;";
            $form_theme_skin_select = new XoopsFormSelect('', "options[5]", $options[5], 1, false);
            $form_theme_skin_select->addOptionArray($themeSkinsList);
            $form .= $form_theme_skin_select->render();
            $form .= "\n&nbsp;&nbsp;<i>" . _MB_MYMENUS_SELECT_SKIN_FROM_THEME_DSC . "</i>\n<br /><br />\n";
        }
    }

    return $form;
}
