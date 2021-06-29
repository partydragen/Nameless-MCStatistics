<?php
/*
 *  Made by Partydragen
 *  https://github.com/partydragen/Nameless-MCStatistics
 *  https://partydragen.com/
 *
 *  MCStatistics settings page
 */

// Can the user view the panel?
if(!$user->handlePanelPageLoad('mcstatistics.settings')) {
    require_once(ROOT_PATH . '/403.php');
    die();
}

define('PAGE', 'panel');
define('PARENT_PAGE', 'mcstatistics');
define('PANEL_PAGE', 'mcstatistics_settings');
$page_title = $language->get('admin', 'general_settings');
require_once(ROOT_PATH . '/core/templates/backend_init.php');

// Deal with input
if (Input::exists()) {
    $errors = array();
    if (Token::check(Input::get('token'))) {
        $validate = new Validate();

        $validation = $validate->check($_POST, array(
            'secret_key' => array(
				'min' => 64,
				'max' => 64
			),
        ));
        
        if($validation->passed()){
            // Update secret key
            $configuration->set('mcstatistics', 'secret_key', Input::get('secret_key'));
            
            // Update show stats on profile page value
            if(isset($_POST['display_profile']) && $_POST['display_profile'] == 'on') $display_profile = 1;
            else $display_profile = 0;
            $configuration->set('mcstatistics', 'display_profile', $display_profile);
            
            Session::flash('mcstatistics_success', $mcstatistics_language->get('mcstatistics', 'settings_updated_successfully'));
            Redirect::to(URL::build('/panel/mcstatistics/settings'));
            die();
        } else {
            foreach($validation->errors() as $error){
				if(strpos($error, 'secret_key') !== false)
					$errors[] = $mcstatistics_language->get('mcstatistics', 'invalid_secret_key');
			}
        }
    } else {
        // Invalid token
        $errors[] = $language->get('general', 'invalid_token');
    }
}

// Load modules + template
Module::loadPage($user, $pages, $cache, $smarty, array($navigation, $cc_nav, $mod_nav), $widgets);

if(Session::exists('mcstatistics_success'))
    $success = Session::flash('mcstatistics_success');

if(isset($success)){
    $smarty->assign(array(
        'SUCCESS_TITLE' => $language->get('general', 'success'),
        'SUCCESS' => $success
    ));
}

if(isset($errors) && count($errors)){
    $smarty->assign(array(
        'ERRORS_TITLE' => $language->get('general', 'error'),
        'ERRORS' => $errors
    ));
}

$smarty->assign(array(
    'PARENT_PAGE' => PARENT_PAGE,
    'DASHBOARD' => $language->get('admin', 'dashboard'),
    'PAGE' => PANEL_PAGE,
    'MCSTATISTICS' => $mcstatistics_language->get('mcstatistics', 'mcstatistics'),
    'SETTINGS' => $mcstatistics_language->get('mcstatistics', 'settings'),
    'INFO' => $language->get('general', 'info'),
    'SECRET_KEY' => $mcstatistics_language->get('mcstatistics', 'secret_key'),
    'SECRET_KEY_INFO' => $mcstatistics_language->get('mcstatistics', 'secret_key_info'),
    'SECRET_KEY_VALUE' => $configuration->get('mcstatistics', 'secret_key'),
    'SHOW_STATS_ON_PROFILE' => $mcstatistics_language->get('mcstatistics', 'show_stats_on_profile'),
    'SHOW_STATS_ON_PROFILE_VALUE' => $configuration->get('mcstatistics', 'display_profile'),
    'TOKEN' => Token::get(),
    'SUBMIT' => $language->get('general', 'submit')
));

$template->addCSSFiles(array(
	(defined('CONFIG_PATH') ? CONFIG_PATH : '') . '/core/assets/plugins/switchery/switchery.min.css' => array()
));

$template->addJSFiles(array(
	(defined('CONFIG_PATH') ? CONFIG_PATH : '') . '/core/assets/plugins/switchery/switchery.min.js' => array()
));

$template->addJSScript('
	var elems = Array.prototype.slice.call(document.querySelectorAll(\'.js-switch\'));

	elems.forEach(function(html) {
		var switchery = new Switchery(html, {color: \'#23923d\', secondaryColor: \'#e56464\'});
	});
');

$page_load = microtime(true) - $start;
define('PAGE_LOAD_TIME', str_replace('{x}', round($page_load, 3), $language->get('general', 'page_loaded_in')));

$template->onPageLoad();

require(ROOT_PATH . '/core/templates/panel_navbar.php');

// Display template
$template->displayTemplate('mcstatistics/settings.tpl', $smarty);
