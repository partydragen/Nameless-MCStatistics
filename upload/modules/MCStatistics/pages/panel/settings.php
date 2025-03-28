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
				'min' => 32,
				'max' => 64
			]
        ]);

        if ($validation->passed()) {
            // Get link location
            if (isset($_POST['link_location'])) {
                switch($_POST['link_location']) {
                    case 1:
                    case 2:
                    case 3:
                    case 4:
                        $location = $_POST['link_location'];
                        break;
                    default:
                        $location = 1;
                }
            } else {
                $location = 1;
            }

            // Update Link location cache
            $cache->setCache('nav_location');
            $cache->store('players_location', $location);

            // Get link location
            if (isset($_POST['leaderboard_link_location'])) {
                switch($_POST['leaderboard_link_location']) {
                    case 1:
                    case 2:
                    case 3:
                    case 4:
                        $location = $_POST['leaderboard_link_location'];
                        break;
                    default:
                        $location = 1;
                }
            } else {
                $location = 1;
            }

            // Update leaderboard Link location cache
            $cache->setCache('nav_location');
            $cache->store('leaderboard_location', $location);

            // Update secret key
            Settings::set('secret_key', Input::get('secret_key'), 'MCStatistics');

            // Update show stats on profile page value
            if(isset($_POST['display_profile']) && $_POST['display_profile'] == 'on') $display_profile = 1;
            else $display_profile = 0;

            Settings::set('display_profile', $display_profile, 'MCStatistics');

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

// Retrieve link_location from cache
$cache->setCache('nav_location');
$link_location = $cache->retrieve('players_location');
$leaderboard_link_location = $cache->retrieve('leaderboard_location');
// Load modules + template
Module::loadPage($user, $pages, $cache, $smarty, [$navigation, $cc_nav, $staffcp_nav], $widgets, $template);

if (Session::exists('mcstatistics_success'))
    $success = Session::flash('mcstatistics_success');

if (isset($success)){
    $template->getEngine()->addVariables([
        'SUCCESS_TITLE' => $language->get('general', 'success'),
        'SUCCESS' => $success
    ]);
}

if (isset($errors) && count($errors)){
    $template->getEngine()->addVariables([
        'ERRORS_TITLE' => $language->get('general', 'error'),
        'ERRORS' => $errors
    ]);
}

$template->getEngine()->addVariables([
    'PARENT_PAGE' => PARENT_PAGE,
    'DASHBOARD' => $language->get('admin', 'dashboard'),
    'PAGE' => PANEL_PAGE,
    'MCSTATISTICS' => $mcstatistics_language->get('general', 'mcstatistics'),
    'SETTINGS' => $mcstatistics_language->get('general', 'settings'),
    'INFO' => $language->get('general', 'info'),
    'SECRET_KEY' => $mcstatistics_language->get('general', 'secret_key'),
    'SECRET_KEY_INFO' => $mcstatistics_language->get('general', 'secret_key_info'),
    'SECRET_KEY_VALUE' => Settings::get('secret_key', '', 'MCStatistics'),
    'LINK_LOCATION' => $mcstatistics_language->get('general', 'link_location'),
    'LINK_LOCATION_VALUE' => $link_location,
    'LINK_NAVBAR' => $language->get('admin', 'page_link_navbar'),
    'LINK_MORE' => $language->get('admin', 'page_link_more'),
    'LINK_FOOTER' => $language->get('admin', 'page_link_footer'),
    'LINK_NONE' => $language->get('admin', 'page_link_none'),
    'SHOW_STATS_ON_PROFILE' => $mcstatistics_language->get('general', 'show_stats_on_profile'),
    'SHOW_STATS_ON_PROFILE_VALUE' => Settings::get('display_profile', '1', 'MCStatistics'),
    'LEADERBOARD_LINK_LOCATION' => $mcstatistics_language->get('general', 'leaderboard_link_location'),
    'LEADERBOARD_LINK_LOCATION_VALUE' => $leaderboard_link_location,
    'TOKEN' => Token::get(),
    'SUBMIT' => $language->get('general', 'submit')
]);

$template->onPageLoad();

require(ROOT_PATH . '/core/templates/panel_navbar.php');

// Display template
$template->displayTemplate('mcstatistics/settings');