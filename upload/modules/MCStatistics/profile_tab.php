<?php
/*
 *	Made by Partydragen
 *  https://github.com/partydragen/Nameless-MCStatistics
 *  https://partydragen.com/
 *
 *  License: MIT
 *
 *  MCStatistics tab file on profile page
 */

$cache->setCache('mcstatistics');
if(!$cache->isCached('user_' . $query->id)){
    $integration = $profile_user->getIntegration('Minecraft');
    if ($integration != null) {
        $results = $mcstatistics->fetchPlayerData(ProfileUtils::formatUUID($integration->data()->identifier));
    } else {
        $results = $mcstatistics->fetchPlayerData($query->username);
    }

    $cache->setCache('mcstatistics');
    $cache->store('user_' . $query->id, $results, 120);
} else {
    $results = $cache->retrieve('user_' . $query->id);
}

if ($results != null) {
    if (isset($results->error) && $results->error == true) {
        if ($results->code == 20) {
            $statistics_error = $mcstatistics_language->get('general', 'player_not_found');
        } else {
            $statistics_error = $mcstatistics_language->get('general', 'failed_to_fetch_player_data');
        }
    } else {
        // Statistics fields
        $fields = [];

        $hours = $results->play_time / 1000 / 3600;
        $minutes = ($results->play_time / 1000 % 3600) / 60;

        $fields['first_join'] = [
            'title' => $mcstatistics_language->get('general', 'first_join'),
            'type' => 'text',
            'value' => $timeago->inWords($results->registered / 1000, $language),
            'tooltip' => date(DATE_FORMAT, $results->registered / 1000)
        ];
        $fields['last_seen'] = [
            'title' => $mcstatistics_language->get('general', 'last_seen'),
            'type' => 'text',
            'value' => $timeago->inWords($results->last_seen / 1000, $language),
            'tooltip' => date(DATE_FORMAT, $results->last_seen / 1000)
        ];
        $fields['play_time'] = [
            'title' => $mcstatistics_language->get("general", 'play_time'),
            'type' => 'text',
            'value' => Output::getClean(sprintf("%d hours, %d min", $hours, $minutes))
        ];
        $fields['kills'] = [
            'title' => $mcstatistics_language->get("general", 'kills'),
            'type' => 'text',
            'value' => $results->kills
        ];
        $fields['deaths'] = [
            'title' => $mcstatistics_language->get("general", 'deaths'),
            'type' => 'text',
            'value' => $results->deaths
        ];
        $fields['blocks_placed'] = [
            'title' => $mcstatistics_language->get("general", 'blocks_placed'),
            'type' => 'text',
            'value' => $results->blocks_placed
        ];
        $fields['blocks_destroyed'] = [
            'title' => $mcstatistics_language->get("general", 'blocks_destroyed'),
            'type' => 'text',
            'value' => $results->blocks_destroyed
        ];

        $smarty->assign('MCSTATISTICS_FIELDS', $fields);
    }
} else {
    $statistics_error = $mcstatistics_language->get('general', 'failed_to_fetch_player_data');
}

if (isset($statistics_error))
    $smarty->assign('MCSTATISTICS_ERROR', $statistics_error);

// Smarty
$smarty->assign([
    'INGAME_STATISTICS_TITLE' => $mcstatistics_language->get('general', 'ingame_statistics')
]);
