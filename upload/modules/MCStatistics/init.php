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

// Load classes
spl_autoload_register(function ($class) {
    $path = join(DIRECTORY_SEPARATOR, [ROOT_PATH, 'modules', 'MCStatistics', 'classes', $class . '.php']);
    if (file_exists($path)) {
        require_once($path);
    }
});
$mcstatistics = new MCStatistics();

require_once(ROOT_PATH . '/modules/MCStatistics/module.php');
$module = new MCStatistics_Module($language, $mcstatistics_language, $pages, $queries, $navigation, $cache);

// Profile page tab
try {
    if (Util::getSetting('display_profile', '1', 'MCStatistics')) {
        if (!isset($profile_tabs)) $profile_tabs = array();
        $profile_tabs['mcstatistics'] = array('title' => $mcstatistics_language->get('general', 'ingame'), 'smarty_template' => 'mcstatistics/profile_tab.tpl', 'require' => ROOT_PATH . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'MCStatistics' . DIRECTORY_SEPARATOR . 'profile_tab.php');
    }
} catch(Exception $e){
    // Error
}