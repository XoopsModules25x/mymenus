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
 * @version         $Id: admin_menu.php 0 2010-07-21 18:47:04Z trabis $
 */

include_once dirname(__FILE__) . '/admin_header.php';

$menus_handler =& xoops_getModuleHandler('menus', 'mymenus');
$criteria = new CriteriaCompo();
$criteria->setSort('title');
$criteria->setOrder('ASC');
$menus_list = $menus_handler->getList($criteria);

$indexAdmin = new ModuleAdmin();

if (empty($menus_list)) {
    redirect_header('admin_menus.php', 1, _AM_MYMENUS_MSG_NOMENUS);
    exit;
}

if (isset($_REQUEST['menu_id']) && in_array($_REQUEST['menu_id'], array_keys($menus_list))){
    $menu_id = $_REQUEST['menu_id'];
    $menu_title = $menus_list[$menu_id];
}  else {
    $keys = array_keys($menus_list);
    $menu_id = $keys[0];
    $menu_title = $menus_list[$menu_id];
}

$mymenusTpl->assign('menu_id', $menu_id);
$mymenusTpl->assign('menu_title', $menu_title);
$mymenusTpl->assign('menus_list', $menus_list);


$op = isset($_GET['op']) ? trim($_GET['op']) : (isset($_POST['op']) ? trim($_POST['op']) : 'list');

$id = isset($_GET['id']) ? intval($_GET['id']) : (isset($_POST['id']) ? intval($_POST['id']) : null);
$pid = isset($_GET['pid']) ? intval($_GET['pid']) : (isset($_POST['pid']) ? intval($_POST['pid']) : null);

$limit = isset($_GET['limit']) ? intval($_GET['limit']) : (isset($_POST['limit']) ? intval($_POST['limit']) : 15);
$start = isset($_GET['start']) ? intval($_GET['start']) : (isset($_POST['start']) ? intval($_POST['start']) : 0);
$redir = isset($_GET['redir']) ? $_GET['redir'] : (isset($_POST['redir']) ? $_POST['redir'] : null);

$weight = isset($_GET['weight']) ? intval($_GET['weight']) : (isset($_POST['weight']) ? intval($_POST['weight']) : 0);
$visible = isset($_GET['visible']) ? intval($_GET['visible']) : (isset($_POST['visible']) ? intval($_POST['visible']) : 0);

$mymenus_adminpage = 'admin_menu.php';

switch ($op) {
    case 'add':
        mymenus_admin_add();
        break;
    case 'form':
        xoops_cp_header();
        echo $indexAdmin->addNavigation('admin_menu.php');
        echo mymenus_admin_form(null, $pid);
        include 'admin_footer.php';
        break;
    case 'edit':
        xoops_cp_header();
        echo $indexAdmin->addNavigation('admin_menu.php');
        echo mymenus_admin_form($id);
        include 'admin_footer.php';
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
        xoops_cp_header();
        echo $indexAdmin->addNavigation('admin_menu.php');
        mymenus_admin_move($id, $weight);
        echo mymenus_admin_list($start);
        include 'admin_footer.php';
        break;
    case 'toggle':
        xoops_cp_header();
        echo $indexAdmin->addNavigation('admin_menu.php');
        mymenus_admin_toggle($id, $visible);
        echo mymenus_admin_list($start);
        include 'admin_footer.php';
        break;
    case 'list':
    default:
        xoops_cp_header();
        echo $indexAdmin->addNavigation('admin_menu.php');
        echo mymenus_admin_list($start);
        include 'admin_footer.php';
        break;
}

function mymenus_admin_list($start = 0)
{
    global $mymenusTpl, $menu_id;

    $this_handler =& xoops_getModuleHandler('menu', 'mymenus');

    $criteria = new CriteriaCompo(new Criteria('mid', $menu_id));
    //$criteria->add(new Criteria('pid', 0));

    $count = $this_handler->getCount($criteria);
    $mymenusTpl->assign('count', $count);
    $criteria->setSort('weight');
    $criteria->setOrder('ASC');

    $menus = array();
    $menusArray = array();
    if ($count > 0) {
        $menus = $this_handler->getObjects($criteria);
        foreach ($menus as $menu) {
            $array[] = $menu->getValues();
        }
        include_once $GLOBALS['xoops']->path('modules/mymenus/class/builder.php');
        $builder = new MymenusBuilder($array);
        $menusArray = $builder->render();
        $mymenusTpl->assign('menus', $menusArray);
    }

    $mymenusTpl->assign('addform', mymenus_admin_form());

    return $mymenusTpl->fetch($GLOBALS['xoops']->path('modules/mymenus/templates/static/mymenus_admin_menu.html'));
}

function mymenus_admin_del($id, $redir = null)
{
    if (!$GLOBALS['xoopsSecurity']->check()) {
        redirect_header($GLOBALS['mymenus_adminpage'], 1, implode('<br />', $GLOBALS['xoopsSecurity']->getErrors()));
    }

    if ($id <= 0) {
        redirect_header($GLOBALS['mymenus_adminpage'], 1);
    }

    $this_handler =& xoops_getModuleHandler('menu' , 'mymenus');
    $obj = $this_handler->get($id);
    if (!is_object($obj)) {
        redirect_header($GLOBALS['mymenus_adminpage'], 1);
    }

    if (!$this_handler->delete($obj)) {
        xoops_cp_header();
        xoops_error(_AM_MYMENUS_MSG_ERROR, $obj->getVar('id'));
        xoops_cp_footer();
        exit();
    }

    redirect_header(!is_null($redir) ? base64_decode($redir) : $GLOBALS['mymenus_adminpage'] , 2, _AM_MYMENUS_MSG_SUCCESS);
}

function mymenus_admin_delall($redir = null)
{
    if (!$GLOBALS['xoopsSecurity']->check()) {
        redirect_header($GLOBALS['mymenus_adminpage'], 1, implode('<br />', $GLOBALS['xoopsSecurity']->getErrors()));
    }

    $this_handler =& xoops_getModuleHandler('menu' , 'mymenus');

    if (!$this_handler->deleteAll()) {
        redirect_header(!is_null($redir) ? base64_decode($redir) : $GLOBALS['mymenus_adminpage'] , 2, _AM_MYMENUS_ERROR);
    }

    redirect_header(!is_null($redir) ? base64_decode($redir) : $GLOBALS['mymenus_adminpage'] , 2, _AM_MYMENUS_SUCCESS);
}

function mymenus_admin_confirmdel($id = null, $redir = null, $op = 'delok')
{

    $arr = array();
    $arr['op'] = $op;
    $arr['id'] = $id;
    if (!is_null($redir)){
        $arr['redir'] = $redir;
    }

    xoops_cp_header();
    xoops_confirm($arr, $GLOBALS['mymenus_adminpage'], _AM_MYMENUS_MSG_AYS);
    xoops_cp_footer();
}


function mymenus_admin_add()
{
    if (!$GLOBALS['xoopsSecurity']->check()) {
        redirect_header($GLOBALS['mymenus_adminpage'], 3, implode('<br />', $GLOBALS['xoopsSecurity']->getErrors()));
    }

    $this_handler =& xoops_getModuleHandler('menu','mymenus');
    $criteria = new CriteriaCompo(new Criteria('mid', $_POST['mid']));
    $criteria->setSort('weight');
    $criteria->setOrder('DESC');
    $criteria->setLimit(1);
    $menus = $this_handler->getObjects($criteria);
    $weight = 1;
    if (isset($menus[0]) && is_object($menus[0])) {
        $weight = $menus[0]->getVar('weight') + 1;
    }

    $obj = $this_handler->create();
    if (!isset($_POST['hooks'])) {
        $_POST['hooks'] = array();
    }
    $obj->setVars($_POST);
    $obj->setVar('weight', $weight);

    if (!$this_handler->insert($obj)){
        $msg = _AM_MYMENUS_MSG_ERROR;
    } else {
        $this_handler->update_weights($obj);
        $msg = _AM_MYMENUS_MSG_SUCCESS;
    }

    redirect_header($GLOBALS['mymenus_adminpage'] . '?op=list&amp;menu_id=' . $obj->getVar('mid'), 2, $msg);
}

function mymenus_admin_edit($id)
{
    if (!$GLOBALS['xoopsSecurity']->check()) {
        redirect_header($GLOBALS['mymenus_adminpage'], 3, implode('<br />', $GLOBALS['xoopsSecurity']->getErrors()));
    }

    $this_handler =& xoops_getModuleHandler('menu','mymenus');
    $obj = $this_handler->get($id);
    if (!isset($_POST['hooks'])) {
        $_POST['hooks'] = array();
    }
    $obj->setVars($_POST);

    if (!$this_handler->insert($obj)){
        $msg = _AM_MYMENUS_MSG_ERROR;
    }else{
        $msg = _AM_MYMENUS_MSG_SUCCESS;
    }

    redirect_header($GLOBALS['mymenus_adminpage'] . '?op=list', 2, $msg);
}

function mymenus_admin_form($id = null, $pid = null)
{
    global $menu_id;

    $registry =& MymenusRegistry::getInstance();
    $plugin =& MymenusPlugin::getInstance();

    $this_handler =& xoops_getModuleHandler('menu','mymenus');
    $objArray = array();

    if (isset($id)) {
        $ftitle = _EDIT;
        $obj = $this_handler->get($id);
        $objArray = $obj->getValues();

    } else {
        $ftitle = _ADD;
        $obj = $this_handler->create();
        $objArray = $obj->getValues();
        if (isset($pid)) {
            $objArray['pid'] = $pid;
        }
    }

    $form = new XoopsThemeForm($ftitle, 'admin_form', $GLOBALS['mymenus_adminpage'], "post", true);
    $formtitle = new XoopsFormText(_AM_MYMENUS_MENU_TITLE, 'title', 50, 255, $objArray['title']);
    $formalttitle = new XoopsFormText(_AM_MYMENUS_MENU_ALTTITLE, 'alt_title', 50, 255, $objArray['alt_title']);

    $formlink  = new XoopsFormText(_AM_MYMENUS_MENU_LINK, 'link', 50, 255, $objArray['link']);
    /*$plugin->triggerEvent('FormLinkDescription');
     $formlink->setDescription($registry->getEntry('form_link_description'));  */
    $formimage  = new XoopsFormText(_AM_MYMENUS_MENU_IMAGE, 'image', 50, 255, $objArray['image']);

    $criteria = new CriteriaCompo(new Criteria('mid', $menu_id));
    $criteria->add(new Criteria('id', $id, '<>'));
    // $criteria->add(new Criteria('pid', 0));
    $criteria->setSort('weight');
    $criteria->setOrder('ASC');

    $results = $this_handler->getAll($criteria, array('title', 'id', 'pid')/*, false, false*/);
    include_once $GLOBALS['xoops']->path('class/tree.php');
    $parent_tree = new XoopsObjectTree($results, 'id', 'pid');
    $parent_select = $parent_tree->makeSelBox('pid', 'title', '-- ', $objArray['pid'], true);
    $formparent = new XoopsFormLabel(_AM_MYMENUS_MENU_PARENT, $parent_select);
//---------------mamba
//    $formvis = new XoopsFormSelect(_AM_MYMENUS_MENU_VISIBLE, "visible", $objArray['visible']);
//    $formvis->addOption("0", _NO);
//    $formvis->addOption("1", _YES);

    global $xoopsModule;
    $pathIcon16 = '../'.$xoopsModule->getInfo('icons16');

    $statontxt
        = "&nbsp;<img src=". $pathIcon16 .'/1.png'.' '. "alt='" ._YES . "' />&nbsp;" . _YES
        . "&nbsp;&nbsp;&nbsp;";
    $statofftxt
        = "&nbsp;<img src=". $pathIcon16 .'/0.png' .' '."alt='" . _NO . "' />&nbsp;"
        . _NO . "&nbsp;";
    $formvis = new XoopsFormRadioYN(_AM_MYMENUS_MENU_VISIBLE, 'visible', $objArray['visible'], $statontxt, $statofftxt);
//---------------mamba

    $formtarget  = new XoopsFormSelect(_AM_MYMENUS_MENU_TARGET, "target", $objArray['target']);
    $formtarget->addOption("_self", _AM_MYMENUS_MENU_TARG_SELF);
    $formtarget->addOption("_blank", _AM_MYMENUS_MENU_TARG_BLANK);
    $formtarget->addOption("_parent", _AM_MYMENUS_MENU_TARG_PARENT);
    $formtarget->addOption("_top", _AM_MYMENUS_MENU_TARG_TOP);

    $formgroups = new XoopsFormSelectGroup(_AM_MYMENUS_MENU_GROUPS, "groups", true, $objArray['groups'], 5, true);
    $formgroups->setDescription(_AM_MYMENUS_MENU_GROUPS_HELP);

    //$formhooks = new XoopsFormTextArea(_AM_MYMENUS_MENU_HOOKS, "hooks", $objArray['hooks'], 7, 60);

    $formhooks = new XoopsFormSelect(_AM_MYMENUS_MENU_ACCESS_FILTER, "hooks", $objArray['hooks'], 5, true);
    $plugin->triggerEvent('AccessFilter');
    $results = $registry->getEntry('access_filter');
    if ($results) {
        foreach ($results as $result) {
            $formhooks->addOption($result['method'], $result['name']);
        }
    }

    $formcss  = new XoopsFormText(_AM_MYMENUS_MENU_CSS, 'css', 50, 255, $objArray['css']);

    $form->addElement($formtitle, true);
    $form->addElement($formalttitle);
    $form->addElement($formlink);
    $form->addElement($formimage);
    $form->addElement($formparent);
    $form->addElement($formvis);
    $form->addElement($formtarget);
    $form->addElement($formgroups);
    $form->addElement($formhooks);
    $form->addElement($formcss);
    //$form->addElement($formhooks2);

    $tray = new XoopsFormElementTray('' ,'');
    $tray->addElement(new XoopsFormButton('', 'submit_button', _SUBMIT, 'submit'));

    $btn = new XoopsFormButton('', 'reset', _CANCEL, 'button');

    if (isset($id)){
        $btn->setExtra('onclick="document.location.href=\'' . $GLOBALS['mymenus_adminpage'] . '?op=list&amp;menu_id=' . $menu_id . ' \'"');
    }else{
        $btn->setExtra('onclick="document.getElementById(\'addform\').style.display = \'none\'; return false;"');
    }

    $tray->addElement($btn);
    $form->addElement($tray);

    if (isset($id)){
        $form->addElement(new XoopsFormHidden('op', 'editok'));
        $form->addElement(new XoopsFormHidden('id', $id));
    }else{
        $form->addElement(new XoopsFormHidden('op', 'add'));
    }

    $form->addElement(new XoopsFormHidden('mid', $menu_id));

    return $form->render();
}

function mymenus_admin_move($id, $weight)
{
    $this_handler =& xoops_getModuleHandler('menu', 'mymenus');
    $obj = $this_handler->get($id);
    $obj->setVar('weight', $weight);
    $this_handler->insert($obj);
    $this_handler->update_weights($obj);
}

function mymenus_admin_toggle($id, $visible)
{
    $visible = ($visible == 1) ? 0 : 1;
    $this_handler =& xoops_getModuleHandler('menu', 'mymenus');
    $obj = $this_handler->get($id);
    $obj->setVar('visible', $visible);
    $this_handler->insert($obj);
}

?>