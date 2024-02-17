<?php

header('Content-type: application/json;charset=utf-8');

$list = $_GET['list'];
$overview = isset($_GET['overview']) && $_GET['overview'] === 'true';
if (!$overview) {
    $page = $_GET['page'] ?? 1;
} else {
    $page = 1;
}

$i = 0;
$mcstatistics = new MCStatistics($cache);

$leaderboards_list = [];
$json = $mcstatistics->getLeaderboardPlayers($_GET['server'], $_GET['leaderboard']);
if (isset($json->players)) {
    foreach ($json->players as $player) {
        $leaderboards_list[] = [
            'username' => Output::getClean($player->username),
            'avatar_url' => AvatarSource::getAvatarFromUUID($player->uuid),
            'group_style' => null,
            'profile_url' => URL::build('/player/' . $player->username),
            'count' => Output::getClean($player->score),
            'group_html' => [],
            'metadata' => []
        ];

        $i++;

        if ($overview && $i == 5) {
            break;
        }
    }
}

$leaderboards_list = json_encode($leaderboards_list);
die($leaderboards_list);