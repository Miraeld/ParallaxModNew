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
        $this->version = '1.0.0';
        $this->author = 'Gaël Robin';
        $this->need_instance = 0;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Parallax Module');
        $this->description = $this->l('This module is allowing you to add a parallax effect on the home page of your shop');

        $this->confirmUninstall = $this->l('Are you sure you want to delete this module?');

        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
    }

    public function install()
    {


        include(dirname(__FILE__).'/sql/install.php');

        return parent::install() &&
            $this->registerHook('header') &&
            $this->registerHook('backOfficeHeader') &&
            $this->registerHook('DisplayHome');
    }

    public function uninstall()
    {
        Configuration::deleteByName('PARALLAXMOD_TITLE');
        Configuration::deleteByName('PARALLAXMOD_TITLE_CSS');
        Configuration::deleteByName('PARALLAXMOD_IMAGE');
        Configuration::deleteByName('PARALLAXMOD_IMAGE_CSS');
        Configuration::deleteByName('PARALLAXMOD_HEIGHT');
        Configuration::deleteByName('PARALLAXMOD_RTE_CONTENT');

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
                      'col' => 4,
                      'type' => 'text',
                      'prefix' => '<i class="icon icon-wrench"></i>',
                      'desc' => $this->l('Enter a Title which will be displayed on the parallax effect'),
                      'name' => 'PARALLAXMOD_TITLE',
                      'label' => $this->l('Title'),
                      'lang' => true,
                      'required' => true,
                  ),
                  array(
                    'col' => 3,
                    'type' => 'text',
                    'prefix' => '<i class="icon icon-wrench"></i>',
                    'desc' => $this->l('CSS class for the title'),
                    'name' => 'PARALLAXMOD_TITLE_CSS',
                    'label' => $this->l('Title CSS Class'),
                  ),
                  array(
                    'type' => 'textarea',
                    'col' => 6,
                    'label' => $this->l('Description content:'),
                    'name' => 'PARALLAXMOD_RTE_CONTENT',
                    'lang' => true,
                    'cols' => 30,
                    'rows' => 10,
                    'class' => 'rte',
                    'autoload_rte' => true,
                    'hint' => $this->l('Invalid characters:').' <>;=#{}'
                  ),
                  array(
                    'type' => 'file',
                    'col' => 7,
                    'label' => $this->l('file_url'),
                    'name' => 'PARALLAXMOD_IMAGE',
                    'label' => $this->l('Background Image'),
                    'display_image' => true,
                    'required' => false,
                  ),
                  array(
                    'col' => 3,
                    'type' => 'text',
                    'prefix' => '<i class="icon icon-wrench"></i>',
                    'desc' => $this->l('CSS class for the image'),
                    'name' => 'PARALLAXMOD_IMAGE_CSS',
                    'label' => $this->l('CSS class'),
                  ),
                  array(
                    'col' => 3,
                    'type' => 'text',
                    'prefix' => '<i class="icon icon-wrench"></i>',
                    'desc' => $this->l('Height in pixels of the Parallax effect'),
                    'name' => 'PARALLAXMOD_HEIGHT',
                    'label' => $this->l('Height'),
                  )
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
      $languages = Language::getLanguages(false);
      $fields = array();

      foreach ($languages as $lang) {
        $data_db = $this->getData((int)$lang['id_lang'])[0];
        $fields['PARALLAXMOD_TITLE'][$lang['id_lang']] = $data_db['title_parallaxMod'];
        $fields['PARALLAXMOD_RTE_CONTENT'][$lang['id_lang']] = $data_db['main_body'];
      }
      $fields['PARALLAXMOD_TITLE_CSS'] = $data_db['title_css'];
      $fields['PARALLAXMOD_IMAGE'] = $data_db['img_path'];
      $fields['PARALLAXMOD_IMAGE_CSS'] = $data_db['img_css'];
      $fields['PARALLAXMOD_HEIGHT'] = $data_db['height'];
      return $fields;

    }

    /**
     * Save form data.
     */
    protected function postProcess()
    {

        if (Tools::isSubmit('submit_form')) { //  form submitted

          $this->allFields = array();

          $languages = Language::getLanguages(false);
          $values = array();
          $update_images_values = false;

          foreach ($languages as $lang) {
            $values['PARALLAXMOD_TITLE'][$lang['id_lang']] = Tools::getValue('PARALLAXMOD_TITLE_'.$lang['id_lang']);
            $values['PARALLAXMOD_RTE_CONTENT'][$lang['id_lang']] = Tools::getValue('PARALLAXMOD_RTE_CONTENT_'.$lang['id_lang']);

          }
          $values['PARALLAXMOD_TITLE_CSS'] = Tools::getValue('PARALLAXMOD_TITLE_CSS');
          $values['PARALLAXMOD_IMAGE'] = Tools::getValue('PARALLAXMOD_IMAGE');
          $values['PARALLAXMOD_IMAGE_CSS'] = Tools::getValue('PARALLAXMOD_IMAGE_CSS');
          $values['PARALLAXMOD_HEIGHT'] = Tools::getValue('PARALLAXMOD_HEIGHT');

          $this->allFields = $values;

          if (!empty($this->allFields['PARALLAXMOD_TITLE'][$this->context->language->id])) {


            if (!empty($this->allFields['PARALLAXMOD_IMAGE'])) {
              $this->checkImg($_FILES['PARALLAXMOD_IMAGE']);
            } else {
              $sql = new DbQuery();
              $sql->select('img_path');
              $sql->from('parallaxMod', 'a');
              $sql->where('a.main_id_parallaxMod = 1');
              $result = Db::getInstance()->executeS($sql);
              $this->allFields['PARALLAXMOD_IMAGE'] = $result[0]['img_path'];
            }
            $this->notificationDisplay($this->insertDb($this->allFields));
          } else {
            $this->notificationDisplay(false);
          }
        }
      }



    private function notificationDisplay($error) {
      if ($error) {
        ?>
          <script>
            window.onload = function () {
              var success = document.getElementById('social_bar_success');
              success.style.display = 'block';
            }
          </script>
        <?php
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
    private function checkImg($img) {
      $newName = $this->randomName(15) .'.'.pathinfo(basename($img['name']),PATHINFO_EXTENSION);
      $target_file = _PS_UPLOAD_DIR_ . $newName;
      $upload = true;

      $imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);

      if (getimagesize($img['tmp_name']) !== false) {
        $upload = true;
        if (!file_exists($target_file)) {
          switch ($imageFileType) {
            case "jpeg":
              $upload = true;
              break;
            case "jpg":
              $upload = true;
              break;
            case "png":
              $upload = true;
              break;
            default:
              $upload = false;
          }
        } else {
          $upload = false;
        }

      } else { // if (getimagezie($img['tmp_name']) !== false)
        $upload = false;
      }


      if ($upload) {
        if (move_uploaded_file($_FILES['PARALLAXMOD_IMAGE']["tmp_name"], $target_file))
        {

          $file_location = basename($_FILES['PARALLAXMOD_IMAGE']["name"]);
          $this->allFields['PARALLAXMOD_IMAGE'] = $newName;

          return true;
        }
        else
        {
          return false;
        }
      } else {
        // DISPLAY ERROR
        return false;
      }
    }
    private function randomName($length = 10) {
      return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
    }
    public function insertDb($values) {
      if (empty($values['PARALLAXMOD_HEIGHT'])) {
        $values['PARALLAXMOD_HEIGHT'] = 350;
      }
      $sql = array();
      foreach (Language::getLanguages(false) as $lang) {
        $val ='';
        $val .= 'title_parallaxMod = \''.$values['PARALLAXMOD_TITLE'][$lang['id_lang']].'\', ';
        $val .= 'title_css = \''. $values['PARALLAXMOD_TITLE_CSS'].'\', ';
        $val .= 'img_path = \''.$values['PARALLAXMOD_IMAGE'].'\', ';
        $val .= 'img_css = \''.$values['PARALLAXMOD_IMAGE_CSS'].'\', ';
        $val .= 'height = \''.$values['PARALLAXMOD_HEIGHT'].'\', ';
        $val .= 'main_body = \''.$values['PARALLAXMOD_RTE_CONTENT'][$lang['id_lang']].'\' ';

        $sql[] = 'UPDATE `'._DB_PREFIX_.'parallaxMod`
      			SET '. $val .'
      			WHERE id_parallaxMod = 1 AND id_lang = '.(int)$lang['id_lang'].';';
      }
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

        $datas = $this->getData($this->context->language->id)[0];
        // $datas['img_path'] = _PS_UPLOAD_DIR_ . $datas['img_path'];
        $this->context->smarty->assign('datas',$datas);

        if (substr($datas['img_path'],0,4) == "http") {
          $uploadDir =  $datas['img_path'];
        } else {
          $uploadDir = _PS_UPLOAD_DIR_.$datas['img_path'];
        }


        $this->context->smarty->assign('pathway_img',$uploadDir);
        $this->context->smarty->assign('module_dir', $this->_path);

        $output = $this->context->smarty->fetch($this->local_path.'views/templates/front/front.tpl');

        return $output;
        /* Display Home Hook
         Can show anything on homepage
        */
      }

    public function getData($id_lang) {
      $sql = new DbQuery();
      $sql->select('*');
      $sql->from('parallaxMod', 'a');
      $sql->where('a.id_parallaxMod = 1 AND a.id_lang = \''.$id_lang.'\'');
      $result = Db::getInstance()->executeS($sql);


      return $result;
    }
}
