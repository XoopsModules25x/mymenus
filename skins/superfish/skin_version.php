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
 * @copyright       XOOPS Project (https://xoops.org)
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU Public License
 * @package         Mymenus
 * @since           1.0
 * @author          trabis <lusopoemas@gmail.com>
 */

$skinVersion['template'] = 'templates/template.tpl';

$skinVersion['css'] = 'css/superfish.css';

$skinVersion['js'] = [
    '../../../../browse.php?Frameworks/jquery/jquery.js',
    // '../../assets/js/jquery-1.11.2.min.js',
    '../../assets/js/hoverIntent.js',
    '../../assets/js/superfish.js'
];

$header = "\n" . '<script type="text/javascript">';
$header .= "\n" . '  var $sf = jQuery.noConflict()';
$header .= "\n" . '  $sf(function () {';
$header .= "\n" . '    $sf(\'ul.sf-menu\').superfish({';
$header .= "\n" . '       delay:       1000,';
$header .= "\n" . '       animation:   {opacity:\'show\',height:\'show\'},';
$header .= "\n" . '       speed:       \'fast\'';
$header .= "\n" . '    });';
$header .= "\n" . '  });';
$header .= "\n" . '</script>';

$skinVersion['header'] = $header;
