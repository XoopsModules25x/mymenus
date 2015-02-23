<?php
/**
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright   The SIMPLE-XOOPS Project http://www.simple-xoops.de/
 * @license     GNU GPL 2 (http://www.gnu.org/licenses/old-licenses/gpl-2.0.html)
 * @version     $Id: xoops.php 12940 2015-01-21 17:33:38Z zyspec $
 */

defined('XOOPS_ROOT_PATH') || exit("Restricted access");

$module_name = basename(dirname(__DIR__));

if (!function_exists("xoops_loadLanguage")) {
    /**
     * @param        $name
     * @param string $domain
     * @param null   $language
     *
     * @return mixed
     */
    function xoops_loadLanguage($name, $domain = '', $language = null)
    {
        $language = empty($language) ? $GLOBALS['xoopsConfig']['language'] : $language;
        $path     = XOOPS_ROOT_PATH . '/' . ((empty($domain) || 'global' == $domain) ? '' : "modules/{$domain}/") . 'language';
        if (!($ret = @include_once "{$path}/{$language}/{$name}.php")) {
            $ret = @include_once "{$path}/english/{$name}.php";
        }

        return $ret;
    }
}

if (!function_exists("InfoTableExists")) {
    /**
     * @param $tablename
     *
     * @return bool
     */
    function InfoTableExists($tablename)
    {
        global $xoopsDB;
        $result = $xoopsDB->queryF("SHOW TABLES LIKE '$tablename'");
        $ret    = ($xoopsDB->getRowsNum($result) > 0) ? true : false;

        return $ret;
    }
}

if (!function_exists("InfoColumnExists")) {
    /**
     * @param $tablename
     * @param $spalte
     *
     * @return bool
     */
    function InfoColumnExists($tablename, $spalte)
    {
        global $xoopsDB;
        if ($tablename == "" || $spalte == "") {
            return true;
        } // Fehler!!
        $result = $xoopsDB->queryF("SHOW COLUMNS FROM " . $tablename . " LIKE '" . $spalte . "'");
        $ret    = ($xoopsDB->getRowsNum($result) > 0) ? true : false;

        return $ret;
    }
}

if (!function_exists("InfoAdminMenu")) {
    /**
     * @param int $currentoption
     */
    function InfoAdminMenu($currentoption = 0)
    {
        /* Nice buttons styles */
        global $xoopsConfig, $xoopsModule;
        $dirname = $xoopsModule->dirname();
        echo "
            <style type='text/css'>
            #buttontop { float:left; width:100%; background: #e7e7e7; font-size:93%; line-height:normal; border-top: 1px solid black; border-left: 1px solid black; border-right: 1px solid black; margin: 0; }
            #buttonbar { float:left; width:100%; background: #e7e7e7 url('" . XOOPS_URL . "/modules/$dirname/assets/images/bg.gif') repeat-x left bottom; font-size:93%; line-height:normal; border-left: 1px solid black; border-right: 1px solid black; margin-bottom: 12px; }
            #buttonbar ul { margin:0; margin-top: 15px; padding:10px 10px 0; list-style:none; }
            #buttonbar li { display:inline; margin:0; padding:0; }
            #buttonbar a { float:left; background:url('" . XOOPS_URL . "/modules/$dirname/assets/images/left_both.gif') no-repeat left top; margin:0; padding:0 0 0 9px; border-bottom:1px solid #000; text-decoration:none; }
            #buttonbar a span { float:left; display:block; background:url('" . XOOPS_URL . "/modules/$dirname/assets/images/right_both.gif') no-repeat right top; padding:5px 15px 4px 6px; font-weight:bold; color:#765; }
            /* Commented Backslash Hack hides rule from IE5-Mac \*/
            #buttonbar a span {float:none;}
            /* End IE5-Mac hack */
            #buttonbar a:hover span { color:#333; }
            #buttonbar #current a { background-position:0 -150px; border-width:0; }
            #buttonbar #current a span { background-position:100% -150px; padding-bottom:5px; color:#333; }
            #buttonbar a:hover { background-position:0% -150px; }
            #buttonbar a:hover span { background-position:100% -150px; }
            </style>";

        $myts      = &MyTextSanitizer::getInstance();
        $tblColors = array();
        xoops_loadLanguage("modinfo", $dirname);
        if (file_exists(XOOPS_ROOT_PATH . '/modules/' . $dirname . '/admin/menu.php')) {
            include XOOPS_ROOT_PATH . '/modules/' . $dirname . '/admin/menu.php';
        } else {
            $adminmenu = array();
        }
        echo "<table width=\"100%\" border='0'><tr><td>";
        echo "<div id='buttontop'>";
        echo "<table style=\"width: 100%; padding: 0; \" cellspacing=\"0\"><tr>";
        echo "<td style=\"width: 45%; font-size: 10px; text-align: left; color: #2F5376; padding: 0 6px; line-height: 18px;\"><a class=\"nobutton\" href=\"../../system/admin.php?fct=preferences&amp;op=showmod&amp;mod=" . $xoopsModule->getVar('mid') . "\">" . _PREFERENCES . "</a></td>";
        echo "<td style='font-size: 10px; text-align: right; color: #2F5376; padding: 0 6px; line-height: 18px;'><b>" . $myts->displayTarea($xoopsModule->name()) . "</td>";
        echo "</tr></table>";
        echo "</div>";
        echo "<div id='buttonbar'>";
        echo "<ul>";
        foreach ($adminmenu as $key => $value) {
            $tblColors[$key]           = '';
            $tblColors[$currentoption] = 'current';
            echo "<li id='" . $tblColors[$key] . "'><a href=\"" . XOOPS_URL . "/modules/" . $xoopsModule->getVar('dirname') . "/" . $value['link'] . "\"><span>" . $value['title'] . "</span></a></li>";
        }
        echo "</ul></div>";
        echo "</td></tr></table>";
    }
}

if (!function_exists("self_parrent")) {
    /**
     * @param int    $id
     * @param string $modulname
     *
     * @return array
     */
    function self_parrent($id = 0, $modulname = "")
    {
        global $xoopsDB;
        $currentParent = array();
        if ($modulname == "") {
            return $currentParent;
        }
        $cP     = $id;
        $sql    = "SELECT storyid FROM " . $xoopsDB->prefix($modulname) . " WHERE parent_id=" . intval($cP);
        $result = $xoopsDB->query($sql);
        while ($row == $xoopsDB->fetchArray($result)) {
            if (intval($row['storyid']) > 0) {
                $cP              = intval($row['storyid']);
                $currentParent[] = $cP;
                $cp2             = array();
                $cp2             = self_parrent($cP, $modulname);
                if (count($cp2) > 0) {
                    foreach ($cp2 as $clist1) {
                        $currentParent[] = $clist1;
                    }
                }
                unset($cp2);
            }
        }

        return $currentParent;
    }
}
