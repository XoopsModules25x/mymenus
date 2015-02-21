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
 * @version         $Id: xoops_version.php 13003 2015-02-20 04:45:42Z zyspec $
 */

defined("XOOPS_ROOT_PATH") || exit("Restricted access");

$modversion['name']           = _MI_MYMENUS_MD_NAME;
$modversion['version']        = 1.51;
$modversion['description']    = _MI_MYMENUS_MD_DESC;
$modversion['credits']        = "Xuups";
$modversion['author']         = "Trabis (www.xuups.com)";
$modversion['help']           = 'page=help';
$modversion['license']        = 'GNU GPL 2.0';
$modversion['license_url']    = "www.gnu.org/licenses/gpl-2.0.html";
$modversion['official']       = 0;
$modversion['image']          = "assets/images/mymenus.png";
$modversion['dirname']        = basename(__DIR__);
$modversion['dirmoduleadmin'] = 'Frameworks/moduleclasses/moduleadmin';
$modversion['icons16']        = 'Frameworks/moduleclasses/icons/16';
$modversion['icons32']        = 'Frameworks/moduleclasses/icons/32';

//help files
$modversion['helpsection'] = array(array('name' => "Overview",
                                         'link' => "page=help"),

                                   array('name' => "Skins",
                                         'link' => "page=skins"),
                                   array('name' => "Usage",
                                         'link' => "page=usage")
);

//about
$modversion["module_status"]       = "Beta 2";
$modversion['release_date']        = '2015/02/19';
$modversion["module_website_url"]  = "www.xoops.org";
$modversion["module_website_name"] = "XOOPS";
$modversion["author_website_url"]  = "http://www.xuups.com/";
$modversion["author_website_name"] = "Xuups";
$modversion['min_php']             = '5.3.7';
$modversion['min_xoops']           = "2.5.7";
$modversion['min_admin']           = '1.1';
$modversion['min_db']              = array('mysql' => '5.0.7',
                                          'mysqli' => '5.0.7');

//update
$modversion['onUpdate'] = 'include/update.php'; //module.php

// Menu
$modversion['hasMain']     = 0;

// Admin things
$modversion['hasAdmin']    = 1;
$modversion['system_menu'] = 1;
$modversion['adminindex']  = "admin/index.php";
$modversion['adminmenu']   = "admin/menu.php";

// Search
$modversion['hasSearch']   = 0;

// Comments
$modversion['hasComments'] = 0;

// Sql
$modversion['sqlfile']['mysql'] = "sql/mysql.sql";

$modversion['tables'] = array("mymenus_links",
                              "mymenus_menus"
);

// Config
$modversion['config'] = array(array('name' => 'assign_method',
                                   'title' => '_MI_MENUS_CONF_ASSIGN_METHOD',
                             'description' => '_MI_MENUS_CONF_ASSIGN_METHOD_DSC',
                                'formtype' => 'select',
                               'valuetype' => 'text',
                                 'default' => 'xotheme',
                                 'options' => array(_MI_MENUS_CONF_ASSIGN_METHOD_XOOPSTPL => 'xoopstpl',
                                                     _MI_MENUS_CONF_ASSIGN_METHOD_XOTHEME => 'xotheme'))
);

// Blocks
$modversion['blocks'] = array(array('file' => "mymenus_block.php",
                                    'name' => _MI_MYMENUS_BLK,
                             'description' => _MI_MYMENUS_BLK_DSC,
                               'show_func' => "mymenus_block_show",
                               'edit_func' => "mymenus_block_edit",
                                 'options' => "0|default|0| |block|0",
                                'template' => "mymenus_block.tpl")
);
