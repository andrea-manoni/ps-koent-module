<?php

/**
 * 2007-2021 PrestaShop
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
 *  @copyright 2007-2021 PrestaShop SA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */



if (!defined('_PS_VERSION_')) {
    exit;
}


class testModule extends Module
{


    public function __construct()
    {
        $this->name = 'testModule';
        $this->tab = 'administration';
        $this->version = '1.1.0';
        $this->author = 'Andrea Manoni';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = [
            'min' => '1.6',
            'max' => _PS_VERSION_
        ];
        $this->bootstrap = true;

        //DASHBOARD
		$this->push_filename = _PS_CACHE_DIR_.'push/activity';
		$this->allow_push = true;
		$this->push_time_limit = 10;

        parent::__construct();

        $this->displayName = $this->l('Test Module');
        $this->description = $this->l('Prestashop test module, fetching data from api and showing it in backoffice.');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
    }

    public function getTableValue($name)
    {
        $sqlSearch = 'SELECT * FROM `prstshp_testmodule` WHERE `data_name`=\'' . $name . '\'';
        $result = Db::getInstance()->getRow($sqlSearch);
        if ($result) {

            return $result["data_value"];
        } else {
            return false;
        }
    }

    public function updateTableValue($name, $data)
    {
        $sql = 'UPDATE `prstshp_testmodule` SET `data_value`=\'' . $data . '\', `date_upd` = CURRENT_TIMESTAMP WHERE `data_name`=\'' . $name . '\'';

        return Db::getInstance()->execute($sql);
    }

    public function getTableValueAutoupdate($name)
    {
        $sqlSearch = 'SELECT * FROM `prstshp_testmodule` WHERE `data_name`=\'' . $name . '\'';
        $result = Db::getInstance()->getRow($sqlSearch);
        if ($result) {
            return $result["data_auto"];
        } else {
            return false;
        }
    }

    public function updateTableValueAutoupdate($name, $data)
    {
		
        $sql = 'UPDATE `prstshp_testmodule` SET `data_auto`=\'' . $data . '\', `date_upd` = CURRENT_TIMESTAMP WHERE `data_name`=\'' . $name . '\'';

        return Db::getInstance()->execute($sql);
    }


    public function insertFirstData()
    {
        $sqlName = 'INSERT INTO `prstshp_testmodule`(`data_name`, `data_value`,`data_json` , `data_auto`,`date_add`, `date_upd`) VALUES (\'TESTMODULE_NAME\', \'Username\',NULL, 0, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)';

        $sqlPass = 'INSERT INTO `prstshp_testmodule`(`data_name`, `data_value`,`data_json` , `data_auto`,`date_add`, `date_upd`) VALUES (\'TESTMODULE_PASSWORD\', \'pass\',NULL, 0, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)';

        $sqlUrl = 'INSERT INTO `prstshp_testmodule`(`data_name`, `data_value`,`data_json` , `data_auto`,`date_add`, `date_upd`) VALUES (\'TESTMODULE_BASEURL\', "jashgfgas" ,NULL, 0, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)';
      
        $sqlAuto = 'INSERT INTO `prstshp_testmodule`(`data_name`, `data_value`,`data_json` , `data_auto`,`date_add`, `date_upd`) VALUES (\'TESTMODULE_AUTO\', NULL ,NULL, 0, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)';

        $sqlUpd = 'INSERT INTO `prstshp_testmodule`(`data_name`, `data_value`,`data_json` , `data_auto`,`date_add`, `date_upd`) VALUES (\'TESTMODULE_UPDATE_TIME\', "10" ,NULL, 0, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)';


        $sqlOrders = 'INSERT INTO `prstshp_testmodule`(`data_name`, `data_value`,`data_json` , `data_auto`,`date_add`, `date_upd`) VALUES (\'TESTMODULE_ORDERS\', "oghorf" ,NULL, 0, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)';
       
        return Db::getInstance()->execute($sqlName) && Db::getInstance()->execute($sqlPass) && Db::getInstance()->execute($sqlUrl) && Db::getInstance()->execute($sqlAuto) && Db::getInstance()->execute($sqlUpd) && Db::getInstance()->execute($sqlOrders);
    }

    public function deleteTable()
    {
        $sql = "DROP TABLE prstshp_testmodule";

        return Db::getInstance()->execute($sql);
    }


    public function install()
    {
        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }

        $sql = "CREATE TABLE IF NOT EXISTS prstshp_testmodule (
            `id_testmodule` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `data_name` VARCHAR(50) NOT NULL,
            `data_value` VARCHAR(50) DEFAULT NULL,
            `data_json` JSON,
            `data_auto` BOOLEAN NOT NULL,
            `date_add` DATETIME DEFAULT CURRENT_TIMESTAMP,
            `date_upd` DATETIME DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;'";


        return
            Db::getInstance()->execute($sql) &&
            $this->insertFirstData() &&
            parent::install() &&
            $this->registerHook('displayOrderConfirmation') &&
            $this->registerHook('actionFrontControllerSetMedia') &&
            $this->registerHook('dashboardZoneTwo') &&
			$this->registerHook('dashboardData') &&
			$this->registerHook('actionAdminControllerSetMedia') &&
			$this->registerHook('displayAdminForm') &&
            $this->installTab();
    }





    public function enable($force_all = false)
    {
        return parent::enable($force_all)
            && $this->installTab();
    }


    public function disable($force_all = false)
    {
        return parent::disable($force_all)
            && $this->uninstallTab();
    }

    private function installTab()
    {
        $tabId = (int) Tab::getIdFromClassName('AdminController');
        if (!$tabId) {
            $tabId = null;
        }

        $tab = new Tab($tabId);
        $tab->active = 1;
        $tab->class_name = 'AdminController';
        // Only since 1.7.7, you can define a route name
        $tab->route_name = 'demo_tab_route';
        $tab->name = array();
        foreach (Language::getLanguages() as $lang) {
            $tab->name[$lang['id_lang']] = $this->trans('Demo Tab', array(), 'Modules.testModule.Admin', $lang['locale']);
        }
        $tab->id_parent = (int) Tab::getIdFromClassName('DEFAULT');
        $tab->module = $this->name;

        return $tab->save();
    }

    private function uninstallTab()
    {
        $tabId = (int) Tab::getIdFromClassName('AdminController');
        if (!$tabId) {
            return true;
        }

        $tab = new Tab($tabId);

        return $tab->delete();
    }

    public function uninstall()
    {
        return
            $this->uninstallTab() &&
            $this->deleteTable() &&
            parent::uninstall();
    }


    public function getContent()
    {
        $output = null;

        if (Tools::isSubmit('submit' . $this->name)) {
            $testModuleName = strval(Tools::getValue("TESTMODULE_NAME"));
            $testModulePass = strval(Tools::getValue("TESTMODULE_PASSWORD"));
            $testModuleBaseUrl = strval(Tools::getValue("TESTMODULE_BASEURL"));
			$testModuleAuto = (Tools::getValue("TESTMODULE_AUTO"));
			
            $testModuleUpdateTime = intval(Tools::getValue("TESTMODULE_UPDATE_TIME"));
           
            $testModuleOrders = strval(Tools::getValue("TESTMODULE_ORDERS"));
			
			
            if (
                !$testModuleName ||
                empty($testModuleName) ||
                !Validate::isGenericName($testModuleName)
            ) {
                $output .= $this->displayError($this->l('Invalid Username value'));
            } else {
                $this->updateTableValue('TESTMODULE_NAME', $testModuleName);
                $output .= $this->displayConfirmation($this->l('Username updated'));
            }

            if (
                !$testModulePass ||
                empty($testModulePass) ||
                !Validate::isGenericName($testModulePass)
            ) {
                $output .= $this->displayError($this->l('Invalid password value'));
            } else {
                $this->updateTableValue('TESTMODULE_PASSWORD', $testModulePass);
                $output .= $this->displayConfirmation($this->l('Password updated'));
            }

            if (
                !$testModuleBaseUrl ||
                empty($testModuleBaseUrl) ||
                !Validate::isGenericName($testModuleBaseUrl)
            ) {
                $output .= $this->displayError($this->l('Invalid Base url value'));
            } else {
                $this->updateTableValue('TESTMODULE_BASEURL', $testModuleBaseUrl);
                $output .= $this->displayConfirmation($this->l('Base url updated'));
            }

            if (
               ($testModuleAuto == 0) || ($testModuleAuto == 1)
            ) {
				
				 $this->updateTableValueAutoupdate('TESTMODULE_AUTO', $testModuleAuto);
                $output .= $this->displayConfirmation($this->l('Autoupdate updated'));
               
            } else {
                $output .= $this->displayError($this->l('Invalid Autoupdate value'));
            }

            if (
                !$testModuleUpdateTime ||
                empty($testModuleUpdateTime) ||
                !is_numeric($testModuleUpdateTime)
            ) {
                $output .= $this->displayError($this->l('Invalid Update time value'));
            } else {
                $this->updateTableValue('TESTMODULE_UPDATE_TIME', $testModuleUpdateTime);
                $output .= $this->displayConfirmation($this->l('Update time updated'));
            }


            if (
                !$testModuleOrders ||
                empty($testModuleOrders) ||
                !Validate::isGenericName($testModuleOrders)
            ) {
                $output .= $this->displayError($this->l('Invalid Orders value'));
            } else {
                $this->updateTableValue('TESTMODULE_ORDERS', $testModuleOrders);
                $output .= $this->displayConfirmation($this->l('Orders updated'));
            }

        
        
        
        
        }

        return $output . $this->displayForm();
    }


    public function displayForm()
    {


        // Get default language
        $defaultLang = (int)Configuration::get('PS_LANG_DEFAULT');

        // Init Fields form array
        $fieldsForm[0]['form'] = [
            'legend' => [
                'title' => $this->l('Settings'),
            ],
            'input' => [
                [
                    'type' => 'text',
                    'label' => $this->l('Username'),
                    'name' => 'TESTMODULE_NAME',
                    'size' => 20,
                    'required' => true
                ],
                [
                    'type' => 'password',
                    'label' => $this->l('Password'),
                    'name' => 'TESTMODULE_PASSWORD',
                    'size' => 20,
                    'required' => true
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('Base URL'),
                    'name' => 'TESTMODULE_BASEURL',
                    'size' => 20,
                    'required' => true
                ],
                [
                    'type' => 'switch',
                        'label' => $this->l('Do you want to autoupdate the data?'),
                        'name' => 'TESTMODULE_AUTO',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => 'yes',
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => 'no',
                            )
                        )
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('Time to pass before new updates in minutes'),
                    'name' => 'TESTMODULE_UPDATE_TIME',
                    'size' => 20,
                    'required' => true,
                ]
            ],
            'submit' => [
                'title' => $this->l('Save'),
                'class' => 'btn btn-default pull-right'
            ]
        ];

        $fieldsForm[1]['form'] = [
            'legend' => [
                'title' => $this->l('Orders Settings'),
            ],
            'input' => [
                [
                    'type' => 'text',
                    'label' => $this->l('Orders'),
                    'name' => 'TESTMODULE_ORDERS',
                    'size' => 20,
                    'required' => true
                ]
            ],
            'submit' => [
                'title' => $this->l('Save'),
                'class' => 'btn btn-default pull-right'
            ]
        ];

        $helper = new HelperForm();

        // Module, token and currentIndex
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;

        // Language
        $helper->default_form_language = $defaultLang;
        $helper->allow_employee_form_lang = $defaultLang;

        // Title and toolbar
        $helper->title = $this->displayName;
        $helper->show_toolbar = true;        // false -> remove toolbar
        $helper->toolbar_scroll = true;      // yes - > Toolbar is always visible on the top of the screen.
        $helper->submit_action = 'submit' . $this->name;
        $helper->toolbar_btn = [
            'save' => [
                'desc' => $this->l('Save'),
                'href' => AdminController::$currentIndex . '&configure=' . $this->name . '&save' . $this->name .
                    '&token=' . Tools::getAdminTokenLite('AdminModules'),
            ],
            'back' => [
                'href' => AdminController::$currentIndex . '&token=' . Tools::getAdminTokenLite('AdminModules'),
                'desc' => $this->l('Back to list')
            ]
        ];

        // Load current value
        $helper->fields_value['TESTMODULE_NAME'] = $this->getTableValue("TESTMODULE_NAME");
        $helper->fields_value['TESTMODULE_PASSWORD'] = $this->getTableValue("TESTMODULE_PASSWORD");
        $helper->fields_value['TESTMODULE_BASEURL'] = $this->getTableValue("TESTMODULE_BASEURL");
        $helper->fields_value['TESTMODULE_AUTO'] = $this->getTableValueAutoupdate("TESTMODULE_AUTO");
        $helper->fields_value['TESTMODULE_UPDATE_TIME'] = $this->getTableValue("TESTMODULE_UPDATE_TIME");

        $helper->fields_value['TESTMODULE_ORDERS'] = $this->getTableValue("TESTMODULE_ORDERS");

		
		
        return $helper->generateForm($fieldsForm) && $this->context->controller->addJS($this->_path.'views/js/back.js');
    }

    //HOOKS
	
	public function hookDisplayAdminForm()
{
	$this->context->controller->addJS($this->_path.'views/js/back.js');
}

	public function hookActionAdminControllerSetMedia()
{
	$this->context->controller->addJquery();
    // Adds your's JavaScript from a module's directory
    $this->context->controller->addJS($this->_path . 'views/js/testModule.js');
}
	
    public function hookDashboardZoneTwo($params)
{
    $this->context->smarty->assign(array(
		'text' => "TEST MODULE DASHBOARD"
	));
		
    return $this->display(__FILE__, 'dashboard_zone_two.tpl');
}
	
	public function hookDashboardData($params)
{
    $luckyNumber = $this->getRandomDataDashboard();
    
    return array(
        'data_value' => array(
            'luckyNumber' => $luckyNumber,
        )
    );
	}

    public function getRandomDataDashboard(){
        return "The lucky number is:".rand();
    }

    public function hookDisplayOrderConfirmation($params)
    {
        $this->context->smarty->assign([
            'my_module_name' => $this->getTableValue("TESTMODULE_NAME"),
            'my_module_password' => $this->getTableValue("TESTMODULE_PASSWORD"),
            'my_module_update_time' => $this->getTableValue("TESTMODULE_UPDATE_TIME"),
            'my_module_message' => $this->l('ORDER CONFIRMED!!!!!!!!!!!!!!!!!!!!!'),

        ]);

        return $this->display(__FILE__, 'testModule.tpl');
    }

    public function hookActionFrontControllerSetMedia()
    {

        $this->context->controller->registerStylesheet(
            'TESTMODULE-style',
            $this->_path . 'views/css/testModule.css',
            [
                'media' => 'all',
                'priority' => 1000,
            ]
        );

        $this->context->controller->registerJavascript(
            'TESTMODULE-javascript',
            $this->_path . 'views/js/testModule.js',
            [
                'position' => 'bottom',
                'priority' => 1000,
            ]
        );
    }
}
