<?php
/*
 *  Made by Partydragen
 *  https://github.com/partydragen/Nameless-MCStatistics
 *  https://partydragen.com/
 *
 *  MCStatistics player page
 */

const PAGE = 'players';
$page_title = $mcstatistics_language->get('general', 'player');
require_once(ROOT_PATH . '/core/templates/frontend_init.php');

$timeago = new TimeAgo(TIMEZONE);

// Get player name
$player = explode('/', $route);
if (count($player) < 3) {
    require_once(ROOT_PATH . '/404.php');
    die();
}
$player = $player[count($player) - 1];

if (!strlen($player)) {
    require_once(ROOT_PATH . '/404.php');
    die();
}

$player = new Player($player);
if (!$player->exists()) {
    require_once(ROOT_PATH . '/404.php');
    die();
}

$totalplaytime = $player->data()->play_time / 1000;
$hours = $totalplaytime / 3600;
$minutes = ($totalplaytime % 3600) / 60;

// Fields
$results = $mcstatistics->fetchPlayerFields($player->data()->username);
$servers = [];
foreach ($results->servers as $server) {
    $fields = [];

    foreach ($server->fields as $field) {
        $fields[] = [
            'title' => $field->name,
            'type' => 'text',
            'value' => $field->value
        ];
    }

    $servers[] = [
        'name' => $server->name,
        'fields' => $fields,
    ];
}

// About fields
$about_fields = [];
$about_fields[] = [
    'title' => $mcstatistics_language->get('general', 'registered'),
    'value' => $timeago->inWords($player->data()->registered / 1000, $language)
];

$about_fields[] = [
    'title' => $mcstatistics_language->get('general', 'last_seen'),
    'value' => $timeago->inWords($player->data()->last_seen / 1000, $language)
];

$about_fields[] = [
    'title' => $mcstatistics_language->get('general', 'play_time'),
    'value' => Output::getClean(sprintf("%d hours, %d min", $hours, $minutes))
];

$about_fields[] = [
    'title' => $mcstatistics_language->get('general', 'last_server'),
    'value' => Output::getClean($player->data()->last_server->name)
];

// Is player online?
$online = ($player->data()->last_seen / 1000) > strtotime('-5 minutes');

$player_user = new User($player->data()->username, 'username');

$smarty->assign([
    'USERNAME' => Output::getClean($player->data()->username),
    'AVATAR' => 'https://crafthead.net/body/' . $player->data()->uuid,
    'REGISTERED' => $mcstatistics_language->get('general', 'registered'),
    'REGISTERED_DATE' => $timeago->inWords($player->data()->registered / 1000, $language),
    'LAST_SEEN' => $mcstatistics_language->get('general', 'last_seen'),
    'LAST_SEEN_DATE' => $timeago->inWords($player->data()->last_seen / 1000, $language),
    'PLAY_TIME' => $mcstatistics_language->get('general', 'play_time'),
    'PLAY_TIME_VALUE' => Output::getClean(sprintf("%d hours, %d min", $hours, $minutes)),
    'LAST_SERVER' => $mcstatistics_language->get('general', 'last_server'),
    'LAST_SERVER_VALUE' => Output::getClean($player->data()->last_server->name),
    'IS_ONLINE' => $online,
    'OFFLINE' => $mcstatistics_language->get('general', 'offline'),
    'ONLINE_ON_SERVER' =>$mcstatistics_language->get('general', 'online_on_server', [
        'server' => Output::getClean($player->data()->last_server->name)
    ]),
    'SERVERS_FIELDS' => $servers,
    'ABOUT_FIELDS' => $about_fields,
    'USER_ID' => $player_user->exists() ? $player_user->data()->id : null,
    'USER_STYLE' => $player_user->exists() ? $player_user->getGroupStyle() : null,
    'USER_AVATAR' => $player_user->exists() ? $player_user->getAvatar() : null,
    'USER_PROFILE' => $player_user->exists() ? $player_user->getProfileURL() : null,
]);

// Load modules + template
Module::loadPage($user, $pages, $cache, $smarty, [$navigation, $cc_nav, $staffcp_nav], $widgets, $template);

$template->onPageLoad();

require(ROOT_PATH . '/core/templates/navbar.php');
require(ROOT_PATH . '/core/templates/footer.php');

// Display template
$template->displayTemplate('mcstatistics/player.tpl', $smarty);