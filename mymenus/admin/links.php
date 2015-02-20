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

$menus_handler = xoops_getModuleHandler('menus', 'mymenus');
$criteria = new CriteriaCompo();
$criteria->setSort('id');
$criteria->setOrder('ASC');
$menus_list = $menus_handler->getList($criteria);

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
        $links_handler = xoops_getModuleHandler('links', 'mymenus');
        foreach ($test['mod'] as $order => $value) {

             $linksObj = $links_handler->get($order);
             $linksObj->setVar('weight', ++$i);

             /*set submenu*/
             if (isset($value)) {
                $linksObj->setVar('pid', $value);
             } else {
                $linksObj->setVar('pid', 0);
             }
             $links_handler->insert($linksObj);
             $links_handler->update_weights($linksObj);

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
    global $mymenusTpl, $menu_id;

    $links_handler = xoops_getModuleHandler('links', 'mymenus');

    $criteria = new CriteriaCompo(new Criteria('mid', $menu_id));

    $count = $links_handler->getCount($criteria);
    $mymenusTpl->assign('count', $count);
    $criteria->setSort('weight');
    $criteria->setOrder('ASC');

    $menus = array();
    $menusArray = array();
    if ($count > 0) {
        $menus = $links_handler->getObjects($criteria);
        foreach ($menus as $menu) {
            $array[] = $menu->getValues();
        }
        include_once $GLOBALS['xoops']->path('modules/mymenus/class/builder.php');
        $builder = new MymenusBuilder($array);
        $menusArray = $builder->render();
        $mymenusTpl->assign('menus', $menusArray);
    }

    $mymenusTpl->assign('addform', mymenus_admin_form());

    return $mymenusTpl->fetch($GLOBALS['xoops']->path('modules/mymenus/templates/static/mymenus_admin_links.html'));
}

/**
 * @param      $id
 * @param null $redir
 */
function mymenus_admin_del($id, $redir = null)
{
    if (!$GLOBALS['xoopsSecurity']->check()) {
        redirect_header($GLOBALS['mymenus_adminpage'], 1, implode('<br />', $GLOBALS['xoopsSecurity']->getErrors()));
    }

    if ($id <= 0) {
        redirect_header($GLOBALS['mymenus_adminpage'], 1);
    }

    $links_handler = xoops_getModuleHandler('links' , 'mymenus');
    $linksObj = $links_handler->get($id);
    if (!is_object($linksObj)) {
        redirect_header($GLOBALS['mymenus_adminpage'], 1);
    }

    //get sub item
    $criteria = new CriteriaCompo();
    $criteria->add(new Criteria('id', $id));
    $criteria->add(new Criteria('pid', $id),'OR');

    //first delete links level 2
    global $xoopsDB;
    $query = "DELETE FROM " . $xoopsDB->prefix("mymenus_links") . " WHERE pid = (
    SELECT id FROM (
    SELECT * FROM " . $xoopsDB->prefix("mymenus_links") . " WHERE pid = " . $id . ") AS sec
    );";
    $result = $xoopsDB->queryF($query);
    //delete links level 0 and 1
    if (!$links_handler->deleteAll($criteria)) {
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
    if (!$GLOBALS['xoopsSecurity']->check()) {
        redirect_header($GLOBALS['mymenus_adminpage'], 1, implode('<br />', $GLOBALS['xoopsSecurity']->getErrors()));
    }

    $links_handler = xoops_getModuleHandler('links' , 'mymenus');

    if (!$links_handler->deleteAll()) {
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
    if (!$GLOBALS['xoopsSecurity']->check()) {
        redirect_header($GLOBALS['mymenus_adminpage'], 3, implode('<br />', $GLOBALS['xoopsSecurity']->getErrors()));
    }

    $links_handler = xoops_getModuleHandler('links','mymenus');
    $criteria = new CriteriaCompo(new Criteria('mid', $_POST['mid']));
    $criteria->setSort('weight');
    $criteria->setOrder('DESC');
    $criteria->setLimit(1);
    $menus = $links_handler->getObjects($criteria);
    $weight = 1;
    if (isset($menus[0]) && is_object($menus[0])) {
        $weight = $menus[0]->getVar('weight') + 1;
    }

    $linksObj = $links_handler->create();
    if (!isset($_POST['hooks'])) {
        $_POST['hooks'] = array();
    }
    $linksObj->setVars($_POST);
    $linksObj->setVar('weight', $weight);

    if (!$links_handler->insert($linksObj)) {
        $msg = _AM_MYMENUS_MSG_ERROR;
    } else {
        $links_handler->update_weights($linksObj);
        $msg = _AM_MYMENUS_MSG_SUCCESS;
    }

    redirect_header($GLOBALS['mymenus_adminpage'] . '?op=list&amp;menu_id=' . $linksObj->getVar('mid'), 2, $msg);
}

/**
 * @param $id
 */
function mymenus_admin_edit($id)
{
    if (!$GLOBALS['xoopsSecurity']->check()) {
        redirect_header($GLOBALS['mymenus_adminpage'], 3, implode('<br />', $GLOBALS['xoopsSecurity']->getErrors()));
    }
    global $menu_id;

    /* Disable xoops debugger in dialog window */
    include_once XOOPS_ROOT_PATH . '/class/logger/xoopslogger.php';
    $xoopsLogger = XoopsLogger::getInstance();
    $xoopsLogger->activated = false;
    error_reporting(0);

    $links_handler = xoops_getModuleHandler('links','mymenus');
    $linksObj = $links_handler->get($id);
    $linksObj->setVars($_POST);

    if (!$links_handler->insert($linksObj)) {
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
    /* Disable xoops debugger in dialog window */
    include_once XOOPS_ROOT_PATH.'/class/logger/xoopslogger.php';
    $xoopsLogger = XoopsLogger::getInstance();
    $xoopsLogger->activated = false;
    error_reporting(0);
	
	global $menu_id;
		
    $registry = MymenusRegistry::getInstance();
    $plugin = MymenusPlugin::getInstance();

    $links_handler = xoops_getModuleHandler('links','mymenus');
    $linksObjArray = array();

    if (isset($id)) {
        $ftitle = _EDIT;
        $linksObj = $links_handler->get($id);
        $linksObjArray = $linksObj->getValues();

    } else {
        $ftitle = _ADD;
        $linksObj = $links_handler->create();
        $linksObjArray = $linksObj->getValues();
        if (isset($pid)) {
            $linksObjArray['pid'] = $pid;
        }
    }
	if (isset($linksObjArray['mid'])) {
		$menu_id = $linksObjArray['mid'];
	}

    $form = new XoopsThemeForm($ftitle, 'admin_form', $GLOBALS['mymenus_adminpage'], "post", true);
    $form_title = new XoopsFormText(_AM_MYMENUS_MENU_TITLE, 'title', 50, 255, $linksObjArray['title']);
    $form_alt_title = new XoopsFormText(_AM_MYMENUS_MENU_ALTTITLE, 'alt_title', 50, 255, $linksObjArray['alt_title']);

    $form_link  = new XoopsFormText(_AM_MYMENUS_MENU_LINK, 'link', 50, 255, $linksObjArray['link']);
    $form_image  = new XoopsFormText(_AM_MYMENUS_MENU_IMAGE, 'image', 50, 255, $linksObjArray['image']);

    $criteria = new CriteriaCompo(new Criteria('mid', $menu_id));
    $criteria->add(new Criteria('id', $id, '<>'));
    $criteria->setSort('weight');
    $criteria->setOrder('ASC');

    global $xoopsModule;
    $pathIcon16 = '../'.$xoopsModule->getInfo('icons16');

    $statontxt = "&nbsp;<img src=". $pathIcon16 .'/1.png'.' '. "alt='" ._YES . "' />&nbsp;" . _YES . "&nbsp;&nbsp;&nbsp;";
    $statofftxt = "&nbsp;<img src=". $pathIcon16 .'/0.png' .' '."alt='" . _NO . "' />&nbsp;" . _NO . "&nbsp;";
    $form_visible = new XoopsFormRadioYN(_AM_MYMENUS_MENU_VISIBLE, 'visible', $linksObjArray['visible'], $statontxt, $statofftxt);
//---------------mamba

    $form_target = new XoopsFormSelect(_AM_MYMENUS_MENU_TARGET, "target", $linksObjArray['target']);
    $form_target->addOption("_self", _AM_MYMENUS_MENU_TARG_SELF);
    $form_target->addOption("_blank", _AM_MYMENUS_MENU_TARG_BLANK);
    $form_target->addOption("_parent", _AM_MYMENUS_MENU_TARG_PARENT);
    $form_target->addOption("_top", _AM_MYMENUS_MENU_TARG_TOP);

    $form_groups = new XoopsFormSelectGroup(_AM_MYMENUS_MENU_GROUPS, "groups", true, $linksObjArray['groups'], 5, true);
    $form_groups->setDescription(_AM_MYMENUS_MENU_GROUPS_HELP);

    $form_css  = new XoopsFormText(_AM_MYMENUS_MENU_CSS, 'css', 50, 255, $linksObjArray['css']);

    $form->addElement($form_title, true);
    $form->addElement($form_alt_title);
    $form->addElement($form_link);
    $form->addElement($form_image);
    $form->addElement($form_parent);
    $form->addElement($form_visible);
    $form->addElement($form_target);
    $form->addElement($form_groups);
    $form->addElement($form_hooks);
    $form->addElement($form_css);

    $tray = new XoopsFormElementTray('' ,'');
    $tray->addElement(new XoopsFormButton('', 'submit_button', _SUBMIT, 'submit'));

    $btn = new XoopsFormButton('', 'reset', _CANCEL, 'button');

    if (isset($id)) {
        $btn->setExtra('onclick="document.location.href=\'' . $GLOBALS['mymenus_adminpage'] . '?op=list&amp;menu_id=' . $menu_id . ' \'"');
    } else {
        $btn->setExtra('onclick="document.getElementById(\'addform\').style.display = \'none\'; return false;"');
    }

    $tray->addElement($btn);
    $form->addElement($tray);

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
    $links_handler = xoops_getModuleHandler('links', 'mymenus');
    $linksObj = $links_handler->get($id);
    $linksObj->setVar('weight', $weight);
    $links_handler->insert($linksObj);
    $links_handler->update_weights($linksObj);
}

/**
 * @param $id
 * @param $visible
 */
function mymenus_admin_toggle($id, $visible)
{
    include_once XOOPS_ROOT_PATH.'/class/logger/xoopslogger.php';
    $xoopsLogger = XoopsLogger::getInstance();
    $xoopsLogger->activated = true;
    error_reporting(0);

    $links_handler = xoops_getModuleHandler('links', 'mymenus');
    $linksObj = $links_handler->get($id);
    $visible = ($linksObj->getVar('visible') == 1) ? 0 : 1;
    $linksObj->setVar('visible', $visible);
    $links_handler->insert($linksObj);
    echo $linksObj->getVar('visible');
}
