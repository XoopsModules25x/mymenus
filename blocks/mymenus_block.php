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
 */

use Xmf\Request;

defined('XOOPS_ROOT_PATH') || die('Restricted access');

require_once __DIR__ . '/../include/common.php';

/**
 * @param array $options array(0 => menu, 1 => moduleSkin, 2 => useThemeSkin, 3 => displayMethod, 4 => unique_id, 5 => themeSkin)
 *
 * @return array|bool
 */
function mymenus_block_show($options)
{
    global $xoopsTpl, $xoopsLogger;
    $mymenus = MymenusMymenus::getInstance();

    $block = [];
    $xoopsLogger->startTime('My Menus Block');
    $myts = \MyTextSanitizer::getInstance();

    require_once $GLOBALS['xoops']->path("modules/{$mymenus->dirname}/include/functions.php");
    require_once $GLOBALS['xoops']->path("modules/{$mymenus->dirname}/class/registry.php");
    require_once $GLOBALS['xoops']->path("modules/{$mymenus->dirname}/class/plugin.php");
    require_once $GLOBALS['xoops']->path("modules/{$mymenus->dirname}/class/builder.php");

    $registry = MymenusRegistry::getInstance();
    $plugin   = MymenusPlugin::getInstance();
    $plugin->triggerEvent('Boot');

    $mid = $options[0];

    $linksCriteria = new \CriteriaCompo(new \Criteria('mid', $mid));
    $linksCriteria->setSort('weight');
    $linksCriteria->setOrder('ASC');
    //get menu links as an array with ids as keys
    $linksArray = $mymenus->getHandler('links')->getAll($linksCriteria, null, false, false); // as array
    unset($linksCriteria);

    foreach ($linksArray as $key => $links) {
        $registry->setEntry('menu', $links);
        $registry->setEntry('has_access', 'yes');
        $plugin->triggerEvent('HasAccess');
        if ('no' === $registry->getEntry('has_access')) {
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
    $block       = $menuBuilder->render();

    /*--------------------------------------------------------------*/
    // Default files to load
    $cssArray = [];
    $jsArray  = [];

    // Get extra files from skins
    $skinInfo = mymenusGetSkinInfo($options[1], $options[2], isset($options[5]) ? $options[5] : '');

    //
    if (isset($skinInfo['css'])) {
        $cssArray = array_merge($cssArray, $skinInfo['css']);
    }
    if (isset($skinInfo['js'])) {
        $jsArray = array_merge($jsArray, $skinInfo['js']);
    }
    //
    if ('xoopstpl' === $mymenus->getConfig('assign_method')) {
        $tpl_vars = '';
        foreach ($cssArray as $file) {
            $tpl_vars .= "\n<link rel='stylesheet' type='text/css' media='all' href='{$file}'>";
        }
        foreach ($jsArray as $file) {
            $tpl_vars .= "\n<script type='text/javascript' src='{$file}'></script>";
        }
        if (isset($skinInfo['header'])) {
            $tpl_vars .= "\n{$skinInfo['header']}";
        }
        $GLOBALS['xoopsTpl']->assign('xoops_module_header', $tpl_vars . @$GLOBALS['xoopsTpl']->get_template_vars('xoops_module_header'));
    } else {
        foreach ($cssArray as $file) {
            $GLOBALS['xoTheme']->addStylesheet($file);
        }
        foreach ($jsArray as $file) {
            $GLOBALS['xoTheme']->addScript($file);
        }
        if (isset($skinInfo['header'])) {
            $GLOBALS['xoopsTpl']->assign('xoops_footer', @$GLOBALS['xoopsTpl']->get_template_vars('xoops_footer') . "\n" . $skinInfo['header']);
        }
    }
    //
    $blockTpl = new \XoopsTpl();
    $blockTpl->assign([
                          'block'     => $block,
                          'config'    => $skinInfo['config'],
                          'skinurl'   => $skinInfo['url'],
                          'skinpath'  => $skinInfo['path'],
                          'xlanguage' => xoops_isActiveModule('xlanguage') ? true : false // xLanguage check
                      ]);
    // Assign ul class
    $menusObj = $mymenus->getHandler('menus')->get($mid);
    $blockTpl->assign('menucss', $menusObj->getVar('css'));
    /*
        $menuCss      = '';
        $menusHandler = xoops_getModuleHandler('menus', 'mymenus');
        $menuCriteria = new \CriteriaCompo(new \Criteria('id', $mid));
        $menuArray    = $menusHandler->getAll($menuCriteria, null, false, false);

        if (is_array($menuArray) && (count($menuArray) > 0)) {
            foreach ($menuArray as $menu) {
                   $menuCss = isset($menu['css']) ? "{$menu['css']} " : '';
            }
            $menuCss = trim($menuCss);
        }
        if (!($menuCss)) {
             $menuCss = "";
        } else {
            $menuCss = implode(' ', $menuCss);
        }
        $blockTpl->assign('menucss', $menuCss);
    */
    $block['content'] = $blockTpl->fetch($skinInfo['template']);

    if ('template' === $options[3]) {
        $GLOBALS['xoopsTpl']->assign($mymenus->getConfig('unique_id_prefix') . $options[4], $block['content']);
        $block = false;
    }

    $registry->unsetAll();
    unset($registry, $plugin);
    $xoopsLogger->stopTime('My Menus Block');

    return $block;
}

/**
 * @param array $options array(0 => menu, 1 => moduleSkin, 2 => useThemeSkin, 3 => displayMethod, 4 => unique_id, 5 => themeSkin)
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
    $menusCriteria = new \CriteriaCompo();
    $menusCriteria->setSort('title');
    $menusCriteria->setOrder('ASC');
    $menusList = $mymenus->getHandler('menus')->getList($menusCriteria);
    unset($menusCriteria);
    if (0 == count($menusList)) {
        $form = "<a href='" . $GLOBALS['xoops']->url("modules/{$mymenus->dirname}/admin/menus.php") . "'>" . _AM_MYMENUS_MSG_NOMENUS . "</a>\n";

        return $form;
    }
    $form            = '<b>' . _MB_MYMENUS_SELECT_MENU . '</b>&nbsp;';
    $formMenusSelect = new \XoopsFormSelect('', 'options[0]', $options[0], 1, false);
    $formMenusSelect->addOptionArray($menusList);
    $form .= $formMenusSelect->render();
    $form .= "</select>\n&nbsp;<i>" . _MB_MYMENUS_SELECT_MENU_DSC . "</i>\n<br><br>\n";
    // option 1: moduleSkin
    xoops_load('XoopsLists');
    $tempModuleSkinsList = XoopsLists::getDirListAsArray($GLOBALS['xoops']->path("modules/{$mymenus->dirname}/skins/"), '');
    $moduleSkinsList     = [];
    foreach ($tempModuleSkinsList as $key => $moduleSkin) {
        if (file_exists($GLOBALS['xoops']->path("modules/{$mymenus->dirname}/skins/{$moduleSkin}/skin_version.php"))) {
            $moduleSkinsList[$moduleSkin] = $moduleSkin;
        }
    }
    $form                 .= '<b>' . _MB_MYMENUS_SELECT_SKIN . '</b>&nbsp;';
    $formModuleSkinSelect = new \XoopsFormSelect('', 'options[1]', $options[1], 1, false);
    $formModuleSkinSelect->addOptionArray($moduleSkinsList);
    $form .= $formModuleSkinSelect->render();
    $form .= "\n&nbsp;<i>" . _MB_MYMENUS_SELECT_SKIN_DSC . "</i>\n<br><br>\n";
    // option 2: useThemeSkin
    $form                  .= '<b>' . _MB_MYMENUS_USE_THEME_SKIN . '</b>&nbsp;';
    $formUseThemeSkinRadio = new \XoopsFormRadioYN('', 'options[2]', $options[2]);
    $form                  .= $formUseThemeSkinRadio->render();
    $form                  .= "\n&nbsp;<i>" . _MB_MYMENUS_USE_THEME_SKIN_DSC . "</i>\n<br><br>\n";
    // option 3: displayMethod
    $displayMethodsList      = [
        'block'    => _MB_MYMENUS_DISPLAY_METHOD_BLOCK,
        'template' => _MB_MYMENUS_DISPLAY_METHOD_TEMPLATE
    ];
    $form                    .= '<b>' . _MB_MYMENUS_DISPLAY_METHOD . '</b>&nbsp;';
    $formDisplayMethodSelect = new \XoopsFormSelect('', 'options[3]', $options[3], 1);
    $formDisplayMethodSelect->addOptionArray($displayMethodsList);
    $form .= $formDisplayMethodSelect->render();
    $form .= "\n&nbsp;<i>" . sprintf(_MB_MYMENUS_DISPLAY_METHOD_DSC, $mymenus->getConfig('unique_id_prefix')) . "</i>\n<br><br>\n";
    // option 4: unique_id
    if (!$options[4] || ('clone' === Request::getCmd('op', '', 'GET'))) {
        $options[4] = time();
    }
    $form             .= '<b>' . _MB_MYMENUS_UNIQUEID . '</b>&nbsp;';
    $formUniqueIdText = new \XoopsFormText('', 'options[4]', 50, 255, $options[4]);
    $form             .= $formUniqueIdText->render();
    $form             .= "\n&nbsp;<i>" . _MB_MYMENUS_UNIQUEID_DSC . "</i>\n<br><br>\n";
    // option 5: themeSkin
    if (file_exists($GLOBALS['xoops']->path('/themes/' . $GLOBALS['xoopsConfig']['theme_set'] . "/modules/{$mymenus->dirname}/skins/"))) {
        xoops_load('XoopsLists');
        $tempThemeSkinsList = XoopsLists::getDirListAsArray($GLOBALS['xoops']->path('/themes/' . $GLOBALS['xoopsConfig']['theme_set'] . "/modules/{$mymenus->dirname}/skins/"), '');
        if (isset($tempThemeSkinsList)) {
            $themeSkinsList = [];
            foreach ($tempThemeSkinsList as $key => $themeSkin) {
                if (file_exists($GLOBALS['xoops']->path('/themes/' . $GLOBALS['xoopsConfig']['theme_set'] . "/modules/{$mymenus->dirname}/skins/{$themeSkin}/skin_version.php"))) {
                    $themeSkinsList[$themeSkin] = '/themes/' . $GLOBALS['xoopsConfig']['theme_set'] . "/modules/{$mymenus->dirname}/skins/{$themeSkin}";
                }
            }
            $form                .= '<b>' . _MB_MYMENUS_SELECT_SKIN_FROM_THEME . '</b>&nbsp;';
            $formThemeSkinSelect = new \XoopsFormSelect('', 'options[5]', $options[5], 1, false);
            $formThemeSkinSelect->addOptionArray($themeSkinsList);
            $form .= $formThemeSkinSelect->render();
            $form .= "\n&nbsp;&nbsp;<i>" . _MB_MYMENUS_SELECT_SKIN_FROM_THEME_DSC . "</i>\n<br><br>\n";
        }
    }

    return $form;
}
