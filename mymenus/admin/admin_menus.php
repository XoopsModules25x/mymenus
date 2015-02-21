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
 * @version         $Id: admin_menus.php 12940 2015-01-21 17:33:38Z zyspec $
 */

include_once __DIR__ . '/admin_header.php';

$op    = XoopsRequest::getCmd('op', 'list');
$id    = XoopsRequest::getInt('id', null);
$limit = XoopsRequest::getInt('limit', 15);
$start = XoopsRequest::getInt('start', 0);
$redir = XoopsRequest::getString('redir', null);
/*
$op = isset($_GET['op']) ? trim($_GET['op']) : (isset($_POST['op']) ? trim($_POST['op']) : 'list');

$id = isset($_GET['id']) ? intval($_GET['id']) : (isset($_POST['id']) ? intval($_POST['id']) : null);

$limit = isset($_GET['limit']) ? intval($_GET['limit']) : (isset($_POST['limit']) ? intval($_POST['limit']) : 15);
$start = isset($_GET['start']) ? intval($_GET['start']) : (isset($_POST['start']) ? intval($_POST['start']) : 0);
$redir = isset($_GET['redir']) ? $_GET['redir'] : (isset($_POST['redir']) ? $_POST['redir'] : null);
*/
$mymenus_adminpage = 'admin_menus.php';

switch ($op) {
    case 'add':
        mymenus_admin_add();
        break;
    case 'edit':
        xoops_cp_header();
        echo $indexAdmin->addNavigation('admin_menus.php');
        //mymenus_adminMenu(0, _MI_MYMENUS_MENUSMANAGER);
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
    case 'list':
    default:
        xoops_cp_header();
        echo $indexAdmin->addNavigation('admin_menus.php');
        echo mymenus_admin_list($start, $limit);
        include 'admin_footer.php';
        break;
}

/**
 * @param integer $start
 * @param integer $limit
 *
 * @return bool|mixed|string
 */
function mymenus_admin_list($start = 0, $limit)
{
    global $mymenusTpl;
    $myts =& MyTextSanitizer::getInstance();

    $limit = (int) $limit;
    $this_handler =& xoops_getModuleHandler('menus', 'mymenus');

    $query = XoopsRequest::getString('query', null, 'POST');
//    $query = isset($_POST['query']) ? $_POST['query'] : null;
    $mymenusTpl->assign('query', $query);

    $criteria = new CriteriaCompo();
    if (!is_null($query)) {
        $crit = new CriteriaCompo(new Criteria('title', $myts->addSlashes($query).'%','LIKE'));
        $criteria->add($crit);
    }

    $count = $this_handler->getCount($criteria);
    $mymenusTpl->assign('count', $count);

    $criteria->setStart($start);
    $criteria->setLimit($limit);
    $criteria->setSort('id');
    $criteria->setOrder('ASC');

    if ($count > 0) {
        if ($count > $limit) {
            xoops_load('XoopsPagenav');
            $nav = new XoopsPageNav($count, $limit, $start, 'start', 'op=list');
            $mymenusTpl->assign('pag', '<div style="float:left; padding-top:2px; vertical-align: center;">' . $nav->renderNav() . '</div>');
        } else {
            $mymenusTpl->assign('pag', '');
        }

        $objs = $this_handler->getObjects($criteria);
        foreach ($objs as $obj) {
            $objArray = $obj->getValues();
            $mymenusTpl->append('objs', $objArray);
            unset($objArray);
        }
        unset($criteria, $objs);
    } else {
        $mymenusTpl->assign('pag', '');
    }

    $mymenusTpl->assign('addform', mymenus_admin_form());

    return $mymenusTpl->fetch($GLOBALS['xoops']->path('modules/mymenus/templates/static/mymenus_admin_menus.tpl'));
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

    if ((int) $id <= 0) {
        redirect_header($GLOBALS['mymenus_adminpage'], 1);
    }

    $this_handler =& xoops_getModuleHandler('menus' , 'mymenus');
    $obj = $this_handler->get((int) $id);
    if (!is_object($obj)) {
        redirect_header($GLOBALS['mymenus_adminpage'], 1);
    }

    if (!$this_handler->delete($obj)) {
        xoops_cp_header();
        xoops_error(_AM_MYMENUS_MSG_DELETE_MENU_ERROR, $obj->getVar('id'));
        xoops_cp_footer();
        exit();
    }

    $this_handler =& xoops_getModuleHandler('links' , 'mymenus');
    $criteria = new Criteria('mid', $id);
    $this_handler->deleteAll($criteria);
    unset($criteria);

    redirect_header(!is_null($redir) ? base64_decode($redir) : $GLOBALS['mymenus_adminpage'] , 2, _AM_MYMENUS_MSG_DELETE_MENU_SUCCESS);
}

/**
 * @param null $redir
 */
function mymenus_admin_delall($redir = null)
{
    if (!$GLOBALS['xoopsSecurity']->check()) {
        redirect_header($GLOBALS['mymenus_adminpage'], 1, implode('<br />', $GLOBALS['xoopsSecurity']->getErrors()));
    }

    $this_handler =& xoops_getModuleHandler('menus', 'mymenus');

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
    $arr = array();
    $arr['op'] = XoopsFilterInput::clean($op, 'CMD');
    $arr['id'] = XoopsFilterInput::clean($id, 'INT');
    if (!is_null($redir)) {
        $arr['redir'] = $redir;
    }
    xoops_cp_header();
    xoops_confirm($arr, $GLOBALS['mymenus_adminpage'], _AM_MYMENUS_MSG_AYSC);
    xoops_cp_footer();
}

function mymenus_admin_add()
{
    if (!$GLOBALS['xoopsSecurity']->check()) {
        redirect_header($GLOBALS['mymenus_adminpage'], 3, implode('<br />', $GLOBALS['xoopsSecurity']->getErrors()));
    }

    $this_handler =& xoops_getModuleHandler('menus','mymenus');
    $obj = $this_handler->create();
    $obj->setVars(array('title' => XoopsRequest::getString('title', '', 'POST'),
                         'css' => XoopsRequest::getString('css', '', 'POST'))
    );

//    $obj->setVars($_POST);

    if (!$this_handler->insert($obj)) {
        $msg = _AM_MYMENUS_MSG_ADD_MENU_ERROR;
    } else {
        $msg = _AM_MYMENUS_MSG_ADD_MENU_SUCCESS;
    }

    redirect_header($GLOBALS['mymenus_adminpage'] . '?op=list', 2, $msg);
}

/**
 * @param $id
 */
function mymenus_admin_edit($id)
{
    if (!$GLOBALS['xoopsSecurity']->check()) {
        redirect_header($GLOBALS['mymenus_adminpage'], 3, implode('<br />', $GLOBALS['xoopsSecurity']->getErrors()));
    }
    $this_handler =& xoops_getmodulehandler('menus','mymenus');
    $obj = $this_handler->get((int) $id);
    $obj->setVars(array('title' => XoopsRequest::getString('title', '', 'POST'),
                         'css' => XoopsRequest::getString('css', '', 'POST'))
    );

//    $obj->setVars($_POST);

    if (!$this_handler->insert($obj)) {
        $msg = _AM_MYMENUS_MSG_EDIT_MENU_ERROR;
    } else {
        $msg = _AM_MYMENUS_MSG_EDIT_MENU_SUCCESS;
    }

    redirect_header($GLOBALS['mymenus_adminpage'] . '?op=list', 2, $msg);
}

/**
 * @param null $id
 *
 * @return string
 */
function mymenus_admin_form($id = null)
{
    $this_handler =& xoops_getmodulehandler('menus','mymenus');
//    $objArray = array();

    if (isset($id)) {
        $ftitle = _EDIT;
        $id = (int) $id;
        $obj = $this_handler->get($id);
        $objArray = $obj->getValues();
    } else {
        $ftitle = _ADD;
        $obj = $this_handler->create();
        $objArray = $obj->getValues();
    }

    $form = new XoopsThemeForm($ftitle, 'admin_form', $GLOBALS['mymenus_adminpage'], "post", true);
    $form->addElement(new XoopsFormText(_AM_MYMENUS_MENU_TITLE, 'title', 50, 255, $objArray['title']), true);
    //
    $form->addElement(new XoopsFormText(_AM_MYMENUS_MENU_CSS, 'css', 50, 255, $objArray['css']), false);
    //

    $tray = new XoopsFormElementTray('', '');
    $tray->addElement(new XoopsFormButton('', 'submit_button', _SUBMIT, 'submit'));

    $btn = new XoopsFormButton('', 'reset', _CANCEL, 'button');

    if (isset($id)) {
        $form->addElement(new XoopsFormHidden('op', 'editok'));
        $form->addElement(new XoopsFormHidden('id', $id));
        $btn->setExtra('onclick="document.location.href=\'' . $GLOBALS['mymenus_adminpage'] . '?op=list\'"');
    } else {
        $form->addElement(new XoopsFormHidden('op', 'add'));
        $btn->setExtra('onclick="document.getElementById(\'addform\').style.display = \'none\'; return false;"');
    }

    $tray->addElement($btn);
    $form->addElement($tray);

    return $form->render();
}
