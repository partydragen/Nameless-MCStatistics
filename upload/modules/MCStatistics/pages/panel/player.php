<?php
/*
 *  Made by Partydragen
 *  https://github.com/partydragen/Nameless-MCStatistics
 *  https://partydragen.com/
 *
 *  License: MIT
 *
 *  MCStatistics player page
 */

// Can the user view the panel?
if(!$user->handlePanelPageLoad('mcstatistics.players')) {
    require_once(ROOT_PATH . '/403.php');
    die();
}

define('PAGE', 'panel');
define('PARENT_PAGE', 'mcstatistics');
define('PANEL_PAGE', 'mcstatistics_players');
$page_title = $language->get('admin', 'general_settings');
require_once(ROOT_PATH . '/core/templates/backend_init.php');
$timeago = new Timeago(TIMEZONE);

$player = explode('/', $route);
$player = $player[count($player) - 1];

if (!strlen($player)) {
    Redirect::to(URL::build('/panel/mcstatistics/players'));
    die();
}

$player = explode('-', $player);
$player = $player[0];

// Load modules + template
Module::loadPage($user, $pages, $cache, $smarty, array($navigation, $cc_nav, $mod_nav), $widgets);

if (!isset($mcstatistics) || (isset($mcstatistics) && !$mcstatistics instanceof MCStatistics)) {
    require_once(ROOT_PATH . '/modules/MCStatistics/classes/MCStatistics.php');
    $mcstatistics = new MCStatistics($cache);
}

$results = $mcstatistics->fetchPlayerData($player);
if($results != null) {
    if(isset($results->error) && $results->error == true) {
        if($results->code == 20) {
            Session::flash('mcstatistics_error', $mcstatistics_language->get('mcstatistics', 'player_not_found'));
        } else {
            Session::flash('mcstatistics_error', $mcstatistics_language->get('mcstatistics', 'failed_to_fetch_player_data'));
        }
        Redirect::to(URL::build('/panel/mcstatistics/players'));
        die();
    }
} else {
    Session::flash('mcstatistics_error', $mcstatistics_language->get('mcstatistics', 'failed_to_fetch_player_data'));
    Redirect::to(URL::build('/panel/mcstatistics/players'));
    die();
}


if(Session::exists('mcstatistics_success'))
    $success = Session::flash('mcstatistics_success');

if(Session::exists('mcstatistics_error'))
    $errors = array(Session::flash('mcstatistics_error'));

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

$hours = $results->play_time / 1000 / 3600;
$minutes = ($results->play_time / 1000 % 3600) / 60;

$smarty->assign(array(
    'PARENT_PAGE' => PARENT_PAGE,
    'DASHBOARD' => $language->get('admin', 'dashboard'),
    'PAGE' => PANEL_PAGE,
    'MCSTATISTICS' => $mcstatistics_language->get('mcstatistics', 'mcstatistics'),
    'TOKEN' => Token::get(),
    'SUBMIT' => $language->get('general', 'submit'),
    'AVATAR' => 'https://cravatar.eu/helmavatar/'.$results->uuid.'/128.png',
    'DETAILS' => $language->get('admin', 'details'),
    'USERNAME' => $language->get('user', 'username'),
    'USERNAME_VALUE' => Output::getClean($results->username),
    'UUID' => $language->get('admin', 'uuid'),
    'UUID_VALUE' => Output::getClean($results->uuid),
    'REGISTERED' => $language->get('user', 'registered'),
    'REGISTERED_VALUE' => date('d M Y, H:i', $results->registered / 1000),
    'LAST_SEEN' => $language->get('user', 'last_seen'),
    'LAST_SEEN_SHORT_VALUE' => $timeago->inWords(date('d M Y, H:i', $results->last_seen / 1000), $language->getTimeLanguage()),
    'LAST_SEEN_FULL_VALUE' => date('d M Y, H:i', $results->results / 1000),
    'LAST_IP' => $mcstatistics_language->get('mcstatistics', 'last_ip'),
    'LAST_IP_VALUE' => Output::getClean($results->last_ip),
    'PLAY_TIME' => $mcstatistics_language->get('mcstatistics', 'play_time'),
    'PLAY_TIME_VALUE' => Output::getClean(sprintf("%d hours, %d min", $hours, $minutes)),
    'KILLS' => $mcstatistics_language->get("mcstatistics", 'kills'),
    'KILLS_VALUE' => Output::getClean($results->kills),
    'DEATHS' => $mcstatistics_language->get("mcstatistics", 'deaths'),
    'DEATHS_VALUE' => Output::getClean($results->deaths),
    'BLOCKS_PLACED' => $mcstatistics_language->get("mcstatistics", 'blocks_placed'),
    'BLOCKS_PLACED_VALUE' => Output::getClean($results->blocks_placed),
    'BLOCKS_DESTROYED' => $mcstatistics_language->get("mcstatistics", 'blocks_destroyed'),
    'BLOCKS_DESTROYED_VALUE' => Output::getClean($results->blocks_destroyed),
));

$page_load = microtime(true) - $start;
define('PAGE_LOAD_TIME', str_replace('{x}', round($page_load, 3), $language->get('general', 'page_loaded_in')));

$template->onPageLoad();

require(ROOT_PATH . '/core/templates/panel_navbar.php');

// Display template
$template->displayTemplate('mcstatistics/player.tpl', $smarty);
