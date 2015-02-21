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

defined('XOOPS_ROOT_PATH') || exit("Restricted access");

/**
 * @param $options
 *
 * @return array|bool
 */
function mymenus_block_show($options)
{
    $block = array();
    global $xoTheme, $xoopsLogger;
    $xoopsLogger->startTime('MyMenus Block');
    $myts =& MyTextSanitizer::getInstance();

    include_once $GLOBALS['xoops']->path('modules/mymenus/include/functions.php');
    //include_once $GLOBALS['xoops']->path('modules/mymenus/class/registry.php');
    include_once $GLOBALS['xoops']->path('modules/mymenus/class/plugin.php');
    include_once $GLOBALS['xoops']->path('modules/mymenus/class/builder.php');

    $registry =& MymenusRegistry::getInstance();
    $plugin   =& MymenusPlugin::getInstance();
    $plugin->triggerEvent('Boot');

    $menuId = $options[0];

    $linksHandler =& xoops_getModuleHandler('links', 'mymenus');
    $criteria = new CriteriaCompo(new Criteria('mid', $menuId));
    $criteria->setSort('weight');
    $criteria->setOrder('ASC');
    //get menus as an array with ids as keys
    $menus = $linksHandler->getAll($criteria, null, false, false);
    unset($criteria);

    foreach ($menus as $key => $links) {
        $registry->setEntry('menu', $links);
        $registry->setEntry('has_access', 'yes');
        $plugin->triggerEvent('HasAccess');
        if ('no' == $registry->getEntry('has_access')) {
            unset($menus[$key]);
        }
    }

    $count = count($menus);
    if (0 == $count) {
        return $block;
    }

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
    $block   = $builder->render();

    /*--------------------------------------------------------------*/
    //default files to load
    $cssArray = array();
    $jsArray  = array();

    //get extra files from skins
    $skin     = $options[1];
    $skinInfo = mymenus_getSkinInfo($skin, $options[2], $options[3]);

    if (isset($skinInfo['css'])) {
        $cssArray = array_merge($cssArray, $skinInfo['css']);
    }

    if (isset($skinInfo['js'])) {
        $jsArray = array_merge($jsArray, $skinInfo['js']);

    }

    $config = mymenus_getModuleConfig();
    if ('xoopstpl' == $config['assign_method']) {
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

    $blockTpl = new XoopsTpl();
    $blockTpl->assign(array('block' => $block,
                           'config' => $skinInfo['config'],
                          'skinurl' => $skinInfo['url'],
                         'skinpath' => $skinInfo['path'],
                        'xlanguage' => xoops_isActiveModule('xlanguage') ? true : false) // xLanguage check
    );

    /*assign ul class*/
    $menuCss      = '';
    $menusHandler = xoops_getModuleHandler('menus', 'mymenus');
    $menuCriteria = new CriteriaCompo(new Criteria('id', $menuId));
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

    $block['content'] = $blockTpl->fetch($skinInfo['template']);

    if ('template' == $options[3]) {
        $GLOBALS['xoopsTpl']->assign($options[4] , $block['content']);
        $block = false;
    }

    $registry->unsetAll();
    unset($registry, $plugin);
    $xoopsLogger->stopTime('MyMenus Block');

    return $block;
}

/**
 * @param $options
 *
 * @return string
 */
function mymenus_block_edit($options)
{
    //Unique ID
    if (!$options[3] || (isset($_GET['op']) && 'clone' == $_GET['op'])) {
        $options[3] = time();
    }
    $i = 0;
    $menusHandler =& xoops_getModuleHandler('menus', 'mymenus');
    xoops_loadLanguage('admin', 'mymenus');

    $criteria = new CriteriaCompo();
    $criteria->setSort('title');
    $criteria->setOrder('ASC');
    $menus = $menusHandler->getList($criteria);
    unset($criteria);

    if (0 == count($menus)) {
        $form = "<a href='" . $GLOBALS['xoops']->url('modules/mymenus/admin/admin_menus.php') . "'>" . _AM_MYMENUS_MSG_NOMENUS . "</a>";
        return $form;
    }

    xoops_load('XoopsFormLoader');

    // Menu 0
    $form    = "<b>" . _MB_MYMENUS_SELECT_MENU . "</b>&nbsp;";
    $element = new XoopsFormSelect('', "options[{$i}0]", $options[$i], 1);
    $element->addOptionArray($menus);
    $form .= $element->render();
    $form .= "</select>\n&nbsp;&nbsp;<i>" . _MB_MYMENUS_SELECT_MENU_DSC . "</i><br /><br />";

    // Skin 1
    ++$i;
    xoops_load('XoopsLists');
    $temp_skins    = XoopsLists::getDirListAsArray($GLOBALS['xoops']->path("/modules/mymenus/skins/"), "");
    $skins_options = array();
    foreach ($temp_skins as $key => $skin) {
        if (file_exists($GLOBALS['xoops']->path('modules/mymenus/skins/' . $skin . '/skin_version.php'))) {
            $skins_options[$skin] = $skin;
        }
    }
    $form .= "<b>" . _MB_MYMENUS_SELECT_SKIN . "</b>&nbsp;";
    $element = new XoopsFormSelect('', "options[{$i}0]", $options[$i], 1);
    $element->addOptionArray($skins_options);
    $form .= $element->render();
    $form .= "</select>\n&nbsp;&nbsp;<i>" . _MB_MYMENUS_SELECT_SKIN_DSC . "</i><br /><br />";

    // Use skin from theme 2
    ++$i;
    $form .= "<b>" . _MB_MYMENUS_USE_THEME_SKIN . "</b>&nbsp;";
    $element = new XoopsFormRadioYN('', "options[{$i}0]", $options[$i]);
    $form .= $element->render();
    $form .= "</select>\n&nbsp;&nbsp;<i>" . _MB_MYMENUS_USE_THEME_SKIN_DSC . "</i><br /><br />";
/*
<<<<<<< .mine
    // Skin from theme 3 - @luciorota
=======
    //Skin from theme 3 - @luciorota

>>>>>>> .r12414
*/
    if (file_exists($GLOBALS['xoops']->path('/themes/' . $GLOBALS['xoopsConfig']['theme_set'] . '/modules/mymenus/skins/'))) {
        ++$i;
        xoops_load('XoopsLists');
        $temp_theme_skins = XoopsLists::getDirListAsArray($GLOBALS['xoops']->path("/themes/" . $GLOBALS['xoopsConfig']['theme_set'] . "/modules/mymenus/skins/"), "");
        if (isset($temp_theme_skins)) {
            $theme_skins_options = array();
            foreach ($temp_theme_skins as $key => $theme_skin) {
                if (file_exists($GLOBALS['xoops']->path("/themes/" . $GLOBALS['xoopsConfig']['theme_set'] . "/modules/mymenus/skins/{$theme_skin}/skin_version.php"))) {
                    $theme_skins_options[$theme_skin] = "/themes/" . $GLOBALS['xoopsConfig']['theme_set'] . "/modules/mymenus/skins/{$theme_skin}";
                }
            }
            $form .= "<b>" . _MB_MYMENUS_SELECT_SKIN_FROM_THEME . "</b>&nbsp;";
            $element = new XoopsFormSelect('', "options[{$i}0]", $options[$i], 1);
            $element->addOption('', '/themes/' . $GLOBALS['xoopsConfig']['theme_set'] . '/menu');
            $element->addOptionArray($theme_skins_options);
            $form .= $element->render();
            $form .= "</select>\n&nbsp;&nbsp;<i>" . _MB_MYMENUS_SELECT_SKIN_FROM_THEME_DSC . "</i><br /><br />";
        }
    }

    // Display method 4
    ++$i;
    $display_options = array('block' => _MB_MYMENUS_DISPLAY_METHOD_BLOCK,
                          'template' => _MB_MYMENUS_DISPLAY_METHOD_TEMPLATE
    );
    $form .= "<b>" . _MB_MYMENUS_DISPLAY_METHOD . "</b>&nbsp;";
    $element = new XoopsFormSelect('', "options[{$i}0]", $options[$i], 1);
    $element->addOptionArray($display_options);
    $form .= $element->render();
    $form .= "</select>\n&nbsp;&nbsp;<i>" . _MB_MYMENUS_DISPLAY_METHOD_DSC . "</i><br /><br />";

    //Unique ID 5
    ++$i;
    $form .= "<b>" . _MB_MYMENUS_UNIQUEID . "</b>&nbsp;";
    $element = new XoopsFormText('', "options[{$i}0]", 10, 50, $options[$i]);
    $form .= $element->render();
    $form .= "\n&nbsp;&nbsp;<i>" . _MB_MYMENUS_UNIQUEID_DSC . "</i><br /><br />";

    return $form;
}
