<?php
/*
 *	Made by Partydragen
 *  https://github.com/partydragen/Nameless-MCStatistics
 *  https://partydragen.com/
 *
 *  License: MIT
 */

class Player {
    private $_data;

    public function __construct(string $player) {
        $header = ['headers' => [
            'X-MCStatistics-Secret' => Settings::get('secret_key', '', 'MCStatistics')
        ]];

        $request = HttpClient::get('https://api.mcstatistics.org/v1/player/' . urlencode($player), $header);
        if (!$request->hasError()) {
            $json = $request->json();
            if (!isset($json->error)) {
                $this->_data = $json;
            }
        }
    }

    public function exists(): bool {
        return !empty($this->_data);
    }

    public function data() {
        return $this->_data;
    }

    public function isOnline(): bool {
        return false;
    }

    public function lastSeenOnServer(): Server {

    }
}