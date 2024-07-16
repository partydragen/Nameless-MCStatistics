<?php
/*
 *  Made by Partydragen
 *  https://github.com/partydragen/Nameless-MCStatistics
 *  https://partydragen.com/
 *
 *  MCStatistics Servers page
 */

const PAGE = 'servers';
$page_title = $mcstatistics_language->get('general', 'servers');
require_once(ROOT_PATH . '/core/templates/frontend_init.php');

$mcstatistics = new MCStatistics($cache);
if ($mcstatistics->isSetup()) {
    $json = $mcstatistics->getServers();

    if (isset($json->servers)) {
        $servers_list = [];
        foreach ($json->servers as $server) {
            $servers_list[] = [
                'name' => Output::getClean($server->name),
                'online' => $server->online,
                'players_online' => Output::getClean($server->players_online),
                'x_players_online' => $mcstatistics_language->get('general', 'x_players_online', [
                    'players' => $server->players_online
                ])
            ];
        }

        $smarty->assign([
            'SERVERS_LIST' => $servers_list,
        ]);

    } else {
        $errors[] = $mcstatistics_language->get('general', 'failed_to_fetch_player_data');
    }
} else {
    $errors[] = $mcstatistics_language->get('general', 'not_setup');
}

$smarty->assign([
    'SERVERS' => $mcstatistics_language->get('general', 'servers'),
    'TOKEN' => Token::get(),
    'NO_DATA_AVAILABLE' => $mcstatistics_language->get('general', 'no_data_available'),
    'SERVER_OFFLINE' => $mcstatistics_language->get('general', 'server_offline'),
]);

// Load modules + template
Module::loadPage($user, $pages, $cache, $smarty, [$navigation, $cc_nav, $staffcp_nav], $widgets, $template);

if (isset($success))
    $smarty->assign([
        'SUCCESS' => $success,
        'SUCCESS_TITLE' => $language->get('general', 'success')
    ]);

if (isset($errors) && count($errors))
    $smarty->assign([
        'ERRORS' => $errors,
        'ERRORS_TITLE' => $language->get('general', 'error')
    ]);

$template->onPageLoad();

$smarty->assign('WIDGETS_LEFT', $widgets->getWidgets('left'));
$smarty->assign('WIDGETS_RIGHT', $widgets->getWidgets('right'));

require(ROOT_PATH . '/core/templates/navbar.php');
require(ROOT_PATH . '/core/templates/footer.php');

// Display template
$template->displayTemplate('mcstatistics/servers.tpl', $smarty);