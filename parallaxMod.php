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

if (!defined('_PS_VERSION_')) {
    exit;
} //!defined('_PS_VERSION_')
class ParallaxMod extends Module
{
    protected $config_form = false;
    public function __construct()
    {
        $this->name          = 'parallaxmod';
        $this->tab           = 'administration';
        $this->version       = '2.0.1';
        $this->author        = 'Gael Robin';
        $this->need_instance = 1;
        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap     = true;
        parent::__construct();
        $this->displayName            = $this->l('Parallax Module');
        $this->description            = $this->l('This module is allowing you to add a parallax effect on your shop homepage');
        $this->confirmUninstall       = $this->l('Are you sure you want to delete this module?');
        $this->ps_versions_compliancy = array(
            'min' => '1.7',
            'max' => _PS_VERSION_
        );
    }
    public function install()
    {
        include(dirname(__FILE__) . '/sql/install.php');
        return parent::install() && $this->registerHook('header') && $this->registerHook('backOfficeHeader') && $this->registerHook('displayHome');
    }
    public function uninstall()
    {
        include(dirname(__FILE__) . '/sql/uninstall.php');
        return parent::uninstall();
    }
    /**
     * Load the configuration form
     */
    public function getContent()
    {
        if (((bool) Tools::isSubmit('submitParallaxModModule')) == true) {
            $this->postProcess();
        } //((bool) Tools::isSubmit('submitParallaxModModule')) == true
        $this->context->smarty->assign('module_dir', $this->_path);
        $output = $output . $this->renderForm();
        $output .= $this->context->smarty->fetch($this->local_path . 'views/templates/admin/configure.tpl');
        return $output;
    }
    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderForm()
    {
        $helper                           = new HelperForm();
        $helper->show_toolbar             = false;
        $helper->table                    = $this->table;
        $helper->module                   = $this;
        $helper->default_form_language    = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);
        $helper->identifier               = $this->identifier;
        $helper->submit_action            = 'submitParallaxModModule';
        $helper->currentIndex             = $this->context->link->getAdminLink('AdminModules', false) . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token                    = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars                 = array(
            'fields_value' => $this->getConfigFormValues(),
            /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );
        return $helper->generateForm(array(
            $this->getConfigForm()
        ));
    }

    protected function getConfigForm()
    {
        return array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Parallax Module Settings'),
                    'icon' => 'icon-cogs'
                ),
                'input' => array(
                    array(
                        'col' => 4,
                        'type' => 'text',
                        'prefix' => '<i class="icon icon-wrench"></i>',
                        'desc' => $this->l('Enter a Title which will be displayed on the parallax effect'),
                        'name' => 'title',
                        'label' => $this->l('Title'),
                        'lang' => true,
                        'required' => true
                    ),
                    array(
                        'col' => 3,
                        'type' => 'text',
                        'prefix' => '<i class="icon icon-wrench"></i>',
                        'desc' => $this->l('CSS class for the title'),
                        'name' => 'title_css',
                        'label' => $this->l('Title CSS Class')
                    ),
                    array(
                        'type' => 'color',
                        'label' => $this->l('Title Color'),
                        'name' => 'title_color',
                        'col' => 8
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Title size'),
                        'name' => 'title_size',
                        'desc' => $this->l('Please enter the unit such as (20px, 20pt, etc...)'),
                        'col' => 3
                    ),
                    array(
                        'type' => 'textarea',
                        'label' => $this->l('Description content:'),
                        'name' => 'main_body',
                        'lang' => true,
                        'cols' => 30,
                        'rows' => 10,
                        'class' => 'rte',
                        'autoload_rte' => true,
                        'hint' => $this->l('Invalid characters:') . ' <>;=#{}'
                    ),
                    array(
                        'type' => 'file',
                        'col' => 7,
                        'label' => $this->l('file_url'),
                        'name' => 'img_path',
                        'label' => $this->l('Background Image'),
                        'display_image' => true,
                        'required' => false
                    ),
                    array(
                        'col' => 3,
                        'type' => 'text',
                        'prefix' => '<i class="icon icon-wrench"></i>',
                        'desc' => $this->l('CSS class for the image'),
                        'name' => 'img_css',
                        'label' => $this->l('Image CSS class')
                    ),
                    array(
                        'col' => 3,
                        'type' => 'text',
                        'prefix' => '<i class="icon icon-wrench"></i>',
                        'desc' => $this->l('Height in pixels of the Parallax effect'),
                        'name' => 'height',
                        'label' => $this->l('Height')
                    ),
                    array(
                        'type' => 'hidden',
                        'name' => 'pathforimg'
                    )
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                    'name' => 'submit_form'
                )
            )
        );
    }

    protected function getConfigFormValues()
    {
        $languages = Language::getLanguages(false);
        $fields    = array();
        foreach ($languages as $lang) {
            $datas_db                              = $this->getData($lang['id_lang']);
            $data_db                               = $datas_db[0];
            $fields['title'][$lang['id_lang']]     = $data_db['title'];
            $fields['main_body'][$lang['id_lang']] = $data_db['main_body'];
        } //$languages as $lang
        $fields['title_css']   = $data_db['title_css'];
        $fields['img_path']    = $data_db['img_path'];
        $fields['img_css']     = $data_db['img_css'];
        $fields['height']      = $data_db['height'];
        $fields['title_color'] = $data_db['title_color'];
        $fields['title_size']  = $data_db['title_size'];
        $fields['pathforimg']  = $data_db['img_path'];
        return $fields;
    }
    /**
     * Save form data.
     */
    protected function postProcess()
    {
        if (Tools::isSubmit('submit_form')) { //  form submitted
            $this->allFields = array();
            $languages       = Language::getLanguages(false);
            $values          = array();
            foreach ($languages as $lang) {
                $values['title'][$lang['id_lang']]     = Tools::getValue('title_' . $lang['id_lang']);
                $values['main_body'][$lang['id_lang']] = Tools::getValue('main_body_' . $lang['id_lang']);
            } //$languages as $lang
            $values['title_css']   = Tools::getValue('title_css');
            $values['img_path']    = Tools::getValue('img_path');
            $values['img_css']     = Tools::getValue('img_css');
            $values['height']      = Tools::getValue('height');
            $values['title_size']  = Tools::getValue('title_size');
            $values['title_color'] = Tools::getValue('title_color');
            $this->allFields       = $values;
            if (!empty($this->allFields['title'][$this->context->language->id])) {
                if (!empty($this->allFields['img_path'])) {
                    $this->checkImg($_FILES['img_path']);
                } //!empty($this->allFields['img_path'])
                else {
                    $sql = new DbQuery();
                    $sql->select('img_path');
                    $sql->from('parallaxMod', 'a');
                    $sql->where('a.id_parallaxMod = 1');
                    $result                      = Db::getInstance()->executeS($sql);
                    $this->allFields['img_path'] = $result[0]['img_path'];
                }
                $this->notificationDisplay($this->insertDb($this->allFields));
            } //!empty($this->allFields['title'][$this->context->language->id])
            else {
                $this->notificationDisplay(false);
            }
        } //Tools::isSubmit('submit_form')
    }
    private function notificationDisplay($error)
    {
        if ($error) {
?>
         <script>
            window.onload = function () {
              var success = document.getElementById('social_bar_success');
              success.style.display = 'block';
            }
          </script>
        <?php
        } //$error
        else {
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
    private function checkImg($img)
    {
        $newName       = $this->randomName(15) . '.' . pathinfo(basename($img['name']), PATHINFO_EXTENSION);
        $target_file   = _PS_UPLOAD_DIR_ . $newName;
        $upload        = true;
        $imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);
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
                } //$imageFileType
            } //!file_exists($target_file)
            else {
                $upload = false;
            }
        } //getimagesize($img['tmp_name']) !== false
        else { // if (getimagezie($img['tmp_name']) !== false)
            $upload = false;
        }
        if ($upload) {
            if (move_uploaded_file($_FILES['img_path']["tmp_name"], $target_file)) {
                $this->allFields['img_path'] = $newName;
                return true;
            } //move_uploaded_file($_FILES['img_path']["tmp_name"], $target_file)
            else {
                return false;
            }
        } //$upload
        else {
            return false;
        }
    }
    private function randomName($length = 10)
    {
        $x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        return Tools::substr(str_shuffle(str_repeat($x, ceil($length / Tools::strlen($x)))), 1, $length);
    }
    public function insertDb($values)
    {
        $sql = array();
        foreach (Language::getLanguages(false) as $lang) {
            $val = '';
            foreach ($values as $key => $value) {
                if (!empty($val) && (Tools::substr($val, -1, 2) != ', '))
                    $val .= ', ';
                if (!empty($value)) {
                    if ($key=='title' || $key=='main_body') {
                      $val.=$key.' = \''.$val[$lang['id_lang']].'\'';
                    } else {
                      $val .= $key . ' = \'' . $value . '\'';
                    }
                }
            } //$values as $key => $value
            $sql[] = 'UPDATE `' . _DB_PREFIX_ . 'parallaxMod` as pm
              LEFT JOIN `' . _DB_PREFIX_ . 'parallaxMod_lang` as pml ON pm.id_parallaxMod = pml.id_parallaxMod
              SET ' . $val . '
              WHERE pml.id_lang = ' . $lang['id_lang'];
        } //Language::getLanguages(false) as $lang
        foreach ($sql as $query) {
            if (Db::getInstance()->execute($query) == false) {
                return false;
            } //Db::getInstance()->execute($query) == false
        } //$sql as $query
        return true;
    }
    /**
     * Add the CSS & JavaScript files you want to be loaded in the BO.
     */
    public function hookBackOfficeHeader()
    {
        if (Tools::getValue('module_name') == $this->name) {
            $this->context->controller->addJS($this->_path . 'views/js/back.js');
            $this->context->controller->addCSS($this->_path . 'views/css/back.css');
        } //Tools::getValue('module_name') == $this->name
    }
    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookHeader()
    {
        $this->context->controller->addJS($this->_path . '/views/js/front.js');
        $this->context->controller->addCSS($this->_path . '/views/css/front.css');
    }
    public function hookDisplayHome()
    {
        $data  = $this->getData($this->context->language->id);
        $datas = $data[0];
        // $datas['img_path'] = _PS_UPLOAD_DIR_ . $datas['img_path'];
        $this->context->smarty->assign('datas', $datas);
        if (Tools::substr($datas['img_path'], 0, 4) == "http") {
            $uploadDir = $datas['img_path'];
        } //Tools::substr($datas['img_path'], 0, 4) == "http"
        else {
            $uploadDir = _PS_UPLOAD_DIR_ . $datas['img_path'];
        }
        $this->context->smarty->assign('pathway_img', $uploadDir);
        $this->context->smarty->assign('module_dir', $this->_path);
        $output = $this->context->smarty->fetch($this->local_path . 'views/templates/front/front.tpl');
        return $output;
    }
    public function getData($id_lang)
    {
        $sql = new DbQuery();
        $sql->select('*');
        $sql->from('parallaxMod', 'pm');
        $sql->leftJoin('parallaxMod_lang', 'pml', 'pm.id_parallaxMod = pml.id_parallaxMod');
        $sql->where('pml.id_lang = ' . $id_lang);
        $result = Db::getInstance()->executeS($sql);
        return $result;
    }
}
