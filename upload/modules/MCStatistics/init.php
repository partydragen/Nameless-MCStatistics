<?php 
/*
 *	Made by Partydragen
 *  https://github.com/partydragen/Nameless-MCStatistics
 *  https://partydragen.com/
 *
 *  License: MIT
 *
 *  MCStatistics module initialisation file
 */

// Initialise Suggestions language
$mcstatistics_language = new Language(ROOT_PATH . '/modules/MCStatistics/language', LANGUAGE);

// Initialise module
require_once(ROOT_PATH . '/modules/MCStatistics/module.php');
$module = new MCStatistics_Module($language, $mcstatistics_language, $pages, $queries, $navigation, $cache);

// Profile page tab
try {
    if($configuration->get('mcstatistics', 'display_profile') == 1) {
        if (!isset($profile_tabs)) $profile_tabs = array();
        $profile_tabs['mcstatistics'] = array('title' => $mcstatistics_language->get('mcstatistics', 'ingame'), 'smarty_template' => 'mcstatistics/profile_tab.tpl', 'require' => ROOT_PATH . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'MCStatistics' . DIRECTORY_SEPARATOR . 'profile_tab.php');
    }
} catch(Exception $e){
    // Error
}