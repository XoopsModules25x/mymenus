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
 * @version         $Id: admin_links.php 12940 2015-01-21 17:33:38Z zyspec $
 */

include_once __DIR__ . '/admin_header.php';

$menus_handler =& xoops_getModuleHandler('menus', 'mymenus');
$criteria = new CriteriaCompo();
$criteria->setSort('id');
$criteria->setOrder('ASC');
$menus_list = $menus_handler->getList($criteria);

//$indexAdmin = new ModuleAdmin();

if (empty($menus_list)) {
    redirect_header('admin_menus.php', 1, _AM_MYMENUS_MSG_NOMENUS);
    exit;
}

$valid_menu_ids = array_keys($menus_list);
if (isset($_REQUEST['mid']) && in_array($_REQUEST['mid'], $valid_menu_ids)) {
    $menu_id = (int) $_REQUEST['mid'];
    $menu_title = $menus_list[$menu_id];
} else {
    $keys = array_keys($menus_list);
    $menu_id = $valid_menu_ids[0];       //force menu id to first valid menu id in the list
    $menu_title = $menus_list[$menu_id]; // and get it's title
}
$mymenusTpl->assign('mid', $menu_id);
$mymenusTpl->assign('menu_title', $menu_title);
$mymenusTpl->assign('menus_list', $menus_list);

$op      = XoopsRequest::getCmd('op', 'list');
$id      = XoopsRequest::getInt('id', 0);
$pid     = XoopsRequest::getInt('pid', 0);
$limit   = XoopsRequest::getInt('limit', 15);
$start   = XoopsRequest::getInt('start', 0);
$redir   = XoopsRequest::getString('redir', null);
$weight  = XoopsRequest::getInt('weight', 0);
$visible = XoopsRequest::getInt('visible', 0);

/*
$op = isset($_GET['op']) ? trim($_GET['op']) : (isset($_POST['op']) ? trim($_POST['op']) : 'list');

$id = isset($_GET['id']) ? intval($_GET['id']) : (isset($_POST['id']) ? intval($_POST['id']) : null);
$pid = isset($_GET['pid']) ? intval($_GET['pid']) : (isset($_POST['pid']) ? intval($_POST['pid']) : null);

$limit = isset($_GET['limit']) ? intval($_GET['limit']) : (isset($_POST['limit']) ? intval($_POST['limit']) : 15);
$start = isset($_GET['start']) ? intval($_GET['start']) : (isset($_POST['start']) ? intval($_POST['start']) : 0);
$redir = isset($_GET['redir']) ? $_GET['redir'] : (isset($_POST['redir']) ? $_POST['redir'] : null);

$weight = isset($_GET['weight']) ? intval($_GET['weight']) : (isset($_POST['weight']) ? intval($_POST['weight']) : 0);
$visible = isset($_GET['visible']) ? intval($_GET['visible']) : (isset($_POST['visible']) ? intval($_POST['visible']) : 0);
*/

$mymenus_adminpage = 'admin_links.php';

switch ($op) {
    case 'add':
        mymenus_admin_add($menu_id);
        break;
    case 'form':
        xoops_cp_header();
        echo $indexAdmin->addNavigation('admin_links.php');
        echo mymenus_admin_form(null, $pid);
        include __DIR__ . '/admin_footer.php';
        break;
    case 'edit':
        echo mymenus_admin_form($id);
        break;
    case 'editok':
        mymenus_admin_edit($id, $menu_id);
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
        echo $indexAdmin->addNavigation('admin_links.php');
        mymenus_admin_move($id, $weight);
        echo mymenus_admin_list($start, $menu_id);
        include __DIR__ . '/admin_footer.php';
        break;
    case 'toggle':
        mymenus_admin_toggle($id, $visible);
        break;
    case 'order':
        $order = $_POST['mod'];
        parse_str($order,$test);

        $i = 1;
        $this_handler =& xoops_getModuleHandler('links', 'mymenus');
        foreach ($test['mod'] as $order=>$value) {

             $obj = $this_handler->get($order);
             $obj->setVar('weight', ++$i);

             /*set submenu*/
             if (isset($value)) {
                $obj->setVar('pid', $value);
             } else {
                $obj->setVar('pid', 0);
             }
             $this_handler->insert($obj);
             $this_handler->update_weights($obj);

        }
        break;
    case 'list':
    default:
    xoops_cp_header();
    $module_handler =& xoops_gethandler('module');
    $system =& $module_handler->getByDirname('system');
    $systemConfigHandler =& xoops_gethandler('config');
    $systemConfig =& $systemConfigHandler->getConfigsByCat(0, $system->getVar('mid'));

    // Add module stylesheet
    $xoTheme->addStylesheet(XOOPS_URL . '/modules/system/css/ui/' . $systemConfig['jquery_theme'] . '/ui.all.css');
    $xoTheme->addStylesheet(XOOPS_URL . '/modules/mymenus/assets/css/admin.css');
    $xoTheme->addStylesheet(XOOPS_URL . '/Frameworks/moduleclasses/moduleadmin/css/admin.css');
    // Define scripts
    $xoTheme->addScript('browse.php?Frameworks/jquery/plugins/jquery.ui.js');
    $xoTheme->addScript(XOOPS_URL . '/modules/mymenus/assets/js/nestedSortable.js');
    //$xoTheme->addScript(XOOPS_URL . '/modules/mymenus/assets/js/switchButton.js');
    $xoTheme->addScript(XOOPS_URL . '/modules/mymenus/assets/js/order.js');

    echo $indexAdmin->addNavigation('admin_links.php');
    echo mymenus_admin_list($start, $menu_id);

    /* Disable xoops debugger in dialog window */
    include_once $GLOBALS['xoops']->path('/class/logger/xoopslogger.php');
    $xoopsLogger            =& XoopsLogger::getInstance();
    $xoopsLogger->activated = true;
    error_reporting(-1);

    include __DIR__ . '/admin_footer.php';
    break;

}

/**
 * Display the links in a menu
 *
 * @param integer $start
 * @param integer $menu_id
 *
 * @return bool|mixed|string
 */
function mymenus_admin_list($start = 0, $menu_id)
{
    global $mymenusTpl;

    $links_handler =& xoops_getModuleHandler('links', 'mymenus');

    $criteria = new CriteriaCompo(new Criteria('mid', (int) $menu_id));

    $count_links = $links_handler->getCount($criteria);
    $mymenusTpl->assign('count', $count_links);
    $criteria->setSort('weight');
    $criteria->setOrder('ASC');

    $menus = array();
    $menusArray = array();
    if (($count_links > 0) && ($count_links >= (int) $start)) {
        $criteria->setStart((int) $start);
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

    return $mymenusTpl->fetch($GLOBALS['xoops']->path('modules/mymenus/templates/static/mymenus_admin_links.tpl'));
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

    $id = (int) $id;
    if ($id <= 0) {
        redirect_header($GLOBALS['mymenus_adminpage'], 1);
    }

    $this_handler =& xoops_getModuleHandler('links' , 'mymenus');
    $obj = $this_handler->get($id);
    if ((empty($obj)) || !($obj instanceof MymenusLinks)) {
        redirect_header($GLOBALS['mymenus_adminpage'], 1);
    }

    //get sub item
    $criteria = new CriteriaCompo();
    $criteria->add(new Criteria('id', $id));
    $criteria->add(new Criteria('pid', $id),'OR');

    //first delete links level 2
//    global $xoopsDB;
    $query = "DELETE FROM " . $GLOBALS['xoopsDB']->prefix("mymenus_links")." WHERE pid = (
    SELECT id FROM (
    SELECT * FROM " . $GLOBALS['xoopsDB']->prefix("mymenus_links")." WHERE pid = {$id}) AS sec
    );";
    $result = $GLOBALS['xoopsDB']->queryF($query);
    //delete links level 0 and 1
    if (!$this_handler->deleteAll($criteria)) {
        xoops_cp_header();
        xoops_error(_AM_MYMENUS_MSG_ERROR, $obj->getVar('id'));
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

    $this_handler =& xoops_getModuleHandler('links' , 'mymenus');

    if (!$this_handler->deleteAll()) {
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

    $arr = array('op' => $op,
                 'id' => $id
    );
    if (!is_null($redir)) {
        $arr['redir'] = $redir;
    }

    xoops_cp_header();
    xoops_confirm($arr, $GLOBALS['mymenus_adminpage'], _AM_MYMENUS_MSG_AYSL);
    xoops_cp_footer();
}

function mymenus_admin_add($menu_id)
{
    if (!$GLOBALS['xoopsSecurity']->check()) {
        redirect_header($GLOBALS['mymenus_adminpage'], 3, implode('<br />', $GLOBALS['xoopsSecurity']->getErrors()));
    }

    $links_handler =& xoops_getModuleHandler('links','mymenus');
//    $criteria = new CriteriaCompo(new Criteria('mid', $_POST['mid']));
    $criteria = new CriteriaCompo(new Criteria('mid', $menu_id));
    $criteria->setSort('weight');
    $criteria->setOrder('DESC');
    $criteria->setLimit(1);
    $menus = $links_handler->getObjects($criteria);
    $weight = 1;
    if (isset($menus[0]) && ($menus[0] instanceof MymenusLinks)) {
        $weight = $menus[0]->getVar('weight') + 1;
    }

    $link_obj = $links_handler->create();
    if (!isset($_POST['hooks'])) {
        $_POST['hooks'] = array();
    }
    //@TODO: clean incoming POST vars
    $link_obj->setVars($_POST);
    $link_obj->setVar('weight', $weight);

    if (!$links_handler->insert($link_obj)) {
        $msg = _AM_MYMENUS_MSG_ERROR;
    } else {
        $links_handler->update_weights($link_obj);
        $msg = _AM_MYMENUS_MSG_SUCCESS;
    }

    redirect_header($GLOBALS['mymenus_adminpage'] . '?op=list&amp;mid=' . $link_obj->getVar('mid'), 2, $msg);
}

/**
 * @param integer $id
 * @param integer $menu_id
 */
function mymenus_admin_edit($id, $menu_id)
{
    if (!$GLOBALS['xoopsSecurity']->check()) {
        redirect_header($GLOBALS['mymenus_adminpage'], 3, implode('<br />', $GLOBALS['xoopsSecurity']->getErrors()));
    }

    if (empty($menu_id)) {
        redirect_header($GLOBALS['mymenus_adminpage'] . "?op=list", 2, _AM_MYMENUS_MSG_MENU_INVALID_ERROR);
    }

    $menu_id       = (int) $menu_id;
    $links_handler =& xoops_getModuleHandler('links','mymenus');
    $link_obj     = $links_handler->get((int) $id);

    //if this was moved then parent could be in different menu, if so then set parent to top level
    if (!empty($_POST['pid'])) {
        $parent_obj = $links_handler->get($link_obj->getVar('pid'));  //get the parent oject
        if(($parent_obj instanceof MylinksLinks) && ($link_obj->getVar('mid') != $parent_obj->getVar('mid'))) {
            $link_obj->setVar('pid', 0);
        }
    }
    /* Disable xoops debugger in dialog window */
    include_once $GLOBALS['xoops']->path('/class/logger/xoopslogger.php');
    $xoopsLogger =& XoopsLogger::getInstance();
    $xoopsLogger->activated = false;
    error_reporting(0);

    // @TODO: clean incoming POST vars
    $link_obj->setVars($_POST);

    if (!$links_handler->insert($link_obj)) {
        $msg = _AM_MYMENUS_MSG_ERROR;
    } else {
        $msg = _AM_MYMENUS_MSG_SUCCESS;
    }

    redirect_header($GLOBALS['mymenus_adminpage'] . "?op=list&mid={$menu_id}", 2, $msg);
}

/**
 * @param null $id
 * @param null $pid
 *
 * @return string
 */
function mymenus_admin_form($id = null, $pid = null, $menu_id = null)
{
    /* Disable xoops debugger in dialog window */
    include_once $GLOBALS['xoops']->path('/class/logger/xoopslogger.php');
    $xoopsLogger =& XoopsLogger::getInstance();
    $xoopsLogger->activated = false;
    error_reporting(0);

    global $pathIcon16;

    $registry =& MymenusRegistry::getInstance();
    $plugin =& MymenusPlugin::getInstance();

    $this_handler =& xoops_getModuleHandler('links','mymenus');
    $objArray = array();

    if (isset($id)) {
        $ftitle = _EDIT;
        $obj = $this_handler->get((int) $id);
        $objArray = $obj->getValues();

    } else {
        $ftitle = _ADD;
        $obj = $this_handler->create();
        $objArray = $obj->getValues();
        if (isset($pid)) {
            $objArray['pid'] = (int) $pid;
        }
        if (isset($menu_id)) {
            $objArray['mid'] = (int) $menu_id;
        }
    }
    $form = new XoopsThemeForm($ftitle, 'admin_form', $GLOBALS['mymenus_adminpage'], "post", true);
    $formtitle = new XoopsFormText(_AM_MYMENUS_MENU_TITLE, 'title', 50, 255, $objArray['title']);
    $formalttitle = new XoopsFormText(_AM_MYMENUS_MENU_ALTTITLE, 'alt_title', 50, 255, $objArray['alt_title']);
    // display menu options (if more than 1 menu available
    $menus_handler =& xoops_getmodulehandler('menus', 'mymenus');
    $criteria = new CriteriaCompo();
    $criteria->setSort('title');
    $criteria->setOrder('ASC');
    $menus_list = $menus_handler->getList($criteria);
    if (count($menus_list > 1)) {
        if (null == $objArray['mid']) { // initial menu value not set
            $menu_values = array_flip($menu_list);
            $formmid = new XoopsFormSelect('Menu', 'mid', array_shift($menu_values));
        } else {
            $formmid = new XoopsFormSelect('Menu', 'mid', $objArray['mid']);
        }
        $formmid->addOptionArray($menus_list);
    } else {
        $menu_keys = array_keys($menu_list);
        $menu_title = array_shift($menu_list);
        $formmid = new XoopsFormElementTray('Menu');
        $formmid->addElement(new XoopsFormHidden('mid', $menu_keys[0]));
        $formmid->addElement(new XoopsFormLabel('', $menu_title, 'menu_title'));
    }
    $formlink  = new XoopsFormText(_AM_MYMENUS_MENU_LINK, 'link', 50, 255, $objArray['link']);
    $formimage  = new XoopsFormText(_AM_MYMENUS_MENU_IMAGE, 'image', 50, 255, $objArray['image']);

    $statontxt  = "&nbsp;<img src='{$pathIcon16}/1.png' alt='" ._YES . "' />&nbsp;" . _YES . "&nbsp;&nbsp;&nbsp;";
    $statofftxt = "&nbsp;<img src='{$pathIcon16}/0.png' alt='" . _NO . "' />&nbsp;" . _NO . "&nbsp;";
    $formvis = new XoopsFormRadioYN(_AM_MYMENUS_MENU_VISIBLE, 'visible', $objArray['visible'], $statontxt, $statofftxt);

    $formtarget  = new XoopsFormSelect(_AM_MYMENUS_MENU_TARGET, "target", $objArray['target']);
    $formtarget->addOption("_self", _AM_MYMENUS_MENU_TARG_SELF);
    $formtarget->addOption("_blank", _AM_MYMENUS_MENU_TARG_BLANK);
    $formtarget->addOption("_parent", _AM_MYMENUS_MENU_TARG_PARENT);
    $formtarget->addOption("_top", _AM_MYMENUS_MENU_TARG_TOP);

    $formgroups = new XoopsFormSelectGroup(_AM_MYMENUS_MENU_GROUPS, "groups", true, $objArray['groups'], 5, true);
    $formgroups->setDescription(_AM_MYMENUS_MENU_GROUPS_HELP);

    $formcss  = new XoopsFormText(_AM_MYMENUS_MENU_CSS, 'css', 50, 255, $objArray['css']);

    $form->addElement($formtitle, true);
    $form->addElement($formalttitle);
    $form->addElement($formmid);
    $form->addElement($formlink);
    $form->addElement($formimage);
    $form->addElement($formparent);
    $form->addElement($formvis);
    $form->addElement($formtarget);
    $form->addElement($formgroups);
    $form->addElement($formhooks);
    $form->addElement($formcss);

    $tray = new XoopsFormElementTray('' ,'');
    $tray->addElement(new XoopsFormButton('', 'submit_button', _SUBMIT, 'submit'));

    $btn = new XoopsFormButton('', 'reset', _CANCEL, 'button');

    if (isset($id)) {
        $btn->setExtra("onclick=\"document.location.href='" . $GLOBALS['mymenus_adminpage'] . "?op=list&amp;mid={$menu_id}'\"");
    } else {
        $btn->setExtra("onclick=\"document.getElementById('addform').style.display = 'none'; return false;\"");
    }

    $tray->addElement($btn);
    $form->addElement($tray);

    if (isset($id)) {
        $form->addElement(new XoopsFormHidden('op', 'editok'));
        $form->addElement(new XoopsFormHidden('id', $id));
    } else {
        $form->addElement(new XoopsFormHidden('op', 'add'));
    }

    return $form->render();
}

/**
 *
 * Update the {@see MymenusLinks} weight (order)
 *
 * @param integer $id of links object
 * @param integer $weight
 */
function mymenus_admin_move($id, $weight)
{
    $this_handler =& xoops_getModuleHandler('links', 'mymenus');
    $obj = $this_handler->get((int) $id);
    $obj->setVar('weight', (int) $weight);
    $this_handler->insert($obj);
    $this_handler->update_weights($obj);
}

/**
 * @param $id
 * @param $visible
 */
function mymenus_admin_toggle($id, $visible)
{
    include_once $GLOBALS['xoops']->path('/class/logger/xoopslogger.php');
    $xoopsLogger =& XoopsLogger::getInstance();
    $xoopsLogger->activated = true;
    error_reporting(0);

    $this_handler =& xoops_getModuleHandler('links', 'mymenus');
    $obj = $this_handler->get((int) $id);
    $visible = (1 == $obj->getVar('visible')) ? 0 : 1;
    $obj->setVar('visible', $visible);
    $this_handler->insert($obj);
    echo $obj->getVar('visible');
}
