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
    if (Settings::get('uuid_linking')) {
        $integration = $profile_user->getIntegration('Minecraft');
        if ($integration != null) {
            $results = $mcstatistics->fetchPlayerFields(ProfileUtils::formatUUID($integration->data()->identifier));
        } else {
            $results = $mcstatistics->fetchPlayerFields($query->username);
        }
    } else {
        $results = $mcstatistics->fetchPlayerFields($query->username);
    }

    $cache->setCache('mcstatistics');
    $cache->store('user_' . $query->id, $results, 120);
} else {
    $results = $cache->retrieve('user_' . $query->id);
}

if ($results != null) {
    if (isset($results->error)) {
        if ($results->code == 'player_not_found') {
            $statistics_error = $mcstatistics_language->get('general', 'player_not_found');
        } else if ($results->code == 'no_fields_configurated') {
            $statistics_error = $mcstatistics_language->get('general', 'no_fields_configurated');
        } else {
            $statistics_error = $mcstatistics_language->get('general', 'failed_to_fetch_player_data');
        }
    } else {
        // Statistics fields
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

        $smarty->assign('MCSTATISTICS_FIELDS', $fields);
        $smarty->assign('MCSTATISTICS_SERVERS', $servers);
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
