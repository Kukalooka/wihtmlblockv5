<?php

if (!defined('_PS_VERSION_'))
    exit();

use PrestaShop\PrestaShop\Core\Module\WidgetInterface;

class Wihtmlblock extends Module implements WidgetInterface
{
    public function __construct()
    {
        $this->name = 'wihtmlblock';
        $this->version = '1.0.0';
        $this->author = 'Alex';
        $this->need_instance = 1;
        $this->ps_versions_compliancy = array('min' => '1.7.1.0', 'max' => _PS_VERSION_);
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Wi HTML Block V1', 'wihtmlblock');
        $this->description = $this->l('Wi HTML Block V1', 'wihtmlblock');

        $this->confirmUninstall = $this->l('Wi HTML Block V1', 'wihtmlblock');
    }

    public function install()
    {
        if(Shop::isFeatureActive()){
            Shop::setContext(Shop::CONTEXT_ALL);
        }

        if(!parent::install() ||
            !$this->registerHook('leftColumn') ||
            !$this->registerHook('header') || 
            !Db::getInstance()->execute(
                'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'tpl_content` (
                `id` int(10) unsigned NOT NULL,
                `content` varchar(255) NOT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;') ||
            !Db::getInstance()->execute(
                'INSERT INTO `' . _DB_PREFIX_ . 'tpl_content` (`id`, `content`)
                VALUES (1, "Hello :>")') ||
            !Configuration::updateValue('MYMODULE_NAME', 'wihtmlblock')
        ) {
            return false;
        }
    }

    public function uninstall()
    {
        if(!parent::uninstall() || 
            !Configuration::deleteByName('MYMODULE_NAME')
        ){
            return false;
        }

        return true;
    }

    public function hookHeader()
    {
        $query=Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT content FROM `'._DB_PREFIX_.'tpl_content`');
        $custdata;
        foreach ($query as $row)
        {
            $custdata = $row['content'];
        }
        $this->context->smarty->assign("query", $custdata);
        return $this->display(__FILE__,'template.tpl');
    }

    public function renderWidget($hookName, array $configuration)
    {

    }

    public function getWidgetVariables($hookName, array $configuration)
    {

    }

    public function displayForm()
    {
        // Init Fields form array
        $form = [
            'form' => [
                'legend' => [
                    'title' => $this->l('Settings'),
                ],
                'input' => [
                    [
                        'type' => 'text',
                        'label' => $this->l('Configuration value'),
                        'name' => 'MYMODULE_CONFIG',
                        'size' => 20,
                        'required' => true,
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Save'),
                    'class' => 'btn btn-default pull-right',
                ],
            ],
        ];

        $helper = new HelperForm();

        // Module, token and currentIndex
        $helper->table = $this->table;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&' . http_build_query(['configure' => $this->name]);
        $helper->submit_action = 'submit' . $this->name;

        // Default language
        $helper->default_form_language = (int) Configuration::get('PS_LANG_DEFAULT');

        // Load current value into the form
        $helper->fields_value['MYMODULE_CONFIG'] = Tools::getValue('MYMODULE_CONFIG', Configuration::get('MYMODULE_CONFIG'));

        return $helper->generateForm([$form]);
    }

    public function getContent()
    {
        $output = '';

        // this part is executed only when the form is submitted
        if (Tools::isSubmit('submit' . $this->name)) {
            // retrieve the value set by the user
            $configValue = (string) Tools::getValue('MYMODULE_CONFIG');

            // check that the value is valid
            if (empty($configValue) || !Validate::isGenericName($configValue)) {
                // invalid value, show an error
                $output = $this->displayError($this->l('Invalid Configuration value'));
            } else {
                // value is ok, update it and display a confirmation message
                Configuration::updateValue('MYMODULE_CONFIG', $configValue);
                Db::getInstance()->execute(
                    'UPDATE `' . _DB_PREFIX_ . 'tpl_content` SET content ="' . $configValue . '"
                     WHERE id=1');
                $output = $this->displayConfirmation($this->l('Settings updated'));
            }
        }

        // display any message, then the form
        return $output . $this->displayForm();
    }
}

?>