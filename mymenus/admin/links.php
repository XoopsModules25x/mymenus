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
 * @author          trabis <lusopoemas@gmail.com>, bleekk <bleekk@outlook.com>
 * @version         $Id: links.php 12644 2014-06-24 21:35:40Z bleekk $
 */

$currentFile = basename(__FILE__);
include_once __DIR__ . '/admin_header.php';

$mymenusTpl = new XoopsTpl();

$criteria = new CriteriaCompo();
$criteria->setSort('id');
$criteria->setOrder('ASC');
$menus_list = $mymenus->getHandler('menus')->getList($criteria);

$indexAdmin = new ModuleAdmin();

if (empty($menus_list)) {
    redirect_header('menus.php', 1, _AM_MYMENUS_MSG_NOMENUS);
    exit;
}

if (isset($_REQUEST['menu_id']) && in_array($_REQUEST['menu_id'], array_keys($menus_list))) {
    $menu_id = $_REQUEST['menu_id'];
    $menu_title = $menus_list[$menu_id];
} else {
    $keys = array_keys($menus_list);
    $menu_id = $keys[0];
    $menu_title = $menus_list[$menu_id];
}

$mymenusTpl->assign('menu_id', $menu_id);
$mymenusTpl->assign('menu_title', $menu_title);
$mymenusTpl->assign('menus_list', $menus_list);

$id = XoopsRequest::getInt('id', null);
$pid = XoopsRequest::getInt('pid', null);

$limit = XoopsRequest::getInt('limit', 15);
$start = XoopsRequest::getInt('start', 0);
$redir = XoopsRequest::getString('redir', null);

$weight = XoopsRequest::getInt('weight', 0);
$visible = XoopsRequest::getBool('visible', false);

$mymenus_adminpage = 'links.php';

$op = XoopsRequest::getString('op', 'list');
switch ($op) {
    case 'add':
        mymenus_admin_add();
        break;
    case 'form':
        //  admin navigation
        xoops_cp_header();
        $indexAdmin = new ModuleAdmin();
        echo $indexAdmin->addNavigation($currentFile);
        echo mymenus_admin_form(null, $pid);
        include 'admin_footer.php';
        break;
    case 'edit':
        echo mymenus_admin_form($id);
        break;
    case 'editok':
        mymenus_admin_edit($id);
        break;
    case 'del':
        mymenus_admin_confirmdel($id, $redir);
        break;
    case 'delok':
        mymenus_admin_del($id, $redir);
        break;
    case 'delall':
        mymenus_admin_confirmdel(null, $redir, 'delallok');
        break;
    case 'delallok':
        mymenus_admin_delall($redir);
        break;
    case 'move':
        //  admin navigation
        xoops_cp_header();
        $indexAdmin = new ModuleAdmin();
        echo $indexAdmin->addNavigation($currentFile);
        mymenus_admin_move($id, $weight);
        echo mymenus_admin_list($start);
        include 'admin_footer.php';
        break;
    case 'toggle':
        mymenus_admin_toggle($id, $visible);
        break;
    case 'order':
        $order = $_POST['mod'];
        parse_str($order,$test);

        $i = 1;
        foreach ($test['mod'] as $order => $value) {

             $linksObj = $mymenus->getHandler('links')->get($order);
             $linksObj->setVar('weight', ++$i);

             /*set submenu*/
             if (isset($value)) {
                $linksObj->setVar('pid', $value);
             } else {
                $linksObj->setVar('pid', 0);
             }
             $mymenus->getHandler('links')->insert($linksObj);
             $mymenus->getHandler('links')->update_weights($linksObj);

        }
        break;
    case 'list':
    default:
        //  admin navigation
        xoops_cp_header();
        $indexAdmin = new ModuleAdmin();
        echo $indexAdmin->addNavigation($currentFile);
        // Add module stylesheet
        $xoTheme->addStylesheet(XOOPS_URL . '/modules/system/css/ui/' . xoops_getModuleOption('jquery_theme', 'system') . '/ui.all.css');
        $xoTheme->addStylesheet(XOOPS_URL . '/modules/mymenus/assets/css/admin.css');
        $xoTheme->addStylesheet(XOOPS_URL . '/Frameworks/moduleclasses/moduleadmin/css/admin.css');
        // Define scripts
        $xoTheme->addScript('browse.php?Frameworks/jquery/plugins/jquery.ui.js');
        $xoTheme->addScript(XOOPS_URL . '/modules/mymenus/assets/js/nestedSortable.js');
        //$xoTheme->addScript(XOOPS_URL . '/modules/mymenus/js/switchButton.js');
        $xoTheme->addScript(XOOPS_URL . '/modules/mymenus/assets/js/order.js');

        echo mymenus_admin_list($start);

        /* Disable xoops debugger in dialog window */
        include_once XOOPS_ROOT_PATH . '/class/logger/xoopslogger.php';
        $xoopsLogger = XoopsLogger::getInstance();
        $xoopsLogger->activated = true;
        error_reporting(-1);

        include 'admin_footer.php';
        break;
}

/**
 * @param int $start
 *
 * @return bool|mixed|string
 */
function mymenus_admin_list($start = 0)
{
    $mymenus = MymenusMymenus::getInstance();
    global $mymenusTpl, $menu_id;
    //
    $linksCriteria = new CriteriaCompo(new Criteria('mid', $menu_id));
    $linksCount = $mymenus->getHandler('links')->getCount($linksCriteria);
    $mymenusTpl->assign('count', $linksCount);
    //
    $linksCriteria->setSort('weight');
    $linksCriteria->setOrder('ASC');
    if ($linksCount > 0) {
        $linksObjs = $mymenus->getHandler('links')->getObjects($linksCriteria);
        foreach ($linksObjs as $linksObj) {
            $array[] = $linksObj->getValues();
        }
        include_once $GLOBALS['xoops']->path('modules/mymenus/class/builder.php');
        $builder = new MymenusBuilder($array);
        $menusArray = $builder->render();
        $mymenusTpl->assign('menus', $menusArray);
    }
    //
    $mymenusTpl->assign('addform', mymenus_admin_form());
    return $mymenusTpl->fetch($GLOBALS['xoops']->path('modules/mymenus/templates/static/mymenus_admin_links.html'));
}

/**
 * @param      $id
 * @param null $redir
 */
function mymenus_admin_del($id, $redir = null)
{
    $mymenus = MymenusMymenus::getInstance();
    //
    if (!$GLOBALS['xoopsSecurity']->check()) {
        redirect_header($GLOBALS['mymenus_adminpage'], 1, implode('<br />', $GLOBALS['xoopsSecurity']->getErrors()));
    }
    if ($id <= 0) {
        redirect_header($GLOBALS['mymenus_adminpage'], 1);
    }
    //
    $linksObj = $mymenus->getHandler('links')->get($id);
    if (!is_object($linksObj)) {
        redirect_header($GLOBALS['mymenus_adminpage'], 1);
    }
    //get sub item
    $criteria = new CriteriaCompo();
    $criteria->add(new Criteria('id', $id));
    $criteria->add(new Criteria('pid', $id),'OR');

    //first delete links level 2
    global $xoopsDB;
    $query = "DELETE FROM " . $xoopsDB->prefix("mymenus_links");
    $query .= " WHERE pid = (SELECT id FROM (SELECT * FROM " . $xoopsDB->prefix("mymenus_links") . " WHERE pid = " . $id . ") AS sec);";
    $result = $xoopsDB->queryF($query);
    //delete links level 0 and 1
    if (!$mymenus->getHandler('links')->deleteAll($criteria)) {
        xoops_cp_header();
        xoops_error(_AM_MYMENUS_MSG_ERROR, $linksObj->getVar('id'));
        xoops_cp_footer();
        exit();
    }
    redirect_header(!is_null($redir) ? base64_decode($redir) : $GLOBALS['mymenus_adminpage'] , 2, _AM_MYMENUS_MSG_SUCCESS);
}

/**
 * @param null $redir
 */
function mymenus_admin_delall($redir = null)
{
    $mymenus = MymenusMymenus::getInstance();
    //
    if (!$GLOBALS['xoopsSecurity']->check()) {
        redirect_header($GLOBALS['mymenus_adminpage'], 1, implode('<br />', $GLOBALS['xoopsSecurity']->getErrors()));
    }
    if (!$mymenus->getHandler('links')->deleteAll()) {
        redirect_header(!is_null($redir) ? base64_decode($redir) : $GLOBALS['mymenus_adminpage'] , 2, _AM_MYMENUS_MSG_ERROR);
    }
    redirect_header(!is_null($redir) ? base64_decode($redir) : $GLOBALS['mymenus_adminpage'] , 2, _AM_MYMENUS_MSG_SUCCESS);
}

/**
 * @param null   $id
 * @param null   $redir
 * @param string $op
 */
function mymenus_admin_confirmdel($id = null, $redir = null, $op = 'delok')
{
    $mymenus = MymenusMymenus::getInstance();
    //
    $arr = array();
    $arr['op'] = $op;
    $arr['id'] = $id;
    if (!is_null($redir)) {
        $arr['redir'] = $redir;
    }

    xoops_cp_header();
    xoops_confirm($arr, $GLOBALS['mymenus_adminpage'], _AM_MYMENUS_MSG_AYSL);
    xoops_cp_footer();
}

function mymenus_admin_add()
{
    $mymenus = MymenusMymenus::getInstance();
    //
    if (!$GLOBALS['xoopsSecurity']->check()) {
        redirect_header($GLOBALS['mymenus_adminpage'], 3, implode('<br />', $GLOBALS['xoopsSecurity']->getErrors()));
    }
    //
    $linksCriteria = new CriteriaCompo(new Criteria('mid', $_POST['mid']));
    $linksCriteria->setSort('weight');
    $linksCriteria->setOrder('DESC');
    $linksCriteria->setLimit(1);
    $linksObjs = $mymenus->getHandler('links')->getObjects($linksCriteria);
    $weight = 1;
    if (isset($linksObjs[0]) && is_object($linksObjs[0])) {
        $weight = $linksObjs[0]->getVar('weight') + 1;
    }
    //
    $newLinksObj = $mymenus->getHandler('links')->create();
    if (!isset($_POST['hooks'])) {
        $_POST['hooks'] = array();
    }
    $newLinksObj->setVars($_POST);
    $newLinksObj->setVar('weight', $weight);

    if (!$mymenus->getHandler('links')->insert($newLinksObj)) {
        $msg = _AM_MYMENUS_MSG_ERROR;
    } else {
        $mymenus->getHandler('links')->update_weights($newLinksObj);
        $msg = _AM_MYMENUS_MSG_SUCCESS;
    }
    redirect_header($GLOBALS['mymenus_adminpage'] . '?op=list&amp;menu_id=' . $newLinksObj->getVar('mid'), 2, $msg);
}

/**
 * @param $id
 */
function mymenus_admin_edit($id)
{
    global $menu_id;
    $mymenus = MymenusMymenus::getInstance();
    // Disable xoops debugger in dialog window
    include_once XOOPS_ROOT_PATH . '/class/logger/xoopslogger.php';
    $xoopsLogger = XoopsLogger::getInstance();
    $xoopsLogger->activated = false;
    error_reporting(0);
    //
    if (!$GLOBALS['xoopsSecurity']->check()) {
        redirect_header($GLOBALS['mymenus_adminpage'], 3, implode('<br />', $GLOBALS['xoopsSecurity']->getErrors()));
    }
    //
    $linksObj = $mymenus->getHandler('links')->get($id);
    $linksObj->setVars($_POST);
    //
    if (!$mymenus->getHandler('links')->insert($linksObj)) {
        $msg = _AM_MYMENUS_MSG_ERROR;
    } else {
        $msg = _AM_MYMENUS_MSG_SUCCESS;
    }
    redirect_header($GLOBALS['mymenus_adminpage'] . "?op=list&menu_id=$menu_id", 2, $msg);
}

/**
 * @param null $id
 * @param null $pid
 *
 * @return string
 */
function mymenus_admin_form($id = null, $pid = null)
{
	global $menu_id;
    $mymenus = MymenusMymenus::getInstance();
    // Disable xoops debugger in dialog window
    include_once XOOPS_ROOT_PATH.'/class/logger/xoopslogger.php';
    $xoopsLogger = XoopsLogger::getInstance();
    $xoopsLogger->activated = false;
    error_reporting(0);
    //
    $registry = MymenusRegistry::getInstance();
    $plugin = MymenusPlugin::getInstance();

    $linksObjArray = array();

    if (isset($id)) {
        $ftitle = _EDIT;
        $linksObj = $mymenus->getHandler('links')->get($id);
        $linksObjArray = $linksObj->getValues();

    } else {
        $formTitle = _ADD;
        $linksObj = $mymenus->getHandler('links')->create();
        $linksObjArray = $linksObj->getValues();
        if (isset($pid)) {
            $linksObjArray['pid'] = $pid;
        }
    }
	if (isset($linksObjArray['mid'])) {
		$menu_id = $linksObjArray['mid'];
	}

    $form = new XoopsThemeForm($formTitle, 'admin_form', $GLOBALS['mymenus_adminpage'], "post", true);
    // links: title
    $form_title_text = new XoopsFormText(_AM_MYMENUS_MENU_TITLE, 'title', 50, 255, $linksObjArray['title']);
    $form->addElement($form_title_text, true);
    // links: alt_title
    $form_alt_title_text = new XoopsFormText(_AM_MYMENUS_MENU_ALTTITLE, 'alt_title', 50, 255, $linksObjArray['alt_title']);
    $form->addElement($form_alt_title_text);
    // links: link
    $form_link_text = new XoopsFormText(_AM_MYMENUS_MENU_LINK, 'link', 50, 255, $linksObjArray['link']);
    $form->addElement($form_link_text);
    // links: image
    $form_image_text = new XoopsFormText(_AM_MYMENUS_MENU_IMAGE, 'image', 50, 255, $linksObjArray['image']);
    $form->addElement($form_image_text);
/*
    $criteria = new CriteriaCompo(new Criteria('mid', $menu_id));
    $criteria->add(new Criteria('id', $id, '<>'));
    $criteria->setSort('weight');
    $criteria->setOrder('ASC');
    ...
    $form->addElement($form_parent);
*/
    // links: visible
    $form_visible_radio = new XoopsFormRadioYN(_AM_MYMENUS_MENU_VISIBLE, 'visible', $linksObjArray['visible']);
    $form->addElement($form_visible_radio);
    // links: target
    $form_target_select = new XoopsFormSelect(_AM_MYMENUS_MENU_TARGET, "target", $linksObjArray['target']);
    $form_target_select->addOption("_self", _AM_MYMENUS_MENU_TARG_SELF);
    $form_target_select->addOption("_blank", _AM_MYMENUS_MENU_TARG_BLANK);
    $form_target_select->addOption("_parent", _AM_MYMENUS_MENU_TARG_PARENT);
    $form_target_select->addOption("_top", _AM_MYMENUS_MENU_TARG_TOP);
    $form->addElement($form_target_select);
    // links: groups
    $form_groups_select = new XoopsFormSelectGroup(_AM_MYMENUS_MENU_GROUPS, "groups", true, $linksObjArray['groups'], 5, true);
    $form_groups_select->setDescription(_AM_MYMENUS_MENU_GROUPS_HELP);
    $form->addElement($form_groups_select);
/*
    // links: hooks
    $form->addElement($form_hooks);
*/
    // links: css
    $form_css  = new XoopsFormText(_AM_MYMENUS_MENU_CSS, 'css', 50, 255, $linksObjArray['css']);
    $form->addElement($form_css);
    // form: button tray
    $button_tray = new XoopsFormElementTray('' ,'');
    $button_tray->addElement(new XoopsFormButton('', 'submit_button', _SUBMIT, 'submit'));
    $button = new XoopsFormButton('', 'reset', _CANCEL, 'button');
    if (isset($id)) {
        $button->setExtra('onclick="document.location.href=\'' . $GLOBALS['mymenus_adminpage'] . '?op=list&amp;menu_id=' . $menu_id . ' \'"');
    } else {
        $button->setExtra('onclick="document.getElementById(\'addform\').style.display = \'none\'; return false;"');
    }
    $button_tray->addElement($button);
    $form->addElement($button_tray);

    if (isset($id)) {
        $form->addElement(new XoopsFormHidden('op', 'editok'));
        $form->addElement(new XoopsFormHidden('id', $id));
    } else {
        $form->addElement(new XoopsFormHidden('op', 'add'));
    }
    $form->addElement(new XoopsFormHidden('mid', $menu_id));
    $form->addElement(new XoopsFormHidden('menu_id', $menu_id));

    return $form->render();
}

/**
 * @param $id
 * @param $weight
 */
function mymenus_admin_move($id, $weight)
{
    $mymenus = MymenusMymenus::getInstance();
    //
    $linksObj = $mymenus->getHandler('links')->get($id);
    $linksObj->setVar('weight', $weight);
    $mymenus->getHandler('links')->insert($linksObj);
    $mymenus->getHandler('links')->update_weights($linksObj);
}

/**
 * @param $id
 * @param $visible
 */
function mymenus_admin_toggle($id, $visible)
{
    $mymenus = MymenusMymenus::getInstance();
    // Disable xoops debugger in dialog window
    include_once XOOPS_ROOT_PATH.'/class/logger/xoopslogger.php';
    $xoopsLogger = XoopsLogger::getInstance();
    $xoopsLogger->activated = true;
    error_reporting(0);
    //
    $linksObj = $mymenus->getHandler('links')->get($id);
    $visible = ($linksObj->getVar('visible') == 1) ? 0 : 1;
    $linksObj->setVar('visible', $visible);
    $mymenus->getHandler('links')->insert($linksObj);
    echo $linksObj->getVar('visible');
}
