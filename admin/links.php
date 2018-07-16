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
 * @author          trabis <lusopoemas@gmail.com>, bleekk <bleekk@outlook.com>
 */

use Xmf\Request;
use XoopsModules\Mymenus;

require __DIR__ . '/admin_header.php';

$currentFile = basename(__FILE__);

$mymenusTpl       = new \XoopsTpl(); // will be removed???
$mymenusAdminPage = 'links.php'; // will be removed???

$menusCriteria = new \CriteriaCompo();
$menusCriteria->setSort('id');
$menusCriteria->setOrder('ASC');
$menusList = $helper->getHandler('Menus')->getList($menusCriteria);
if (!$menusList) {
    redirect_header('menus.php', 1, _AM_MYMENUS_MSG_NOMENUS);
}

$valid_menu_ids = array_keys($menusList);
$mid            = Request::getInt('mid', Request::getInt('mid', '', 'POST'), 'GET');
if ($mid && in_array($mid, $valid_menu_ids)) {
    $menuTitle = $menusList[$mid];
} else {
    $keys      = array_keys($menusList);
    $mid       = $valid_menu_ids[0]; //force menu id to first valid menu id in the list
    $menuTitle = $menusList[$mid]; // and get it's title
}
$mymenusTpl->assign('mid', $mid);
$mymenusTpl->assign('menuTitle', $menuTitle);
$mymenusTpl->assign('menus_list', $menusList);

$id      = Request::getInt('id', 0);
$pid     = Request::getInt('pid', 0);
$start   = Request::getInt('start', 0);
$weight  = Request::getInt('weight', 0);
$visible = Request::getInt('visible', 0);

$op = Request::getString('op', 'list');
switch ($op) {

/*
        case 'form':
            xoops_cp_header();
            $adminObject  = \Xmf\Module\Admin::getInstance();
            $adminObject->displayNavigation($currentFile);
            //
            echo editLink(null, $pid, $mid);
            //
            require __DIR__   . '/admin_footer.php';
            break;
*/

    case 'edit':
        echo Mymenus\LinksUtility::editLink($id, null, $mid);
        break;

    case 'add':
        Mymenus\LinksUtility::addLink($mid);
        break;

    case 'save':
        Mymenus\LinksUtility::saveLink($id, $mid);
        break;

    case 'delete':
        $id       = Request::getInt('id', null);
        $linksObj = $helper->getHandler('Links')->get($id);
        if (true === Request::getBool('ok', false, 'POST')) {
            if (!$GLOBALS['xoopsSecurity']->check()) {
                redirect_header($currentFile, 3, implode(',', $GLOBALS['xoopsSecurity']->getErrors()));
            }
            //get sub item
            $linksCriteria = new \CriteriaCompo();
            $linksCriteria->add(new \Criteria('id', $id));
            $linksCriteria->add(new \Criteria('pid', $id), 'OR');
            //first delete links level 2
            $query  = 'DELETE FROM ' . $GLOBALS['xoopsDB']->prefix('mymenus_links');
            $query  .= ' WHERE pid = (SELECT id FROM (SELECT * FROM ' . $GLOBALS['xoopsDB']->prefix('mymenus_links') . " WHERE pid = {$id}) AS sec);";
            $result = $GLOBALS['xoopsDB']->queryF($query);
            //delete links level 0 and 1
            if (!$helper->getHandler('Links')->deleteAll($linksCriteria)) {
                xoops_cp_header();
                xoops_error(_AM_MYMENUS_MSG_ERROR, $linksObj->getVar('id'));
                xoops_cp_footer();
                exit();
            }
            redirect_header($currentFile, 3, _AM_MYMENUS_MSG_DELETE_LINK_SUCCESS);
        } else {
            xoops_cp_header();
            xoops_confirm(['ok' => true, 'id' => $id, 'op' => 'delete'], //                $_SERVER['REQUEST_URI'],
                          Request::getString('REQUEST_URI', '', 'SERVER'), sprintf(_AM_MYMENUS_LINKS_SUREDEL, $linksObj->getVar('title')));
            require __DIR__ . '/admin_footer.php';
        }
        break;

    case 'move':
        xoops_cp_header();
        $adminObject = \Xmf\Module\Admin::getInstance();
        $adminObject->displayNavigation($currentFile);
        //
        Mymenus\LinksUtility::moveLink($id, $weight);
        echo Mymenus\LinksUtility::listLinks($start, $mid);
        //
        require __DIR__   . '/admin_footer.php';
        break;

    case 'toggle':
        Mymenus\LinksUtility::toggleLinkVisibility($id, $visible);
        break;

    case 'order':
        $test  = [];
        $order = Request::getString('mod', '', 'POST');
        parse_str($order, $test);
        $i = 1;
        foreach ($test['mod'] as $order => $value) {
            $linksObj = $helper->getHandler('Links')->get($order);
            $linksObj->setVar('weight', ++$i);
            // Set submenu
            if (isset($value)) {
                $linksObj->setVar('pid', $value);
            } else {
                $linksObj->setVar('pid', 0);
            }
            $helper->getHandler('Links')->insert($linksObj);
            $helper->getHandler('Links')->updateWeights($linksObj);
        }
        break;

    case 'list':
    default:
        xoops_cp_header();
        $adminObject = \Xmf\Module\Admin::getInstance();
        $adminObject->displayNavigation($currentFile);
        // Add module stylesheet
        $GLOBALS['xoTheme']->addStylesheet(XOOPS_URL . '/modules/system/css/ui/' . xoops_getModuleOption('jquery_theme', 'system') . '/ui.all.css');
        $GLOBALS['xoTheme']->addStylesheet(XOOPS_URL . "/modules/{$helper->getDirname()}/assets/css/admin.css");
        $GLOBALS['xoTheme']->addStylesheet(XOOPS_URL . '/Frameworks/moduleclasses/moduleadmin/css/admin.css');
        // Define scripts
        $GLOBALS['xoTheme']->addScript('browse.php?Frameworks/jquery/plugins/jquery.ui.js');
        $GLOBALS['xoTheme']->addScript(XOOPS_URL . "/modules/{$helper->getDirname()}/assets/js/nestedSortable.js");
        //$GLOBALS['xoTheme']->addScript(XOOPS_URL . '/modules/{$mymenus->dirname}/assets/js/switchButton.js');
        $GLOBALS['xoTheme']->addScript(XOOPS_URL . "/modules/{$helper->getDirname()}/assets/js/links.js");
        echo Mymenus\LinksUtility::listLinks($start, $mid);
        // Disable xoops debugger in dialog window
        require $GLOBALS['xoops']->path('/class/logger/xoopslogger.php');
        $xoopsLogger            = XoopsLogger::getInstance();
        $xoopsLogger->activated = true;
        error_reporting(-1);
        //
        require __DIR__   . '/admin_footer.php';
        break;
}
