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

if (!isset($mcstatistics) || (isset($mcstatistics) && !$mcstatistics instanceof MCStatistics)) {
    require_once(ROOT_PATH . '/modules/MCStatistics/classes/MCStatistics.php');
    $mcstatistics = new MCStatistics($cache);
}

require_once(ROOT_PATH . '/core/integration/uuid.php');

$cache->setCache('mcstatistics');
if(!$cache->isCached('user_' . $query->id)){
    if(!empty($query->uuid)) {
        $results = $mcstatistics->fetchPlayerData(ProfileUtils::formatUUID($query->uuid));
    } else {
        $results = $mcstatistics->fetchPlayerData(ProfileUtils::formatUUID($query->username));
    }
    $cache->setCache('mcstatistics');
    $cache->store('user_' . $query->id, $results, 120);
} else {
    $results = $cache->retrieve('user_' . $query->id);
}

if($results != null) {
    if(isset($results->error) && $results->error == true) {
        if($results->code == 20) {
            $statistics_error = $mcstatistics_language->get('mcstatistics', 'player_not_found');
        } else {
            $statistics_error = $mcstatistics_language->get('mcstatistics', 'failed_to_fetch_player_data');
        }
    } else {
        // Statistics fields
        $fields = array();
        
        $hours = $results->play_time / 1000 / 3600;
        $minutes = ($results->play_time / 1000 % 3600) / 60;

        $fields['first_join'] = array(
            'title' => $mcstatistics_language->get('mcstatistics', 'first_join'),
            'type' => 'text',
            'value' => $timeago->inWords(date('d M Y, H:i', $results->registered / 1000), $language->getTimeLanguage()),
            'tooltip' => date('d M Y, H:i', $results->registered / 1000)
        );
        $fields['last_seen'] = array(
            'title' => $mcstatistics_language->get('mcstatistics', 'last_seen'),
            'type' => 'text',
            'value' => $timeago->inWords(date('d M Y, H:i', $results->last_seen / 1000), $language->getTimeLanguage()),
            'tooltip' => date('d M Y, H:i', $results->last_seen / 1000)
        );
        $fields['play_time'] = array(
            'title' => $mcstatistics_language->get("mcstatistics", 'play_time'),
            'type' => 'text',
            'value' => Output::getClean(sprintf("%d hours, %d min", $hours, $minutes))
        );
        $fields['kills'] = array(
            'title' => $mcstatistics_language->get("mcstatistics", 'kills'),
            'type' => 'text',
            'value' => $results->kills
        );
        $fields['deaths'] = array(
            'title' => $mcstatistics_language->get("mcstatistics", 'deaths'),
            'type' => 'text',
            'value' => $results->deaths
        );
        $fields['blocks_placed'] = array(
            'title' => $mcstatistics_language->get("mcstatistics", 'blocks_placed'),
            'type' => 'text',
            'value' => $results->blocks_placed
        );
        $fields['blocks_destroyed'] = array(
            'title' => $mcstatistics_language->get("mcstatistics", 'blocks_destroyed'),
            'type' => 'text',
            'value' => $results->blocks_destroyed
        );

        $smarty->assign('MCSTATISTICS_FIELDS', $fields);
    }
} else {
    $statistics_error = $mcstatistics_language->get('mcstatistics', 'failed_to_fetch_player_data');
}

if(isset($statistics_error))
    $smarty->assign('MCSTATISTICS_ERROR', $statistics_error);

// Smarty
$smarty->assign(array(
    'INGAME_STATISTICS_TITLE' => $mcstatistics_language->get('mcstatistics', 'ingame_statistics')
));