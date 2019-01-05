<?php
/**
* 2007-2019 PrestaShop
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
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2019 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/
$sql = array();


//
// $sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'parallaxMod` (
//     `main_id_parallaxMod` int(11) NOT NULL AUTO_INCREMENT,
//     `id_parallaxMod` int(11) NOT NULL,
//     `title_parallaxMod` varchar(255) NOT NULL,
//     `title_css` TEXT,
//     `img_path` TEXT,
//     `img_css` TEXT,
//     `height` int(11),
//     `main_body` TEXT,
//     `id_lang` int(11) NOT NULL,
//     PRIMARY KEY  (`main_id_parallaxMod`)
// ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';
//
// $parallax_content = '<p>This is the description content of the parallax Module<br /> You can edit it from the configuration page of Parallax Module</p>';
// $i = 1;
// foreach (Language::getLanguages(false) as $lang) {
//
//   $val = "('".$i."','1','Main Title','parallax-title','https://bit.ly/2VkbIrj','parallax-img','350','".$parallax_content."','".(int)$lang['id_lang']."')";
//   $sql[] = 'INSERT INTO `'._DB_PREFIX_.'parallaxMod` VALUES '. $val;
//   $i++;
// }


//
// $val = "('1','1','Main Title','parallax-title','https://bit.ly/2VkbIrj','parallax-img','350','".$parallax_content."','1')";

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'parallaxMod` (
	   `id_parallaxMod` int(10) NOT NULL,
     `title_css` varchar(255),
     `img_path` TEXT,
     `img_css` varchar(255),
     `height` int(10),
     `title_color` varchar(255),
     `title_size` varchar(255),
     PRIMARY KEY  (`id_parallaxMod`)
   ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';


$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'parallaxMod_lang` (
    `id_parallaxMod` int(10) NOT NULL,
    `id_lang` int(10) NOT NULL,
    `title` TEXT,
    `main_body` TEXT,
	PRIMARY KEY (`id_parallaxMod`,`id_lang`)
  ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

$parallax_content = '<p style="text-align:center; color:white; font-size:20px">This is the description content of the parallax Module<br /> You can edit it from the configuration page of Parallax Module</p>';

$parallaxMod_value = "(1,'titleCSS','https://bit.ly/2VkbIrj','parallax-img', 350,'#fff', '25px')";
$sql[] = 'INSERT INTO `'._DB_PREFIX_.'parallaxMod`
            VALUES '. $parallaxMod_value;


foreach (Language::getLanguages(false) as $lang) {
  $lang_value = "(1, ".$lang['id_lang'].",'Main Title', '".$parallax_content."')";
  $sql[] = 'INSERT INTO `'._DB_PREFIX_.'parallaxMod_lang`
            VALUES '. $lang_value;
}

foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return false;
    }
}
