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
 * Mymenus module
 *
 * @copyright       XOOPS Project (https://xoops.org)
 * @license         GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package         mymenus
 * @since           1.5
 * @author          Xoops Development Team
 */

use XoopsModules\Mymenus;

defined('XOOPS_ROOT_PATH') || die('Restricted access');

//$moduleDirname = basename(dirname(__DIR__));
//require(XOOPS_ROOT_PATH . "/modules/$moduleDirname/include/common.php");
require __DIR__ . '/common.php';
$helper = Mymenus\Helper::getInstance($debug);

xoops_loadLanguage('admin', $helper->getDirname());

/**
 * @param  object|\XoopsObject $xoopsModule
 * @param  int                $previousVersion
 * @return bool               FALSE if failed
 */
function xoops_module_update_mymenus(\XoopsObject $xoopsModule, $previousVersion)
{
    if ($previousVersion < 151) {
        //if (!checkInfoTemplates($xoopsModule)) return false;
        if (!Mymenus\Updater::checkInfoTable($xoopsModule)) {
            return false;
        }
        //update_tables_to_150($xoopsModule);
    }

    return true;
}


if (!function_exists('InfoColumnExists')) {
    /**
     * @param $tablename
     * @param $spalte
     *
     * @return bool
     */
    function InfoColumnExists($tablename, $spalte)
    {
        if ('' === $tablename || '' === $spalte) {
            return true;
        } // Fehler!!
        $result = $GLOBALS['xoopsDB']->queryF('SHOW COLUMNS FROM ' . $tablename . " LIKE '" . $spalte . "'");
        $ret    = $GLOBALS['xoopsDB']->getRowsNum($result) > 0;

        return $ret;
    }
}

if (!function_exists('InfoTableExists')) {
    /**
     * @param $tablename
     *
     * @return bool
     */
    function InfoTableExists($tablename)
    {
        $result = $GLOBALS['xoopsDB']->queryF("SHOW TABLES LIKE '$tablename'");
        $ret    = $GLOBALS['xoopsDB']->getRowsNum($result) > 0;

        return $ret;
    }
}
