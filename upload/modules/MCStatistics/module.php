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
        $module_version = '1.1.0';
        $nameless_version = '2.0.0-pr10';
        
        parent::__construct($this, $name, $author, $module_version, $nameless_version);
        
        // Define URLs which belong to this module
        $pages->add('MCStatistics', '/panel/mcstatistics/settings', 'pages/panel/settings.php');
        $pages->add('MCStatistics', '/panel/mcstatistics/players', 'pages/panel/players.php');
        $pages->add('MCStatistics', '/panel/mcstatistics/player', 'pages/panel/player.php');
        
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
    
    public function onInstall(){
        // Initialise
        $this->initialise();
    }

    public function onUninstall(){
        // Not necessary
    }

    public function onEnable(){
        // Check if we need to initialise again
        $this->initialise();
    }

    public function onDisable(){
        // Not necessary
    }

    public function onPageLoad($user, $pages, $cache, $smarty, $navs, $widgets, $template){
        
        if(defined('BACK_END')){
            // Navigation
            $cache->setCache('panel_sidebar');
            
            PermissionHandler::registerPermissions($this->_mcstatistics_language->get('mcstatistics', 'mcstatistics'), array(
                'mcstatistics.settings' => $this->_mcstatistics_language->get('mcstatistics', 'mcstatistics') . ' &raquo; ' . $this->_mcstatistics_language->get('mcstatistics', 'settings'),
                'mcstatistics.players' => $this->_mcstatistics_language->get('mcstatistics', 'mcstatistics') . ' &raquo; ' . $this->_mcstatistics_language->get('mcstatistics', 'players')
            ));
            
            if($user->hasPermission('mcstatistics.settings') || $user->hasPermission('mcstatistics.players')){
                if(!$cache->isCached('mcstatistics_order')){
                    $order = 48;
                    $cache->store('mcstatistics_order', 48);
                } else {
                    $order = $cache->retrieve('mcstatistics_order');
                }
                
                if(!$cache->isCached('mcstatistics_icon')){
                    $icon = '<i class="nav-icon fas fa-wrench"></i>';
                    $cache->store('mcstatistics_icon', $icon);
                } else
                    $icon = $cache->retrieve('mcstatistics_icon');

                $navs[2]->add('mcstatistics_divider', mb_strtoupper($this->_mcstatistics_language->get('mcstatistics', 'mcstatistics'), 'UTF-8'), 'divider', 'top', null, $order, '');
                $navs[2]->addDropdown('mcstatistics', $this->_mcstatistics_language->get('mcstatistics', 'mcstatistics'), 'top', $order, $icon);
                
                if($user->hasPermission('mcstatistics.settings')) {
                    if(!$cache->isCached('mcstatistics_settings_icon')){
                        $icon = '<i class="nav-icon fas fa-cogs"></i>';
                        $cache->store('mcstatistics_settings_icon', $icon);
                    } else
                        $icon = $cache->retrieve('mcstatistics_settings_icon');
                    
                    $navs[2]->addItemToDropdown('mcstatistics', 'mcstatistics_settings', $this->_mcstatistics_language->get('mcstatistics', 'settings'), URL::build('/panel/mcstatistics/settings'), 'top', $order, $icon);
                }
                
                if($user->hasPermission('mcstatistics.players')) {
                    if(!$cache->isCached('mcstatistics_players_icon')){
                        $icon = '<i class="nav-icon fas fa-users"></i>';
                        $cache->store('mcstatistics_players_icon', $icon);
                    } else
                        $icon = $cache->retrieve('mcstatistics_players_icon');
                    
                    $navs[2]->addItemToDropdown('mcstatistics', 'mcstatistics_players', $this->_mcstatistics_language->get('mcstatistics', 'players'), URL::build('/panel/mcstatistics/players'), 'top', $order, $icon);
                }
                
                if(!$cache->isCached('mcstatistics_website_icon')){
                    $icon = '<i class="nav-icon fas fa-link"></i>';
                    $cache->store('mcstatistics_website_icon', $icon);
                } else
                    $icon = $cache->retrieve('mcstatistics_website_icon');
                
                $navs[2]->addItemToDropdown('mcstatistics', 'mcstatistics_website', $this->_mcstatistics_language->get('mcstatistics', 'view_website'), 'https://mcstatistics.org/', 'top', $order, $icon);
            }
        }
        
        // Check for module updates
        if(isset($_GET['route']) && $user->isLoggedIn() && $user->hasPermission('admincp.update')){
            if(rtrim($_GET['route'], '/') == '/panel/mcstatistics/settings' || rtrim($_GET['route'], '/') == '/panel/mcstatistics/players'){

                $cache->setCache('mcstatistics_module_cache');
                if($cache->isCached('update_check')){
                    $update_check = $cache->retrieve('update_check');
                } else {
                    require_once(ROOT_PATH . '/modules/MCStatistics/classes/MCStatistics.php');
                    $update_check = MCStatistics::updateCheck();
                    $cache->store('update_check', $update_check, 3600);
                }

                $update_check = json_decode($update_check);
                if(isset($update_check->premium)) {
                    $cache->setCache('partydragen');
                    $cache->store('premium', (bool) $update_check->premium);
                }
                
                if(!isset($update_check->error) && !isset($update_check->no_update) && isset($update_check->new_version)){
                    $smarty->assign(array(
                        'NEW_UPDATE' => str_replace('{x}', $this->getName(), (isset($update_check->urgent) && $update_check->urgent == 'true') ? $this->_mcstatistics_language->get('mcstatistics', 'new_urgent_update_available_x') : $this->_mcstatistics_language->get('mcstatistics', 'new_update_available_x')),
                        'NEW_UPDATE_URGENT' => (isset($update_check->urgent) && $update_check->urgent == 'true'),
                        'CURRENT_VERSION' => str_replace('{x}', $this->getVersion(), $this->_mcstatistics_language->get('mcstatistics', 'current_version_x')),
                        'NEW_VERSION' => str_replace('{x}', Output::getClean($update_check->new_version), $this->_mcstatistics_language->get('mcstatistics', 'new_version_x')),
                        'UPDATE' => $this->_mcstatistics_language->get('mcstatistics', 'view_resource'),
                        'UPDATE_LINK' => Output::getClean($update_check->link)
                    ));
                }
            }
        }
    }
    
    private function initialise(){
        // Generate tables
        try {
            $engine = Config::get('mysql/engine');
            $charset = Config::get('mysql/charset');
        } catch(Exception $e){
            $engine = 'InnoDB';
            $charset = 'utf8mb4';
        }

        if(!$engine || is_array($engine))
            $engine = 'InnoDB';

        if(!$charset || is_array($charset))
            $charset = 'latin1';

        $queries = new Queries();
        
        if(!$queries->tableExists('mcstatistics_settings')){
            try {
                $queries->createTable("mcstatistics_settings", " `id` int(11) NOT NULL AUTO_INCREMENT, `name` varchar(20) NOT NULL, `value` varchar(2048) NOT NULL, PRIMARY KEY (`id`)", "ENGINE=$engine DEFAULT CHARSET=$charset");
                
                // Insert data
                $queries->create('mcstatistics_settings', array(
                    'name' => 'secret_key',
                    'value' => ''
                ));
                $queries->create('mcstatistics_settings', array(
                    'name' => 'display_profile',
                    'value' => '1'
                ));
            } catch(Exception $e){
                // Error
            }
        }
        
        try {
            $queries->addPermissionGroup(2, 'mcstatistics.settings');
            $queries->addPermissionGroup(2, 'mcstatistics.players');
        } catch(Exception $e){
            // Error
        }
    }
}