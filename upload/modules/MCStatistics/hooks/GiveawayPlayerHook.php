<?php
class GiveawayPlayerHook extends HookBase {

    public static function minPlayerAge(UserPreEntryGiveawayEvent $event): void {
        $player_age = json_decode($event->giveaway->data()->min_player_age, true) ?? [];
        if (isset($player_age['interval']) && $player_age['interval'] > 0) {
            $integration_user = $event->user->getIntegration('Minecraft');
            if ($integration_user != null) {
                $player = new Player(ProfileUtils::formatUUID($integration_user->data()->identifier));

                if ($player->exists()) {
                    $min_age = strtotime('-' . $player_age['interval'] . ' ' . $player_age['period']);

                    if (($player->data()->registered / 1000) > $min_age) {
                        $event->setCancelled(true, MCStatistics::getLanguage()->get('general', 'age_requirement_not_meet'));
                    }
                } else {
                    $event->setCancelled(true, MCStatistics::getLanguage()->get('general', 'player_age_not_found'));
                }
            }
        }
    }

    public static function minPlayerPlaytime(UserPreEntryGiveawayEvent $event): void {
        $player_playtime = json_decode($event->giveaway->data()->min_player_playtime, true) ?? [];
        if (isset($player_playtime['playtime']) && $player_playtime['playtime'] > 0) {
            $integration_user = $event->user->getIntegration('Minecraft');
            if ($integration_user != null) {
                $player = new Player(ProfileUtils::formatUUID($integration_user->data()->identifier));
                if ($player->exists()) {
                    $min_playtime = 3600 * $player_playtime['playtime'];

                    if (($player->data()->play_time / 1000) < $min_playtime) {
                        $event->setCancelled(true, MCStatistics::getLanguage()->get('general', 'playtime_requirement_not_meet'));
                    }
                } else {
                    $event->setCancelled(true, MCStatistics::getLanguage()->get('general', 'player_playtime_not_found'));
                }
            }
        }
    }

}