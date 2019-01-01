<?php
/**
* 2007-2018 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    Gaël ROBIN <gael@luxury-concept.com>
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*/
$sql = array();

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'parallaxMod` (
    `id_parallaxMod` int(11) NOT NULL AUTO_INCREMENT,
    `title_parallaxMod` varchar(255) NOT NULL,
    `title_css` TEXT,
    `subtitle_parallaxMod` varchar(255),
    `subtitle_css` TEXT,
    `img_path` TEXT,
    `img_css` TEXT,
    `height` int(11),
    `btn_txt` TEXT,
    `btn_css` TEXT,
    `btn_link` TEXT,
    `main_body` TEXT,
    PRIMARY KEY  (`id_parallaxMod`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';
//$sql[] = 'INSERT INTO `'. _DB_PREFIX_ .'parallaxMod` VALUES (\'1\',\'Title\',\'\', \'Subtitle\', \'\', \'path_img\', \'\',\'350\')';
$val = '(\'1\',\'Title\',\'titleCss\', \'Subtitle\', \'subCss\', \'https://bit.ly/2AjkHjm\', \'img-css\',\'350\',\'Button\',\'btn-css\',\'https://google.fr\',\'<p>Hello World</p>\')';
$sql[] = 'INSERT INTO `'._DB_PREFIX_.'parallaxMod` VALUES '. $val;
foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return false;
    }
}
