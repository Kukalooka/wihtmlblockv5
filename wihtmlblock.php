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
        return $this->display(__FILE__,'template.tpl');
    }

    public function renderWidget($hookName, array $configuration)
    {

    }

    public function getWidgetVariables($hookName, array $configuration)
    {

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
                $output = $this->displayConfirmation($this->l('Settings updated'));
            }
        }

        // display any message, then the form
        return $output . $this->displayForm();
    }
}

?>