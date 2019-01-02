<?php
/**
* 2007-2019 Gaël ROBIN
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
*  @copyright 2007-2019 Pimclick
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*
*/
$sql = array();



$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'parallaxMod` (
    `main_id_parallaxMod` int(11) NOT NULL AUTO_INCREMENT,
    `id_parallaxMod` int(11) NOT NULL,
    `title_parallaxMod` varchar(255) NOT NULL,
    `title_css` TEXT,
    `img_path` TEXT,
    `img_css` TEXT,
    `height` int(11),
    `main_body` TEXT,
    `id_lang` int(11) NOT NULL,
    PRIMARY KEY  (`main_id_parallaxMod`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

$parallax_content = '<p>This is the description content of the parallax Module<br /> You can edit it from the configuration page of Parallax Module</p>';
$i = 1;
foreach (Language::getLanguages(false) as $lang) {

  $val = "('".$i."','1','Main Title','parallax-title','https://bit.ly/2VkbIrj','parallax-img','350','".$parallax_content."','".(int)$lang['id_lang']."')";
  $sql[] = 'INSERT INTO `'._DB_PREFIX_.'parallaxMod` VALUES '. $val;
  $i++;
}


//
// $val = "('1','1','Main Title','parallax-title','https://bit.ly/2VkbIrj','parallax-img','350','".$parallax_content."','1')";


foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return false;
    }
}
