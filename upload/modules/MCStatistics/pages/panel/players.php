<?php
/*
 *  Made by Partydragen
 *  https://github.com/partydragen/Nameless-MCStatistics
 *  https://partydragen.com/
 *
 *  License: MIT
 *
 *  MCStatistics players page
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

if (Input::exists()) {
    // Check token
    if (Token::check()) {
        Redirect::to(URL::build('/panel/mcstatistics/player/' . Output::getClean(Input::get('search'))));
        die();
    } else {
        $errors = array($language->get('general', 'invalid_token'));
    }
}
// Load modules + template
Module::loadPage($user, $pages, $cache, $smarty, array($navigation, $cc_nav, $mod_nav), $widgets);

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

$smarty->assign(array(
    'PARENT_PAGE' => PARENT_PAGE,
    'DASHBOARD' => $language->get('admin', 'dashboard'),
    'PAGE' => PANEL_PAGE,
    'MCSTATISTICS' => $mcstatistics_language->get('mcstatistics', 'mcstatistics'),
    'TOKEN' => Token::get(),
    'SUBMIT' => $language->get('general', 'submit'),
    'SEARCH_FOR_PLAYER' => $mcstatistics_language->get('mcstatistics', 'search_for_player'),
    'PLAYERS' => $mcstatistics_language->get('mcstatistics', 'players'),
));

$page_load = microtime(true) - $start;
define('PAGE_LOAD_TIME', str_replace('{x}', round($page_load, 3), $language->get('general', 'page_loaded_in')));

$template->onPageLoad();

require(ROOT_PATH . '/core/templates/panel_navbar.php');

// Display template
$template->displayTemplate('mcstatistics/players.tpl', $smarty);
