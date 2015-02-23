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
 * @version     $Id: update.php 12940 2015-01-21 17:33:38Z zyspec $
 */

include dirname(__DIR__) . "/include/xoops.php";
$infoname = basename(dirname(__DIR__));

//Install
eval('function xoops_module_pre_install_' . $infoname . '($module) {
  // Templatevorlagen prüfen
  if (!check_infotemplates($module)) return false;
  if (!check_infotable($module)) return false;
  return true;
}');

//Install
eval('function xoops_module_install_' . $infoname . '($module) {
  // Templatevorlagen prüfen
  if (!check_infotemplates($module)) return false;
  if (!check_infotable($module)) return false;
  return true;
}');

//Update
eval('function xoops_module_update_' . $infoname . '($module) {
  // Templatevorlagen prüfen
  if (!check_infotemplates($module)) return false;
  if (!check_infotable($module)) return false;
  return true;
}');

/**
 * @param $module
 *
 * @return bool
 */
function check_infotemplates($module)
{
    $err = true;
    if (!file_exists(XOOPS_ROOT_PATH . "/modules/" . $module->getInfo("dirname") . "/templates/blocks/" . $module->getInfo("dirname") . "_block.tpl")) {
        $module->setErrors("Template " . $module->getInfo("dirname") . "_block.tpl not exists!");
        $err = false;
    }

    return $err;
}

/**
 * @param $module
 *
 * @return bool
 */
function check_infotable($module)
{
    global $xoopsDB;
    $err = true;

    $tables_menus = array("id"    => "int(5) NOT NULL auto_increment",
                          "title" => "varchar(150) NOT NULL default ''",
                          "css"   => "varchar(150) NOT NULL default ''"
    );

    $tables_links = array("id"        => "int(5) NOT NULL auto_increment",
                          "pid"       => "int(5) NOT NULL default '0'",
                          "mid"       => "int(5) NOT NULL default '0'",
                          "title"     => "varchar(150) NOT NULL default ''",
                          "alt_title" => "varchar(150) NOT NULL default ''",
                          "visible"   => "tinyint(1) NOT NULL default '0'",
                          "link"      => "varchar(255) default NULL",
                          "weight"    => "tinyint(4) NOT NULL default '0'",
                          "target"    => "varchar(10) default NULL",
                          "groups"    => "text default NULL",
                          "hooks"     => "text default NULL",
                          "image"     => "varchar(150) default NULL",
                          "css"       => "varchar(150) default NULL"
    );

    if (!InfoTableExists($xoopsDB->prefix($module->getInfo("dirname")) . '_menus')) {
        $sql = "CREATE TABLE " . $xoopsDB->prefix($module->getInfo("dirname")) . "_menus (";
        foreach ($tables_menus as $s => $w) {
            $sql .= " " . $s . " " . $w . ",";
        }
        $sql .= " PRIMARY KEY  (id)
                ); ";

        echo $sql;
        $result = $xoopsDB->queryF($sql);
        if (!$result) {
            $module->setErrors("Can't create Table " . $xoopsDB->prefix($module->getInfo("dirname")) . '_menus');

            return false;
        } else {
            $sql    = "INSERT INTO " . $xoopsDB->prefix($module->getInfo("dirname")) . "_menus (id,title) VALUES (1,'Default')";
            $result = $xoopsDB->queryF($sql);
        }
    } else {
        foreach ($tables_menus as $s => $w) {
            if (!InfoColumnExists($xoopsDB->prefix($module->getInfo("dirname")) . '_menus', $s)) {
                $sql    = "ALTER TABLE " . $xoopsDB->prefix($module->getInfo("dirname")) . "_menus " . $s . " " . $w . ";";
                $result = $xoopsDB->queryF($sql);
            } else {
                $sql    = "ALTER TABLE " . $xoopsDB->prefix($module->getInfo("dirname")) . "_menus CHANGE " . $s . " " . $s . " " . $w . ";";
                $result = $xoopsDB->queryF($sql);
            }
        }
    }

    if (!InfoTableExists($xoopsDB->prefix($module->getInfo("dirname")) . "_links")) {
        $sql = "CREATE TABLE " . $xoopsDB->prefix($module->getInfo("dirname")) . "_links ( ";
        foreach ($tables_links as $c => $w) {
            $sql .= " " . $c . " " . $w . ",";
        }
        $sql .= "  PRIMARY KEY  (storyid) ) ;";
        $result = $xoopsDB->queryF($sql);
        if (!$result) {
            $module->setErrors("Can't create Table " . $xoopsDB->prefix($module->getInfo("dirname")) . "_links");
            $sql    = 'DROP TABLE ' . $xoopsDB->prefix($module->getInfo("dirname")) . '_menus';
            $result = $xoopsDB->queryF($sql);

            return false;
        }
    } else {
        foreach ($tables_links as $s => $w) {
            if (!InfoColumnExists($xoopsDB->prefix($module->getInfo("dirname")) . '_links', $s)) {
                $sql    = "ALTER TABLE " . $xoopsDB->prefix($module->getInfo("dirname")) . "_links ADD " . $s . " " . $w . ";";
                $result = $xoopsDB->queryF($sql);
            } else {
                $sql    = "ALTER TABLE " . $xoopsDB->prefix($module->getInfo("dirname")) . "_links CHANGE " . $s . " " . $s . " " . $w . ";";
                $result = $xoopsDB->queryF($sql);
            }
        }
    }

    return true;
}
