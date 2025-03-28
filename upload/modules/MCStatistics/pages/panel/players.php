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
    } else {
        $errors = array($language->get('general', 'invalid_token'));
    }
}

// Load modules + template
Module::loadPage($user, $pages, $cache, $smarty, [$navigation, $cc_nav, $staffcp_nav], $widgets, $template);

if(Session::exists('mcstatistics_success'))
    $success = Session::flash('mcstatistics_success');

if(Session::exists('mcstatistics_error'))
    $errors = array(Session::flash('mcstatistics_error'));

if(isset($success)){
    $template->getEngine()->addVariables([
        'SUCCESS_TITLE' => $language->get('general', 'success'),
        'SUCCESS' => $success
    ]);
}

if(isset($errors) && count($errors)){
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
    'TOKEN' => Token::get(),
    'SUBMIT' => $language->get('general', 'submit'),
    'SEARCH_FOR_PLAYER' => $mcstatistics_language->get('general', 'search_for_player'),
    'PLAYERS' => $mcstatistics_language->get('general', 'players')
]);

$template->onPageLoad();

require(ROOT_PATH . '/core/templates/panel_navbar.php');

// Display template
$template->displayTemplate('mcstatistics/players');
