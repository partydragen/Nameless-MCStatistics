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
    $errors = [];

    if (Token::check(Input::get('token'))) {
        $validation = Validate::check($_POST, [
            'secret_key' => [
				'min' => 40,
				'max' => 64
			]
        ]);

        if ($validation->passed()){
            // Update secret key
            Util::setSetting('secret_key', Input::get('secret_key'), 'MCStatistics');

            // Update show stats on profile page value
            if(isset($_POST['display_profile']) && $_POST['display_profile'] == 'on') $display_profile = 1;
            else $display_profile = 0;

            Util::setSetting('display_profile', $display_profile, 'MCStatistics');

            Session::flash('mcstatistics_success', $mcstatistics_language->get('general', 'settings_updated_successfully'));
            Redirect::to(URL::build('/panel/mcstatistics/settings'));
        } else {
			$errors[] = $mcstatistics_language->get('general', 'invalid_secret_key');
        }
    } else {
        // Invalid token
        $errors[] = $language->get('general', 'invalid_token');
    }
}

// Load modules + template
Module::loadPage($user, $pages, $cache, $smarty, [$navigation, $cc_nav, $staffcp_nav], $widgets, $template);

if (Session::exists('mcstatistics_success'))
    $success = Session::flash('mcstatistics_success');

if (isset($success)){
    $smarty->assign([
        'SUCCESS_TITLE' => $language->get('general', 'success'),
        'SUCCESS' => $success
    ]);
}

if (isset($errors) && count($errors)){
    $smarty->assign([
        'ERRORS_TITLE' => $language->get('general', 'error'),
        'ERRORS' => $errors
    ]);
}

$smarty->assign([
    'PARENT_PAGE' => PARENT_PAGE,
    'DASHBOARD' => $language->get('admin', 'dashboard'),
    'PAGE' => PANEL_PAGE,
    'MCSTATISTICS' => $mcstatistics_language->get('general', 'mcstatistics'),
    'SETTINGS' => $mcstatistics_language->get('general', 'settings'),
    'INFO' => $language->get('general', 'info'),
    'SECRET_KEY' => $mcstatistics_language->get('general', 'secret_key'),
    'SECRET_KEY_INFO' => $mcstatistics_language->get('general', 'secret_key_info'),
    'SECRET_KEY_VALUE' => Util::getSetting('secret_key', '', 'MCStatistics'),
    'SHOW_STATS_ON_PROFILE' => $mcstatistics_language->get('general', 'show_stats_on_profile'),
    'SHOW_STATS_ON_PROFILE_VALUE' => Util::getSetting('display_profile', '1', 'MCStatistics'),
    'TOKEN' => Token::get(),
    'SUBMIT' => $language->get('general', 'submit')
]);

$template->onPageLoad();

require(ROOT_PATH . '/core/templates/panel_navbar.php');

// Display template
$template->displayTemplate('mcstatistics/settings.tpl', $smarty);