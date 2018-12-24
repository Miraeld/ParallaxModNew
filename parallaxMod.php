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
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2018 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

class ParallaxMod extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'parallaxMod';
        $this->tab = 'front_office_features';
        $this->version = '0.0.1';
        $this->author = 'Gaël Robin';
        $this->need_instance = 0;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Parallax Module');
        $this->description = $this->l('This is a module to add parallax effect to your shop');

        $this->confirmUninstall = $this->l('Are you sure you want to delete this module?');

        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        Configuration::updateValue('PARALLAXMOD_LIVE_MODE', false);

        include(dirname(__FILE__).'/sql/install.php');

        return parent::install() &&
            $this->registerHook('header') &&
            $this->registerHook('backOfficeHeader') &&
            $this->registerHook('displayHome');
    }

    public function uninstall()
    {
        Configuration::deleteByName('PARALLAXMOD_LIVE_MODE');

        include(dirname(__FILE__).'/sql/uninstall.php');

        return parent::uninstall();
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        /**
         * If values have been submitted in the form, process.
         */
        if (((bool)Tools::isSubmit('submitParallaxModModule')) == true) {
            $this->postProcess();
        }

        $this->context->smarty->assign('module_dir', $this->_path);
        $output = $output.$this->renderForm();
        $output .= $this->context->smarty->fetch($this->local_path.'views/templates/admin/configure.tpl');

        return $output;
    }

    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitParallaxModModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($this->getConfigForm()));
    }

    /**
     * Create the structure of your form.
     */
    protected function getConfigForm()
    {
      return array(
          'form' => array(
              'legend' => array(
              'title' => $this->l('Parallax Module Settings'),
              'icon' => 'icon-cogs',
              ),
              'input' => array(

                  array(
                      'col' => 3,
                      'type' => 'text',
                      'prefix' => '<i class="icon icon-wrench"></i>',
                      'desc' => $this->l('Enter a valid Title'),
                      'name' => 'PARALLAXMOD_TITLE',
                      'label' => $this->l('Title'),
                      'required' => true,
                  ),
                  array(
                    'col' => 3,
                    'type' => 'text',
                    'prefix' => '<i class="icon icon-wrench"></i>',
                    'desc' => $this->l('Class CSS for title'),
                    'name' => 'PARALLAXMOD_TITLE_CSS',
                    'label' => $this->l('Class CSS'),
                  ),
                  array(
                      'col' => 3,
                      'type' => 'text',
                      'prefix' => '<i class="icon icon-wrench"></i>',
                      'desc' => $this->l('Enter a valid subtitle'),
                      'name' => 'PARALLAXMOD_SUBTITLE',
                      'label' => $this->l('Subtitle'),
                  ),
                  array(
                    'col' => 3,
                    'type' => 'text',
                    'prefix' => '<i class="icon icon-wrench"></i>',
                    'desc' => $this->l('Class CSS for subtitle'),
                    'name' => 'PARALLAXMOD_SUBTITLE_CSS',
                    'label' => $this->l('Class CSS'),
                  ),
                  array(
                    'type' => 'file',
                    'col' => 7,
                    'label' => $this->l('file_url'),
                    'name' => 'PARALLAXMOD_IMAGE',
                    'label' => $this->l('Background Image'),
                    'display_image' => true,
                    'required' => true,
                  ),
                  array(
                    'col' => 3,
                    'type' => 'text',
                    'prefix' => '<i class="icon icon-wrench"></i>',
                    'desc' => $this->l('Class CSS for the image'),
                    'name' => 'PARALLAXMOD_IMAGE_CSS',
                    'label' => $this->l('Class CSS'),
                  ),
              ),
              'submit' => array(
                  'title' => $this->l('Save'),
                  'name' => 'submit_form',
              ),
          ),
      );
    }

    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValues()
    {
        return array(
            'PARALLAXMOD_TITLE' => Configuration::get('PARALLAXMOD_TITLE'),
            'PARALLAXMOD_TITLE_CSS' => Configuration::get('PARALLAXMOD_TITLE_CSS'),
            'PARALLAXMOD_SUBTITLE' => Configuration::get('PARALLAXMOD_SUBTITLE'),
            'PARALLAXMOD_SUBTITLE_CSS' => Configuration::get('PARALLAXMOD_SUBTITLE_CSS'),
            'PARALLAXMOD_IMAGE' => Configuration::get('PARALLAXMOD_IMAGE'),
            'PARALLAXMOD_IMAGE_CSS' => Configuration::get('PARALLAXMOD_IMAGE_CSS'),
        );
    }

    /**
     * Save form data.
     */
    protected function postProcess()
    {
        $form_values = $this->getConfigFormValues();
        if (Tools::isSubmit('submit_form')) {
          $allFields = Array();
          foreach (array_keys($form_values) as $key) {
              Configuration::updateValue($key, Tools::getValue($key));
              $allFields[$key]=Tools::getValue($key);
          }

          if (!empty($allFields['PARALLAXMOD_IMAGE']) && !empty($allFields['PARALLAXMOD_TITLE'])) {
            if ($this->insertDb($allFields)) {
              ?>
                <script>
                  window.onload = function () {
                    var success = document.getElementById('social_bar_success');
                    success.style.display = 'block';
                  }
                </script>
              <?php
            }


          } else {
            ?>
            <script>

            window.onload = function () {
              var error = document.getElementById('social_bar_error');
              error.style.display = 'block';

            }
            </script>
            <?php
          }
        }
    }
    public function insertDb($values) {
      $sql = Array();
      $sql[] = 'UPDATE `'._DB_PREFIX_.'parallaxMod`
                SET title_parallaxMod = \''.$values['PARALLAXMOD_TITLE'].'\', title_css = \''.$values['PARALLAXMOD_TITLE_CSS'].'\', subtitle_parallaxMod = \''.$values['PARALLAXMOD_SUBTITLE'].'\', subtitle_css = \''.$values['PARALLAXMOD_SUBTITLE_CSS'].'\', img_path = \''.$values['PARALLAXMOD_IMAGE'].'\', img_css = \''.$values['PARALLAXMOD_IMAGE_CSS'].'\'
                    WHERE id_parallaxMod =1;';
      foreach ($sql as $query) {
          if (Db::getInstance()->execute($query) == false) {
              return false;
          }
      }
      return true;
    }
    /**
    * Add the CSS & JavaScript files you want to be loaded in the BO.
    */
    public function hookBackOfficeHeader()
    {
        if (Tools::getValue('module_name') == $this->name) {
            $this->context->controller->addJS($this->_path.'views/js/back.js');
            $this->context->controller->addCSS($this->_path.'views/css/back.css');
        }
    }

    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookHeader()
    {
        $this->context->controller->addJS($this->_path.'/views/js/front.js');
        $this->context->controller->addCSS($this->_path.'/views/css/front.css');

    }

    public function hookDisplayHome()
    {

      $this->context->smarty->assign('module_dir', $this->_path);

      $output = $this->context->smarty->fetch($this->local_path.'views/templates/front/front.tpl');

      return $output;
      /* Display Home Hook
       Can show anything on homepage
      */
    }
}
