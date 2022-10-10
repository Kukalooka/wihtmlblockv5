<?php

if (!defined('_PS_VERSION_'))
    exit();

use PrestaShop\PrestaShop\Core\Module\WidgetInterface;

class Wihtmlblockv5 extends Module implements WidgetInterface
{
    public function __construct()
    {
        $this->name = 'wihtmlblockv5';
        $this->version = '5.0.0';
        $this->author = 'WEBimpuls.pl';
        $this->need_instance = 1;
        $this->ps_versions_compliancy = array('min' => '1.7.1.0', 'max' => _PS_VERSION_);
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Wi HTML Block V5', 'wihtmlblockv5');
        $this->description = $this->l('Wi HTML Block V5', 'wihtmlblockv5');

        $this->confirmUninstall = $this->l('Wi HTML Block V5', 'wihtmlblockv5');
    }

    public function install()
    {
        if(Shop::isFeatureActive()){
            Shop::setContext(Shop::CONTEXT_ALL);
        }

        if(!parent::install() ||
            !$this->registerHook('leftColumn') ||
            !$this->registerHook('header') || 
            !$this->registerHook('actionAdminControllerSetMedia') || 
            !$this->installTab() ||
            !Db::getInstance()->execute(
                'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'tpl_content` (
                `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `content` varchar(255) NOT NULL,
                `toggle` BIT DEFAULT 0,
                `orderNum` int,
                `hook` varchar(255),
                PRIMARY KEY (`id`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;') ||
            !Configuration::updateValue('MYMODULE_NAME', 'wihtmlblockv5')
        ) {
            return false;
        }
    }

    public function uninstall()
    {
        if(!parent::uninstall() || 
            !Configuration::deleteByName('MYMODULE_NAME') ||
            !Db::getInstance()->execute('DROP TABLE `' . _DB_PREFIX_ . 'tpl_content`') || 
            !$this->uninstallTab()
        ){
            return false;
        }

        return true;
    }

    public function renderWidget($hookName, array $configuration)
    {
        $query=Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT content FROM `'._DB_PREFIX_.'tpl_content` WHERE toggle=0 AND hook="' . $hookName . '" ORDER BY orderNum');
        $custdata = array();
        foreach ($query as $row)
        {
            $custdata[] = $row['content'];
        }
        $this->context->smarty->assign("query", $custdata);
        return $this->display(__FILE__,'template.tpl');
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
        $tabId = (int) Tab::getIdFromClassName('AdminWihtmlblockv5Controller');
        if (!$tabId) {
        $tabId = null;
        }
        $tab = new Tab($tabId);
        $tab->active = 1;
        $tab->class_name = 'AdminWihtmlblockv5Controller';
        // Only since 1.7.7, you can define a route name
        $tab->route_name = 'blockRoutev5';
        $tab->name = array();
        foreach (Language::getLanguages() as $lang) {
            $tab->name[$lang['id_lang']] = $this->trans('HTML Blocks V5', array(), 'Modules.Wihtmlblockv5.Admin', 
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

    public function hookActionAdminControllerSetMedia($params)
    { 
        // Adds your's JavaScript file from a module's directory
        $this->context->controller->addJS($this->_path . 'Resources/public/js/editarea/edit_area/edit_area_full.js');
    }
}

?>