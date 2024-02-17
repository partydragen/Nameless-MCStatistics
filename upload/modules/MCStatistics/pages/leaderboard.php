<?php
/*
 *  Made by Partydragen
 *  https://github.com/partydragen/Nameless-MCStatistics
 *  https://partydragen.com/
 *
 *  MCStatistics leaderboard page
 */

const PAGE = 'leaderboard';
$page_title = $mcstatistics_language->get('general', 'leaderboard');
require_once(ROOT_PATH . '/core/templates/frontend_init.php');
$mcstatistics = new MCStatistics($cache);
$directories = explode('/', rtrim($_GET['route'], '/'));

// List all leaderboards
if ($mcstatistics->isSetup()) {
    $json = $mcstatistics->getAllLeaderboards();

    if (isset($json->servers)) {
        $leaderboards_list = [];

        $leaderboards_list[] = [
            'server_name' => 'Overview',
            'placeholders' => [[
                'name' => 'Overview',
                'link' => Url::build('/leaderboard')
            ]]
        ];

        foreach ($json->servers as $server) {
            $placeholders = [];
            foreach ($server->placeholders as $placeholder) {
                $placeholders[] = [
                    'name' => Output::getClean($placeholder->friendly_name),
                    'link' => Url::build('/leaderboard/' . urlencode($server->name) . '/' . urlencode($placeholder->friendly_name))
                ];
            }

            $leaderboards_list[] = [
                'server_name' => Output::getClean($server->name),
                'link' => Url::build('/leaderboard/' . urlencode($server->name)),
                'placeholders' => $placeholders
            ];
        }

        $smarty->assign('LEADERBOARD_LIST', $leaderboards_list);
    }
} else {
    $errors[] = $mcstatistics_language->get('general', 'not_setup');
}

$count = count($directories);
if ($count >= 4 && $directories[$count - 3] == 'leaderboard') {
    // View leaderboard
    $server = $directories[$count - 2];
    $leaderboard = $directories[$count - 1];

    $leaderboards = $mcstatistics->getLeaderboards($server, $leaderboard);
    if (!count($leaderboards)) {
        Redirect::to(URL::build('/leaderboard'));
    }

    $smarty->assign('VIEWING_LEADERBOARDS', $leaderboards);
    $viewing_list = $server . '_' . $leaderboard;
} else if ($count >= 3 && $directories[$count - 2] == 'leaderboard') {
    // View server leaderboards
    $server = $directories[$count - 1];

    $leaderboards = $mcstatistics->getLeaderboards($server);
    if (!count($leaderboards)) {
        Redirect::to(URL::build('/leaderboard'));
    }

    $smarty->assign('VIEWING_LEADERBOARDS', $leaderboards);
    $viewing_list = 'overview';
} else {
    // Viewing leaderboards
    $smarty->assign('VIEWING_LEADERBOARDS', $mcstatistics->getLeaderboards());
    $viewing_list = 'overview';
}

$smarty->assign([
    'LEADERBOARD' => $mcstatistics_language->get('general', 'leaderboard'),
    'TOKEN' => Token::get(),
    'QUERIES_URL' => URL::build('/queries/leaderboard_list', 'server={{server}}&leaderboard={{leaderboard}}&page={{page}}&overview=' . ($viewing_list === 'overview' ? 'true' : 'false')),
    'VIEWING_LIST' => $viewing_list,
    'VIEW_ALL' => $mcstatistics_language->get('general', 'view_all'),
    'NO_DATA_AVAILABLE' => $mcstatistics_language->get('general', 'no_data_available'),
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
$template->displayTemplate('mcstatistics/leaderboard.tpl', $smarty);