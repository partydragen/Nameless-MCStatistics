<?php
class CheckoutPlayerHook extends HookBase {

    public static function minPlayerAge(array $params = []): array {
        $user = $params['user'];
        $product = $params['product'];
        $recipient = $params['recipient'];

        $player_age = json_decode($product->data()->min_player_age, true) ?? [];
        if (isset($player_age['interval']) && $player_age['interval'] > 0) {
            $player = new Player(ProfileUtils::formatUUID($recipient->getIdentifier()));
            if ($player->exists()) {
                $min_age = strtotime('-'.$player_age['interval'].' ' . $player_age['period']);

                if (($player->data()->registered / 1000) > $min_age) {
                    $params['errors'][] = MCStatistics::getLanguage()->get('general', 'age_requirement_not_meet');
                }
            } else {
                $params['errors'][] = MCStatistics::getLanguage()->get('general', 'player_age_not_found');
            }
        }

        return $params;
    }

    public static function minPlayerPlaytime(array $params = []): array {
        $user = $params['user'];
        $product = $params['product'];
        $recipient = $params['recipient'];

        $player_playtime = json_decode($product->data()->min_player_playtime, true) ?? [];
        if (isset($player_playtime['playtime']) && $player_playtime['playtime'] > 0) {
            $player = new Player(ProfileUtils::formatUUID($recipient->getIdentifier()));
            if ($player->exists()) {
                $min_playtime = 3600 * $player_playtime['playtime'];

                if (($player->data()->play_time / 1000) < $min_playtime) {
                    $params['errors'][] = MCStatistics::getLanguage()->get('general', 'playtime_requirement_not_meet');
                }
            } else {
                $params['errors'][] = MCStatistics::getLanguage()->get('general', 'player_playtime_not_found');
            }
        }

        return $params;
    }

}