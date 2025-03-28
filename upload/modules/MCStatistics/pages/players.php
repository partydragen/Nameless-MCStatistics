<?php
/*
 *  Made by Partydragen
 *  https://github.com/partydragen/Nameless-MCStatistics
 *  https://partydragen.com/
 *
 *  MCStatistics players page
 */

const PAGE = 'players';
$page_title = $mcstatistics_language->get('general', 'players');
require_once(ROOT_PATH . '/core/templates/frontend_init.php');

// Get page
if (isset($_GET['p'])) {
    if (!is_numeric($_GET['p'])) {
        Redirect::to(URL::build('/players'));
    }

    if ($_GET['p'] == 1) {
        // Avoid bug in pagination class
        Redirect::to(URL::build('/players'));
    }
    $p = $_GET['p'];
} else {
    $p = 1;
}

$mcstatistics = new MCStatistics($cache);
if ($mcstatistics->isSetup()) {
    $json = $mcstatistics->getPlayers();

    if (isset($json->players, $json->players_count)) {
        // Pagination
        $paginator = new Paginator(
            $template_pagination ?? null,
            $template_pagination_left ?? null,
            $template_pagination_right ?? null
        );
        $results = $paginator->getLimited($json->players, 15, $p, $json->players_count);
        $pagination = $paginator->generate(7, URL::build('/players/'));

        $template->getEngine()->addVariable('PAGINATION', $pagination);

        $players_list = [];
        foreach ($results->data as $player) {
            $player_user = new User($player->username, 'username');

            $players_list[] = [
                'username' => Output::getClean($player->username),
                'user_id' => $player_user->exists() ? $player_user->data()->id : null,
                'user_style' => $player_user->exists() ? $player_user->getGroupStyle() : null,
                'registered' => date(DATE_FORMAT, $player->firstjoin_date / 1000),
                'last_seen' => date(DATE_FORMAT, $player->lastjoin_date / 1000),
                'avatar' => AvatarSource::getAvatarFromUUID($player->uuid),
                'link' => URL::build('/player/' . $player->username)
            ];
        }

        $template->getEngine()->addVariables([
            'PLAYERS_LIST' => $players_list,
            'PLAYER' => $mcstatistics_language->get('general', 'player'),
            'REGISTERED' => $mcstatistics_language->get('general', 'registered'),
            'LAST_SEEN' => $mcstatistics_language->get('general', 'last_seen'),
            'VIEW' => $language->get('general', 'view'),
        ]);
    } else {
        $errors[] = $mcstatistics_language->get('general', 'failed_to_fetch_player_data');
    }
} else {
    $errors[] = $mcstatistics_language->get('general', 'not_setup');
}

$template->getEngine()->addVariables([
    'PLAYERS' => $mcstatistics_language->get('general', 'players'),
    'TOKEN' => Token::get(),
    'SEARCH_URL' => URL::build('/players'),
    'SEARCH' => $language->get('general', 'search'),
    'NO_DATA_AVAILABLE' => $mcstatistics_language->get('general', 'no_data_available'),
]);

// Load modules + template
Module::loadPage($user, $pages, $cache, $smarty, [$navigation, $cc_nav, $staffcp_nav], $widgets, $template);

if (isset($success))
    $template->getEngine()->addVariables([
        'SUCCESS' => $success,
        'SUCCESS_TITLE' => $language->get('general', 'success')
    ]);

if (isset($errors) && count($errors))
    $template->getEngine()->addVariables([
        'ERRORS' => $errors,
        'ERRORS_TITLE' => $language->get('general', 'error')
    ]);

$template->onPageLoad();

$template->getEngine()->addVariable('WIDGETS_LEFT', $widgets->getWidgets('left'));
$template->getEngine()->addVariable('WIDGETS_RIGHT', $widgets->getWidgets('right'));

require(ROOT_PATH . '/core/templates/navbar.php');
require(ROOT_PATH . '/core/templates/footer.php');

// Display template
$template->displayTemplate('mcstatistics/players');