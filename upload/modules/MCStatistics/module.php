<?php
/*
 *	Made by Partydragen
 *  https://github.com/partydragen/Nameless-MCStatistics
 *  https://partydragen.com/
 *
 *  License: MIT
 *
 *  MCStatistics module file
 */
 
class MCStatistics_Module extends Module {
    private $_language;
    private $_mcstatistics_language;
    
    public function __construct($language, $mcstatistics_language, $pages, $queries, $navigation, $cache){
        $this->_language = $language;
        $this->_mcstatistics_language = $mcstatistics_language;
        
        $name = 'MCStatistics';
        $author = '<a href="https://partydragen.com" target="_blank" rel="nofollow noopener">Partydragen</a>';
        $module_version = '1.2.1';
        $nameless_version = '2.0.2';
        
        parent::__construct($this, $name, $author, $module_version, $nameless_version);
        
        // Define URLs which belong to this module
        $pages->add('MCStatistics', '/panel/mcstatistics/settings', 'pages/panel/settings.php');
        $pages->add('MCStatistics', '/panel/mcstatistics/players', 'pages/panel/players.php');
        $pages->add('MCStatistics', '/panel/mcstatistics/player', 'pages/panel/player.php');

        if (Util::isModuleEnabled('Store')) {
            require_once(ROOT_PATH . '/modules/MCStatistics/hooks/CheckoutPlayerHook.php');
            EventHandler::registerListener('storeCheckoutAddProduct', 'CheckoutPlayerHook::minPlayerAge');
            EventHandler::registerListener('storeCheckoutAddProduct', 'CheckoutPlayerHook::minPlayerPlaytime');
        }

        if (Util::isModuleEnabled('Forms')) {
            require_once(ROOT_PATH . '/modules/MCStatistics/hooks/FormPlayerHook.php');
            EventHandler::registerListener('renderForm', 'FormPlayerHook::minPlayerAge');
            EventHandler::registerListener('renderForm', 'FormPlayerHook::minPlayerPlaytime');
        }

        // Check if module version changed
        $cache->setCache('mcstatistics_module_cache');
        if(!$cache->isCached('module_version')){
            $cache->store('module_version', $module_version);
        } else {
            if($module_version != $cache->retrieve('module_version')) {
                // Version have changed, Perform actions
                $cache->store('module_version', $module_version);
                
                if($cache->isCached('update_check')){
                    $cache->erase('update_check');
                }
            }
        }
    }
    
    public function onInstall() {
        // Initialise
        $this->initialise();
    }

    public function onUninstall() {
        // Not necessary
    }

    public function onEnable() {
        // Check if we need to initialise again
        $this->initialise();
    }

    public function onDisable() {
        // Not necessary
    }

    public function onPageLoad($user, $pages, $cache, $smarty, $navs, $widgets, $template){
        
        if(defined('BACK_END')){
            // Navigation
            $cache->setCache('panel_sidebar');
            
            PermissionHandler::registerPermissions($this->_mcstatistics_language->get('general', 'mcstatistics'), array(
                'mcstatistics.settings' => $this->_mcstatistics_language->get('general', 'mcstatistics') . ' &raquo; ' . $this->_mcstatistics_language->get('general', 'settings'),
                'mcstatistics.players' => $this->_mcstatistics_language->get('general', 'mcstatistics') . ' &raquo; ' . $this->_mcstatistics_language->get('general', 'players')
            ));
            
            if ($user->hasPermission('mcstatistics.settings') || $user->hasPermission('mcstatistics.players')){
                if (!$cache->isCached('mcstatistics_order')){
                    $order = 48;
                    $cache->store('mcstatistics_order', 48);
                } else {
                    $order = $cache->retrieve('mcstatistics_order');
                }
                
                if (!$cache->isCached('mcstatistics_icon')){
                    $icon = '<i class="nav-icon fas fa-wrench"></i>';
                    $cache->store('mcstatistics_icon', $icon);
                } else
                    $icon = $cache->retrieve('mcstatistics_icon');

                $navs[2]->add('mcstatistics_divider', mb_strtoupper($this->_mcstatistics_language->get('general', 'mcstatistics'), 'UTF-8'), 'divider', 'top', null, $order, '');
                $navs[2]->addDropdown('mcstatistics', $this->_mcstatistics_language->get('general', 'mcstatistics'), 'top', $order, $icon);
                
                if ($user->hasPermission('mcstatistics.settings')) {
                    if(!$cache->isCached('mcstatistics_settings_icon')){
                        $icon = '<i class="nav-icon fas fa-cogs"></i>';
                        $cache->store('mcstatistics_settings_icon', $icon);
                    } else
                        $icon = $cache->retrieve('mcstatistics_settings_icon');
                    
                    $navs[2]->addItemToDropdown('mcstatistics', 'mcstatistics_settings', $this->_mcstatistics_language->get('general', 'settings'), URL::build('/panel/mcstatistics/settings'), 'top', $order, $icon);
                }
                
                if ($user->hasPermission('mcstatistics.players')) {
                    if(!$cache->isCached('mcstatistics_players_icon')){
                        $icon = '<i class="nav-icon fas fa-users"></i>';
                        $cache->store('mcstatistics_players_icon', $icon);
                    } else
                        $icon = $cache->retrieve('mcstatistics_players_icon');
                    
                    $navs[2]->addItemToDropdown('mcstatistics', 'mcstatistics_players', $this->_mcstatistics_language->get('general', 'players'), URL::build('/panel/mcstatistics/players'), 'top', $order, $icon);
                }
                
                if (!$cache->isCached('mcstatistics_website_icon')){
                    $icon = '<i class="nav-icon fas fa-link"></i>';
                    $cache->store('mcstatistics_website_icon', $icon);
                } else
                    $icon = $cache->retrieve('mcstatistics_website_icon');
                
                $navs[2]->addItemToDropdown('mcstatistics', 'mcstatistics_website', $this->_mcstatistics_language->get('general', 'view_website'), 'https://mcstatistics.org/', 'top', $order, $icon);
            }
            
            if ($user->hasPermission('mcstatistics.players'))
                Core_Module::addUserAction($this->_mcstatistics_language->get('general', 'mcstatistics'), URL::build('/panel/mcstatistics/player/{username}'));
        }
        
        // Check for module updates
        if(isset($_GET['route']) && $user->isLoggedIn() && $user->hasPermission('admincp.update')){
            // Page belong to this module?
            $page = $pages->getActivePage();
            if ($page['module'] == 'MCStatistics') {
                $cache->setCache('mcstatistics_module_cache');
                if($cache->isCached('update_check')){
                    $update_check = $cache->retrieve('update_check');
                } else {
                    $update_check = MCStatistics::updateCheck();
                    $cache->store('update_check', $update_check, 3600);
                }

                $update_check = json_decode($update_check);
                if (!isset($update_check->error) && !isset($update_check->no_update) && isset($update_check->new_version)) {  
                    $smarty->assign([
                        'NEW_UPDATE' => (isset($update_check->urgent) && $update_check->urgent == 'true') ? $this->_mcstatistics_language->get('general', 'new_urgent_update_available_x', ['module' => $this->getName()]) : $this->_mcstatistics_language->get('general', 'new_update_available_x', ['module' => $this->getName()]),
                        'NEW_UPDATE_URGENT' => (isset($update_check->urgent) && $update_check->urgent == 'true'),
                        'CURRENT_VERSION' => $this->_mcstatistics_language->get('general', 'current_version_x', ['version' => Output::getClean($this->getVersion())]),
                        'NEW_VERSION' => $this->_mcstatistics_language->get('general', 'new_version_x', ['new_version' => Output::getClean($update_check->new_version)]),
                        'NAMELESS_UPDATE' => $this->_mcstatistics_language->get('general', 'view_resource'),
                        'NAMELESS_UPDATE_LINK' => Output::getClean($update_check->link)
                    ]);
                }
            }
        }
    }
    
    private function initialise(){
        // Generate tables
    }
    
    public function getDebugInfo(): array {
        return [];
    }
}