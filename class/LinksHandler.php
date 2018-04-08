<?php namespace XoopsModules\Mymenus;

/*
 You may not change or alter any portion of this comment or credits
 of supporting developers from this source code or any supporting source code
 which is considered copyrighted (c) material of the original comment or credit authors.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

/**
 * @copyright       XOOPS Project (https://xoops.org)
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU Public License
 * @package         Mymenus
 * @since           1.0
 * @author          trabis <lusopoemas@gmail.com>
 */

use XoopsModules\Mymenus;

defined('XOOPS_ROOT_PATH') || die('Restricted access');

require_once __DIR__ . '/../include/common.php';


/**
 * Class MymenusLinksHandler
 */
class LinksHandler extends \XoopsPersistableObjectHandler
{
    /**
     * @var Mymenus\Helper
     * @access private
     */
    private $helper = null;

    /**
     * @param null|\XoopsDatabase $db
     */
    public function __construct(\XoopsDatabase $db)
    {
        parent::__construct($db, 'mymenus_links', Links::class, 'id', 'title');
        $this->helper = Mymenus\Helper::getInstance();
    }

    /**
     * @param $obj
     */
    public function updateWeights($obj)
    {
        $sql = 'UPDATE ' . $this->table . ' SET weight = weight+1';
        $sql .= ' WHERE';
        $sql .= ' weight >= ' . $obj->getVar('weight');
        $sql .= ' AND';
        $sql .= ' id <> ' . $obj->getVar('id');
        //$sql .= " AND pid = " . $obj->getVar('pid');
        $sql .= ' AND';
        $sql .= ' mid = ' . $obj->getVar('mid');
        $this->db->queryF($sql);

        $sql = 'SELECT id FROM ' . $this->table;
        $sql .= ' WHERE mid = ' . $obj->getVar('mid');
        //$sql .= " AND pid = " . $obj->getVar('pid');
        $sql    .= ' ORDER BY weight ASC';
        $result = $this->db->query($sql);
        $i      = 1;  //lets start at 1 please!
        while (false !== (list($id) = $this->db->fetchRow($result))) {
            $sql = 'UPDATE ' . $this->table;
            $sql .= " SET weight = {$i}";
            $sql .= " WHERE id = {$id}";
            $this->db->queryF($sql);
            ++$i;
        }
    }
}
