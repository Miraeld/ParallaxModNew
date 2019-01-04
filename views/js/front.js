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
function simpleParallax() {
	var velocity = 0.01;

  var scrolled = $(window).scrollTop();
  var movement = -(scrolled*velocity);
  $('.parallax-mod.parallax-img').css('background-position', '0' + movement + 'px');

}
//Everytime we scroll, it will fire the function
$(window).scroll(function (e) {
    simpleParallax();
});