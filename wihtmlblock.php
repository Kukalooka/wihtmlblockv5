<?php

if (!defined('_PS_VERSION_'))
    exit();

use PrestaShop\PrestaShop\Core\Module\WidgetInterface;

class Wihtmlblock extends Module implements WidgetInterface
{
    public function __construct()
    {
        $this->name = 'wihtmlblock';
        $this->version = '3.0.0';
        $this->author = 'WEBimpuls.pl';
        $this->need_instance = 1;
        $this->ps_versions_compliancy = array('min' => '1.7.1.0', 'max' => _PS_VERSION_);
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Wi HTML Block V3', 'wihtmlblock');
        $this->description = $this->l('Wi HTML Block V3', 'wihtmlblock');

        $this->confirmUninstall = $this->l('Wi HTML Block V3', 'wihtmlblock');
    }

    public function install()
    {
        if(Shop::isFeatureActive()){
            Shop::setContext(Shop::CONTEXT_ALL);
        }

        if(!parent::install() ||
            !$this->registerHook('leftColumn') ||
            !$this->registerHook('header') || 
            !$this->installTab() ||
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
            !Configuration::deleteByName('MYMODULE_NAME') ||
            !$this->uninstallTab()
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

    public function getContent()
    {
        $output = 'Created by WEBimpuls';

        return $output;
    }

    /**
     * Install menu tab.
     *
     * @return bool
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    private function installTab()
    {
        $tabId = (int) Tab::getIdFromClassName('AdminWihtmlblockController');
        if (!$tabId) {
        $tabId = null;
        }
        $tab = new Tab($tabId);
        $tab->active = 1;
        $tab->class_name = 'AdminWihtmlblockController';
        // Only since 1.7.7, you can define a route name
        $tab->route_name = 'blockRoute';
        $tab->name = array();
        foreach (Language::getLanguages() as $lang) {
            $tab->name[$lang['id_lang']] = $this->trans('HTML Blocks', array(), 'Modules.Wihtmlblock.Admin', 
            $lang['locale']);
        }
        $tab->id_parent = (int) Tab::getIdFromClassName('IMPROVE');
        $tab->module = $this->name;
        $tab->icon = 'vibration';
        return $tab->save();
    }
    /**
     * Uninstall menu tab.
     *
     * @return bool
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    private function uninstallTab()
    {
        $tabId = (int) Tab::getIdFromClassName('AdminWihtmlblockController');
        if (!$tabId) {
            return true;
        }
        $tab = new Tab($tabId);
        return $tab->delete();
    }

    public function isUsingNewTranslationSystem()
    {
        return true;
    }

    public function hookDisplayBackOfficeHeader()
    {
        if (Tools::getValue('configure') != $this->name) {
            return;
        }

        $this->context->controller->addJS($this->_path.' /test.js');
    }

}

?>