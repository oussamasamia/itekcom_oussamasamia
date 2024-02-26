<?php
/**
 * 2007-2024 PrestaShop
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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2024 PrestaShop SA
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

$autoloadPath = __DIR__ . '/vendor/autoload.php';
if (file_exists($autoloadPath)) {
    require_once $autoloadPath;
}

class Itekcom_oussamasamia extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'itekcom_oussamasamia';
        $this->tab = 'administration';
        $this->version = '2.0.0';
        $this->author = 'Oussama SAMIA';
        $this->need_instance = 1;

        /***
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Itekcom Module');
        $this->description = $this->l('Itekcom module description');

        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => '8.1.5');
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {

        // Add the "doctor" field to the ps_customer table
        $sql = 'ALTER TABLE `' . _DB_PREFIX_ . 'customer` ADD COLUMN `doctor` VARCHAR(255) DEFAULT NULL';
        if (!Db::getInstance()->execute($sql)) {
            return false; // Return false if the query execution fails
        }

        // Add the "github_id" field to the ps_customer table
        $sql2 = 'ALTER TABLE `' . _DB_PREFIX_ . 'customer` ADD COLUMN `github_id` INT(11) DEFAULT NULL';
        if (!Db::getInstance()->execute($sql2)) {
            return false; // Return false if the query execution fails
        }


        Configuration::updateValue('ITEKCOM_OUSSAMASAMIA_CLIENT_ID', "");
        Configuration::updateValue('ITEKCOM_OUSSAMASAMIA_CLIENT_SECRET', "");


        // Register hooks
        return parent::install() &&
            $this->registerHook('header') &&
            $this->registerHook('displayBackOfficeHeader') &&
            $this->registerHook('displayGitHubLoginButton');
    }

    public function uninstall()
    {
        // Remove the "doctor" field from the ps_customer table
        $sql = 'ALTER TABLE `' . _DB_PREFIX_ . 'customer` DROP COLUMN `doctor`';
        if (!Db::getInstance()->execute($sql)) {
            return false; // Return false if the query execution fails
        }

        // Remove the "github_id" field from the ps_customer table
        $sql2 = 'ALTER TABLE `' . _DB_PREFIX_ . 'customer` DROP COLUMN `github_id`';
        if (!Db::getInstance()->execute($sql2)) {
            return false; // Return false if the query execution fails
        }

        Configuration::deleteByName('ITEKCOM_OUSSAMASAMIA_CLIENT_ID');
        Configuration::deleteByName('ITEKCOM_OUSSAMASAMIA_CLIENT_SECRET');

        return parent::uninstall();
    }


    public function initiateGitHubAuthentication()
    {
        $clientId = Configuration::get('ITEKCOM_OUSSAMASAMIA_CLIENT_ID');
        $state = bin2hex(random_bytes(16)); // Generate a random state

        $authorizationUrl = 'https://github.com/login/oauth/authorize' .
            '?client_id=' . $clientId .
            '&state=' . urlencode($state);

        return $authorizationUrl;
    }

    /**
     * Github Login form
     */
    public function hookDisplayGitHubLoginButton($params)
    {

        $githubLoginUrl = $this->initiateGitHubAuthentication();
        // Assign any necessary variables to be used in the template
        //$githubLoginUrl = $this->context->link->getModuleLink('itekcom_oussamasamia', 'githubauth');

        // Create a Smarty instance
        $smarty = $this->context->smarty;

        // Assign variables to Smarty
        $smarty->assign(array(
            'github_login_url' => $githubLoginUrl,
        ));

        // Fetch the content of the template file
        $buttonHtml = $this->display(__FILE__, 'github_login_button.tpl');

        return $buttonHtml;
    }


    /**
     * Load the configuration form
     */
    public function getContent()
    {
        /**
         * If values have been submitted in the form, process.
         */
        if (((bool)Tools::isSubmit('submitItekcom_oussamasamiaModule')) == true) {
            $this->postProcess();
        }

        $this->context->smarty->assign('module_dir', $this->_path);

        $output = $this->context->smarty->fetch($this->local_path . 'views/templates/admin/configure.tpl');

        return $output . $this->renderForm();
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
        $helper->submit_action = 'submitItekcom_oussamasamiaModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
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
                    'title' => $this->l('Settings'),
                    'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'col' => 3,
                        'type' => 'text',
                        'name' => 'ITEKCOM_OUSSAMASAMIA_CLIENT_ID',
                        'label' => $this->l('Client ID'),
                    ),
                    array(
                        'type' => 'password',
                        'name' => 'ITEKCOM_OUSSAMASAMIA_CLIENT_SECRET',
                        'label' => $this->l('Client Secret'),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
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
            'ITEKCOM_OUSSAMASAMIA_CLIENT_ID' => Configuration::get('ITEKCOM_OUSSAMASAMIA_CLIENT_ID', null),
            'ITEKCOM_OUSSAMASAMIA_CLIENT_SECRET' => Configuration::get('ITEKCOM_OUSSAMASAMIA_CLIENT_SECRET', null),
        );
    }


    /**
     * Save form data.
     */
    protected function postProcess()
    {
        $form_values = $this->getConfigFormValues();

        foreach (array_keys($form_values) as $key) {
            Configuration::updateValue($key, Tools::getValue($key));
        }
    }

    /**
     * Add the CSS & JavaScript files you want to be loaded in the BO.
     */
    public function hookDisplayBackOfficeHeader()
    {
        if (Tools::getValue('configure') == $this->name) {
            $this->context->controller->addJS($this->_path . 'views/js/back.js');
            $this->context->controller->addCSS($this->_path . 'views/css/back.css');
        }
    }

    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookHeader()
    {
        $this->context->controller->addJS($this->_path . '/views/js/front.js');
        $this->context->controller->addCSS($this->_path . '/views/css/front.css');
    }
}
