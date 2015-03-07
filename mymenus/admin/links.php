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
 * @version         $Id: links.php
 */

include_once __DIR__ . '/admin_header.php';

$currentFile = basename(__FILE__);

$mymenusTpl       = new XoopsTpl(); // will be removed???
$mymenusAdminPage = 'links.php'; // will be removed???

$menusCriteria = new CriteriaCompo();
$menusCriteria->setSort('id');
$menusCriteria->setOrder('ASC');
$menusList = $mymenus->getHandler('menus')->getList($menusCriteria);
if (!($menusList)) {
    redirect_header('menus.php', 1, _AM_MYMENUS_MSG_NOMENUS);
}

$valid_menu_ids = array_keys($menusList);
$mid            = XoopsRequest::getInt('mid', XoopsRequest::getInt('mid', '', 'POST'), 'GET');
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

$id      = XoopsRequest::getInt('id', 0);
$pid     = XoopsRequest::getInt('pid', 0);
$start   = XoopsRequest::getInt('start', 0);
$weight  = XoopsRequest::getInt('weight', 0);
$visible = XoopsRequest::getInt('visible', 0);

$op = XoopsRequest::getCmd('op', 'list');
switch ($op) {
    /*
        case 'form':
            xoops_cp_header();
            $indexAdmin = new ModuleAdmin();
            echo $indexAdmin->addNavigation($currentFile);
            //
            echo mymenusAdminForm(null, $pid, $mid);
            //
            include __DIR__ . '/admin_footer.php';
            break;
    */
    case 'edit':
        echo MymenusLinksUtilities::mymenusAdminForm($id, null, $mid);
        break;

    case 'add':
        MymenusLinksUtilities::mymenusAdminAdd($mid);
        break;

    case 'save':
        MymenusLinksUtilities::mymenusAdminSave($id, $mid);
        break;

    case 'delete':
        $id       = XoopsRequest::getInt('id', null);
        $linksObj = $mymenus->getHandler('links')->get($id);
        if (XoopsRequest::getBool('ok', false, 'POST') === true) {
            if (!$GLOBALS['xoopsSecurity']->check()) {
                redirect_header($currentFile, 3, implode(',', $GLOBALS['xoopsSecurity']->getErrors()));
            }
            //get sub item
            $linksCriteria = new CriteriaCompo();
            $linksCriteria->add(new Criteria('id', $id));
            $linksCriteria->add(new Criteria('pid', $id), 'OR');
            //first delete links level 2
            $query = "DELETE FROM " . $GLOBALS['xoopsDB']->prefix("mymenus_links");
            $query .= " WHERE pid = (SELECT id FROM (SELECT * FROM " . $GLOBALS['xoopsDB']->prefix("mymenus_links") . " WHERE pid = {$id}) AS sec);";
            $result = $GLOBALS['xoopsDB']->queryF($query);
            //delete links level 0 and 1
            if (!$mymenus->getHandler('links')->deleteAll($linksCriteria)) {
                xoops_cp_header();
                xoops_error(_AM_MYMENUS_MSG_ERROR, $linksObj->getVar('id'));
                xoops_cp_footer();
                exit();
            }
            redirect_header($currentFile, 3, _AM_MYMENUS_MSG_DELETE_link_SUCCESS);
        } else {
            xoops_cp_header();
            xoops_confirm(
                array('ok' => true, 'id' => $id, 'op' => 'delete'),
//                $_SERVER['REQUEST_URI'],
                XoopsRequest::getString('REQUEST_URI', '', 'SERVER'),
                sprintf(_AM_MYMENUS_LINKS_SUREDEL, $linksObj->getVar('title'))
            );
            include_once __DIR__ . '/admin_footer.php';
        }
        break;

    case 'move':
        xoops_cp_header();
        $indexAdmin = new ModuleAdmin();
        echo $indexAdmin->addNavigation($currentFile);
        //
        MymenusLinksUtilities::mymenusAdminMove($id, $weight);
        echo MymenusLinksUtilities::mymenusAdminList($start, $mid);
        //
        include __DIR__ . '/admin_footer.php';
        break;

    case 'toggle':
        MymenusLinksUtilities::mymenusAdminToggle($id, $visible);
        break;

    case 'order':
        $test  = array();
        $order = XoopsRequest::getString('mod', '', 'POST');
        parse_str($order, $test);
        $i = 1;
        foreach ($test['mod'] as $order => $value) {
            $linksObj = $mymenus->getHandler('links')->get($order);
            $linksObj->setVar('weight', ++$i);
            // Set submenu
            if (isset($value)) {
                $linksObj->setVar('pid', $value);
            } else {
                $linksObj->setVar('pid', 0);
            }
            $mymenus->getHandler('links')->insert($linksObj);
            $mymenus->getHandler('links')->updateWeights($linksObj);
        }
        break;

    case 'list':
    default:
        xoops_cp_header();
        $indexAdmin = new ModuleAdmin();
        echo $indexAdmin->addNavigation($currentFile);
        // Add module stylesheet
        $GLOBALS['xoTheme']->addStylesheet(XOOPS_URL . '/modules/system/css/ui/' . xoops_getModuleOption('jquery_theme', 'system') . '/ui.all.css');
        $GLOBALS['xoTheme']->addStylesheet(XOOPS_URL . "/modules/{$mymenus->dirname}/assets/css/admin.css");
        $GLOBALS['xoTheme']->addStylesheet(XOOPS_URL . '/Frameworks/moduleclasses/moduleadmin/css/admin.css');
        // Define scripts
        $GLOBALS['xoTheme']->addScript('browse.php?Frameworks/jquery/plugins/jquery.ui.js');
        $GLOBALS['xoTheme']->addScript(XOOPS_URL . "/modules/{$mymenus->dirname}/assets/js/nestedSortable.js");
        //$GLOBALS['xoTheme']->addScript(XOOPS_URL . '/modules/{$mymenus->dirname}/assets/js/switchButton.js');
        $GLOBALS['xoTheme']->addScript(XOOPS_URL . "/modules/{$mymenus->dirname}/assets/js/links.js");
        echo MymenusLinksUtilities::mymenusAdminList($start, $mid);
        // Disable xoops debugger in dialog window
        include_once $GLOBALS['xoops']->path('/class/logger/xoopslogger.php');
        $xoopsLogger            =& XoopsLogger::getInstance();
        $xoopsLogger->activated = true;
        error_reporting(-1);
        //
        include __DIR__ . '/admin_footer.php';
        break;
}

/**
 * Class MymenusLinksUtilities
 */
class MymenusLinksUtilities
{

    /**
     * Display the links in a menu
     *
     * @param integer $start
     * @param integer $mid
     *
     * @return bool|mixed|string
     */
    public static function mymenusAdminList($start = 0, $mid)
    {
        $mymenus = MymenusMymenus::getInstance();
        global $mymenusTpl;
        //
        $linksCriteria = new CriteriaCompo(new Criteria('mid', (int)$mid));
        $linksCount    = $mymenus->getHandler('links')->getCount($linksCriteria);
        $mymenusTpl->assign('count', $linksCount);
        //
        $linksCriteria->setSort('weight');
        $linksCriteria->setOrder('ASC');
        //
//        $menusArray = array();
        if (($linksCount > 0) && ($linksCount >= (int)$start)) {
            $linksCriteria->setStart((int)$start);
            $linksArrays = $mymenus->getHandler('links')->getObjects($linksCriteria, false, false); // as array
            //
            include_once $GLOBALS['xoops']->path("modules/{$mymenus->dirname}/class/builder.php");
            $menuBuilder = new MymenusBuilder($linksArrays);
            $menusArray  = $menuBuilder->render();
            $mymenusTpl->assign('menus', $menusArray); // not 'menus', 'links' shoult be better
        }
        //
        $mymenusTpl->assign('addform', MymenusLinksUtilities::mymenusAdminForm(null, null, $mid));
        //
        return $mymenusTpl->fetch($GLOBALS['xoops']->path("modules/{$mymenus->dirname}/templates/static/mymenus_admin_links.tpl"));
    }

    /**
     * @param $mid
     */
    public static function mymenusAdminAdd($mid)
    {
        $mymenus = MymenusMymenus::getInstance();
        //
        if (!$GLOBALS['xoopsSecurity']->check()) {
            redirect_header($GLOBALS['mymenusAdminPage'], 3, implode('<br />', $GLOBALS['xoopsSecurity']->getErrors()));
        }
        if (!($mid)) {
            redirect_header($GLOBALS['mymenusAdminPage'] . "?op=list", 2, _AM_MYMENUS_MSG_MENU_INVALID_ERROR);
        }
        //
        $linksCiteria = new CriteriaCompo(new Criteria('mid', $mid));
        $linksCiteria->setSort('weight');
        $linksCiteria->setOrder('DESC');
        $linksCiteria->setLimit(1);
        $linksObjs = $mymenus->getHandler('links')->getObjects($linksCiteria);
        $weight    = 1;
        if (isset($linksObjs[0]) && ($linksObjs[0] instanceof MymenusLinks)) {
            $weight = $linksObjs[0]->getVar('weight') + 1;
        }

        $newLinksObj = $mymenus->getHandler('links')->create();
//    if (!isset($_POST['hooks'])) {
//        $_POST['hooks'] = array();
//    }
        if (!(XoopsRequest::getArray('hooks', null, 'POST'))) {
            $_POST['hooks'] = array();
        }
// @TODO: clean incoming POST vars
        $newLinksObj->setVars($_POST);
        $newLinksObj->setVar('weight', $weight);

        if (!$mymenus->getHandler('links')->insert($newLinksObj)) {
            $msg = _AM_MYMENUS_MSG_ERROR;
        } else {
            $mymenus->getHandler('links')->updateWeights($newLinksObj);
            $msg = _AM_MYMENUS_MSG_SUCCESS;
        }

        redirect_header($GLOBALS['mymenusAdminPage'] . '?op=list&amp;mid=' . $newLinksObj->getVar('mid'), 2, $msg);
    }

    /**
     * @param integer $id
     * @param integer $mid
     */
    public static function mymenusAdminSave($id, $mid)
    {
        $mymenus = MymenusMymenus::getInstance();
        //
        if (!$GLOBALS['xoopsSecurity']->check()) {
            redirect_header($GLOBALS['mymenusAdminPage'], 3, implode('<br />', $GLOBALS['xoopsSecurity']->getErrors()));
        }
        if (!($mid)) {
            redirect_header($GLOBALS['mymenusAdminPage'] . "?op=list", 2, _AM_MYMENUS_MSG_MENU_INVALID_ERROR);
        }
        //
        $mid      = (int)$mid;
        $linksObj = $mymenus->getHandler('links')->get((int)$id);

        //if this was moved then parent could be in different menu, if so then set parent to top level
        if (XoopsRequest::getInt('pid', '', 'POST')) {
            $parentLinksObj = $mymenus->getHandler('links')->get($linksObj->getVar('pid'));  //get the parent oject
            if (($parentLinksObj instanceof MylinksLinks) && ($linksObj->getVar('mid') != $parentLinksObj->getVar('mid'))) {
                $linksObj->setVar('pid', 0);
            }
        }
        // Disable xoops debugger in dialog window
        include_once $GLOBALS['xoops']->path('/class/logger/xoopslogger.php');
        $xoopsLogger            =& XoopsLogger::getInstance();
        $xoopsLogger->activated = false;
        error_reporting(0);

// @TODO: clean incoming POST vars
        $linksObj->setVars($_POST);

        if (!$mymenus->getHandler('links')->insert($linksObj)) {
            $msg = _AM_MYMENUS_MSG_ERROR;
        } else {
            $msg = _AM_MYMENUS_MSG_SUCCESS;
        }

        redirect_header($GLOBALS['mymenusAdminPage'] . "?op=list&mid={$mid}", 2, $msg);
    }

    /**
     * @param null $id
     * @param null $pid
     *
     * @param  null   $mid
     * @return string
     */
    public static function mymenusAdminForm($id = null, $pid = null, $mid = null)
    {
        $mymenus = MymenusMymenus::getInstance();
        //
        // Disable xoops debugger in dialog window
        include_once $GLOBALS['xoops']->path('/class/logger/xoopslogger.php');
        $xoopsLogger            =& XoopsLogger::getInstance();
        $xoopsLogger->activated = false;
        error_reporting(0);

        $pathIcon16 = $GLOBALS['xoops']->url('www/' . $GLOBALS['xoopsModule']->getInfo('systemIcons16'));

//        $registry =& MymenusRegistry::getInstance();
//        $plugin   =& MymenusPlugin::getInstance();

        $linksObj = $mymenus->getHandler('links')->get((int)$id);

        if ($linksObj->isNew()) {
            $formTitle = _ADD;
            if (isset($pid)) {
                $linksObj->setVar('pid', (int)$pid);
            }
            if (isset($mid)) {
                $linksObj->setVar('mid', (int)$mid);
            }
        } else {
            $formTitle = _EDIT;
        }
        $form = new XoopsThemeForm($formTitle, 'admin_form', $GLOBALS['mymenusAdminPage'], "post", true);
        // links: title
        $formtitle = new XoopsFormText(_AM_MYMENUS_MENU_TITLE, 'title', 50, 255, $linksObj->getVar('title'));
        $form->addElement($formtitle, true);
        // links: alt_title
        $formalttitle = new XoopsFormText(_AM_MYMENUS_MENU_ALTTITLE, 'alt_title', 50, 255, $linksObj->getVar('alt_title'));
        $form->addElement($formalttitle);
        // links: mid
        $menusCriteria = new CriteriaCompo();
        $menusCriteria->setSort('title');
        $menusCriteria->setOrder('ASC');
        $menusList = $mymenus->getHandler('menus')->getList($menusCriteria);
        if (count($menusList) > 1) {
            // display menu options (if more than 1 menu available
            if (!($linksObj->getVar('mid'))) { // initial menu value not set
//                $menuValues = array_flip($menusList);
                $formmid    = new XoopsFormSelect(_AM_MYMENUS_MENU_MENU, 'mid', $mid);//array_shift($menuValues));
            } else {
                $formmid = new XoopsFormSelect(_AM_MYMENUS_MENU_MENU, 'mid', $linksObj->getVar('mid'));
            }
            $formmid->addOptionArray($menusList);
        } else {
            $menuKeys  = array_keys($menusList);
            $menuTitle = array_shift($menusList);
            $formmid   = new XoopsFormElementTray('Menu');
            $formmid->addElement(new XoopsFormHidden('mid', $menuKeys[0]));
            $formmid->addElement(new XoopsFormLabel('', $menuTitle, 'menuTitle'));
        }
        $form->addElement($formmid);
        // links: link
        $formlink = new XoopsFormText(_AM_MYMENUS_MENU_LINK, 'link', 50, 255, $linksObj->getVar('link'));
        $form->addElement($formlink);
        // links: image
        $formimage = new XoopsFormText(_AM_MYMENUS_MENU_IMAGE, 'image', 50, 255, $linksObj->getVar('image'));
        $form->addElement($formimage);
        //
        //$form->addElement($formparent);
        // links: visible
        $statontxt  = "&nbsp;<img src='{$pathIcon16}/1.png' alt='" . _YES . "' />&nbsp;" . _YES . "&nbsp;&nbsp;&nbsp;";
        $statofftxt = "&nbsp;<img src='{$pathIcon16}/0.png' alt='" . _NO . "' />&nbsp;" . _NO . "&nbsp;";
        $formvis    = new XoopsFormRadioYN(_AM_MYMENUS_MENU_VISIBLE, 'visible', $linksObj->getVar('visible'), $statontxt, $statofftxt);
        $form->addElement($formvis);
        // links: target
        $formtarget = new XoopsFormSelect(_AM_MYMENUS_MENU_TARGET, "target", $linksObj->getVar('target'));
        $formtarget->addOption("_self", _AM_MYMENUS_MENU_TARG_SELF);
        $formtarget->addOption("_blank", _AM_MYMENUS_MENU_TARG_BLANK);
        $formtarget->addOption("_parent", _AM_MYMENUS_MENU_TARG_PARENT);
        $formtarget->addOption("_top", _AM_MYMENUS_MENU_TARG_TOP);
        $form->addElement($formtarget);
        // links: groups
        $formgroups = new XoopsFormSelectGroup(_AM_MYMENUS_MENU_GROUPS, "groups", true, $linksObj->getVar('groups'), 5, true);
        $formgroups->setDescription(_AM_MYMENUS_MENU_GROUPS_HELP);
        $form->addElement($formgroups);
// @TODO: reintroduce hooks
        /*
            //links: hooks
            $formhooks = new XoopsFormSelect(_AM_MYMENUS_MENU_ACCESS_FILTER, "hooks", $linksObj->getVar('hooks'), 5, true);
            $plugin->triggerEvent('AccessFilter');
            $results = $registry->getEntry('accessFilter');
            if ($results) {
                foreach ($results as $result) {
                    $formhooks->addOption($result['method'], $result['name']);
                }
            }
            $form->addElement($formhooks);
        */
        // links: css
        $formcss = new XoopsFormText(_AM_MYMENUS_MENU_CSS, 'css', 50, 255, $linksObj->getVar('css'));
        $form->addElement($formcss);
        //
        $buttonTray = new XoopsFormElementTray('', '');
        $buttonTray->addElement(new XoopsFormButton('', 'submit_button', _SUBMIT, 'submit'));
        $button = new XoopsFormButton('', 'reset', _CANCEL, 'button');
        if (isset($id)) {
            $button->setExtra("onclick=\"document.location.href='" . $GLOBALS['mymenusAdminPage'] . "?op=list&amp;mid={$mid}'\"");
        } else {
            $button->setExtra("onclick=\"document.getElementById('addform').style.display = 'none'; return false;\"");
        }
        $buttonTray->addElement($button);
        $form->addElement($buttonTray);

        if (isset($id)) {
            $form->addElement(new XoopsFormHidden('op', 'save'));
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
     * @param integer $id     of links object
     * @param integer $weight
     */
    public static function mymenusAdminMove($id, $weight)
    {
        $mymenus = MymenusMymenus::getInstance();
        //
        $linksObj = $mymenus->getHandler('links')->get((int)$id);
        $linksObj->setVar('weight', (int)$weight);
        $mymenus->getHandler('links')->insert($linksObj);
        $mymenus->getHandler('links')->updateWeights($linksObj);
    }

    /**
     * @param $id
     * @param $visible
     */
    public static function mymenusAdminToggle($id, $visible)
    {
        $mymenus = MymenusMymenus::getInstance();
        //
        // Disable xoops debugger in dialog window
        include_once $GLOBALS['xoops']->path('/class/logger/xoopslogger.php');
        $xoopsLogger            =& XoopsLogger::getInstance();
        $xoopsLogger->activated = false;
        error_reporting(0);
        //
        $linksObj = $mymenus->getHandler('links')->get((int)$id);
        $visible  = (1 === $linksObj->getVar('visible')) ? 0 : 1;
        $linksObj->setVar('visible', $visible);
        $mymenus->getHandler('links')->insert($linksObj);
        echo $linksObj->getVar('visible');
    }
}
