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
$timeago = new TimeAgo(TIMEZONE);

$player = explode('/', $route);
$player = $player[count($player) - 1];

if (!strlen($player)) {
    Redirect::to(URL::build('/panel/mcstatistics/players'));
}

// Load modules + template
Module::loadPage($user, $pages, $cache, $smarty, [$navigation, $cc_nav, $staffcp_nav], $widgets, $template);

$results = $mcstatistics->fetchPlayerData($player);
if ($results != null) {
    if (isset($results->error)) {
        if ($results->code == 'player_not_found') {
            Session::flash('mcstatistics_error', $mcstatistics_language->get('general', 'player_not_found'));
        } else {
            Session::flash('mcstatistics_error', $mcstatistics_language->get('general', 'failed_to_fetch_player_data'));
        }
        Redirect::to(URL::build('/panel/mcstatistics/players'));
    }
} else {
    Session::flash('mcstatistics_error', $mcstatistics_language->get('general', 'failed_to_fetch_player_data'));
    Redirect::to(URL::build('/panel/mcstatistics/players'));
}

if (!isset($_GET['view'])) {
    $template_file = 'mcstatistics/player.tpl';
} else {
    switch($_GET['view']) {
        case 'sessions':
            $sessions_results = $mcstatistics->fetchPlayerSessions($results->_id);
            
            $sessions_list = array();
            foreach ($sessions_results->sessions as $session) {
                $playtime = $session->play_time / 1000;
                    
                $hours = $playtime / 3600;
                $minutes = ($playtime % 3600) / 60;
                
                $sessions_list[] = array(
                    'session_start' => date('d M Y, H:i', $session->session_start / 1000),
                    'session_end' => date('d M Y, H:i', $session->session_end / 1000),
                    'play_time' => sprintf("%d hours, %d min", $hours, $minutes),
                    'ip' => Output::getClean($session->ip),
                    'version' => Output::getClean($session->version),
                );
            }
            
            $smarty->assign(array(
                'SESSIONS_LIST' => $sessions_list,
                'SESSION_START' => $mcstatistics_language->get('general', 'session_start'),
                'SESSION_END' => $mcstatistics_language->get('general', 'session_end'),
                'PLAY_TIME' => $mcstatistics_language->get('general', 'play_time'),
                'VERSION' => $mcstatistics_language->get('general', 'version'),
                'IP_ADDRESS' => $mcstatistics_language->get('general', 'ip_address'),
            ));
        
            $template_file = 'mcstatistics/player_sessions.tpl';
        break;
        case 'ip_history':
            $ips_results = $mcstatistics->fetchPlayerIPHistory($results->_id);

            $ip_history_list = array();
            foreach($ips_results->ips as $ip) {
                $ip_history_list[] = array(
                    'ip' => Output::getClean($ip->ip),
                    'sessions' => Output::getClean($ip->sessions)
                );
            }
            
            $smarty->assign(array(
                'IP_HISTORY_LIST' => $ip_history_list,
                'IP_ADDRESS' => $mcstatistics_language->get('general', 'ip_address'),
            ));
        
            $template_file = 'mcstatistics/player_ip_history.tpl';
        break;
    }
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
    'MCSTATISTICS' => $mcstatistics_language->get('general', 'mcstatistics'),
    'TOKEN' => Token::get(),
    'SUBMIT' => $language->get('general', 'submit'),
    'AVATAR' => 'https://crafthead.net/avatar/'.$results->uuid.'/128',
    'DETAILS' => $language->get('admin', 'details'),
    'DETAILS_LINK' => URL::build('/panel/mcstatistics/player/' . Output::getClean($results->username)),
    'SESSIONS' => $mcstatistics_language->get('general', 'sessions'),
    'SESSIONS_LINK' => URL::build('/panel/mcstatistics/player/' . Output::getClean($results->username), 'view=sessions'),
    'IP_HISTORY' => $mcstatistics_language->get('general', 'ip_history'),
    'IP_HISTORY_LINK' => URL::build('/panel/mcstatistics/player/' . Output::getClean($results->username), 'view=ip_history'),
    'PLAYER_ID' => Output::getClean($results->_id),
    'USERNAME' => $language->get('user', 'username'),
    'USERNAME_VALUE' => Output::getClean($results->username),
    'UUID' => $language->get('admin', 'uuid'),
    'UUID_VALUE' => Output::getClean($results->uuid),
    'REGISTERED' => $language->get('user', 'registered'),
    'REGISTERED_VALUE' => date('d M Y, H:i', $results->registered / 1000),
    'LAST_SEEN' => $language->get('user', 'last_seen'),
    'LAST_SEEN_SHORT_VALUE' => $timeago->inWords($results->last_seen / 1000, $language),
    'LAST_SEEN_FULL_VALUE' => date('d M Y, H:i', $results->results / 1000),
    'LAST_IP' => $mcstatistics_language->get('general', 'last_ip'),
    'LAST_IP_VALUE' => Output::getClean($results->last_ip),
    'LAST_VERSION' => $mcstatistics_language->get('general', 'last_version'),
    'LAST_VERSION_VALUE' => Output::getClean($results->last_version),
    'PLAY_TIME' => $mcstatistics_language->get('general', 'play_time'),
    'PLAY_TIME_VALUE' => Output::getClean(sprintf("%d hours, %d min", $hours, $minutes)),
    'KILLS' => $mcstatistics_language->get("general", 'kills'),
    'KILLS_VALUE' => Output::getClean($results->kills),
    'DEATHS' => $mcstatistics_language->get("general", 'deaths'),
    'DEATHS_VALUE' => Output::getClean($results->deaths),
    'BLOCKS_PLACED' => $mcstatistics_language->get("general", 'blocks_placed'),
    'BLOCKS_PLACED_VALUE' => Output::getClean($results->blocks_placed),
    'BLOCKS_DESTROYED' => $mcstatistics_language->get("general", 'blocks_destroyed'),
    'BLOCKS_DESTROYED_VALUE' => Output::getClean($results->blocks_destroyed),
    'NO_DATA_AVAILABLE' => $mcstatistics_language->get('general', 'no_data_available'),
));

$template->onPageLoad();

require(ROOT_PATH . '/core/templates/panel_navbar.php');

// Display template
$template->displayTemplate($template_file, $smarty);
