<?php
/*
 *	Made by Partydragen
 *  https://github.com/partydragen/Nameless-MCStatistics
 *  https://partydragen.com/
 *
 *  License: MIT
 */

class MCStatistics {

    private $_secret_key;

    /**
     * @var Language Instance of Language class for translations
     */
    private static Language $_mcstatistics_language;
    private Cache $_cache;

    public function __construct(Cache $cache) {
        $this->_cache = $cache;

        $secret_key = Settings::get('secret_key', '', 'MCStatistics');
        if(!empty($secret_key)) {
            $this->_secret_key = $secret_key;
        }
    }

    public function isSetup(): bool {
        return !empty($this->_secret_key);
    }

    /**
     * @return Language The current language instance for translations
     */
    public static function getLanguage(): Language {
        if (!isset(self::$_mcstatistics_language)) {
            self::$_mcstatistics_language = new Language(ROOT_PATH . '/modules/MCStatistics/language');
        }

        return self::$_mcstatistics_language;
    }

    // Get player data
    public function fetchPlayerData($value) {
        if($this->_secret_key) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://api.mcstatistics.org/v1/player/' . urlencode($value));
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-MCStatistics-Secret: ' . $this->_secret_key));
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            
            $ch_result = curl_exec($ch);
            $result = json_decode($ch_result);
            curl_close($ch);
            
            return $result;
        }
        
        return null;
    }
    
    // Get player data
    public function fetchPlayerFields($value) {
        if($this->_secret_key) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://api.mcstatistics.org/v1/player/' . urlencode($value) . '/fields');
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-MCStatistics-Secret: ' . $this->_secret_key));
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            
            $ch_result = curl_exec($ch);
            $result = json_decode($ch_result);
            curl_close($ch);
            
            return $result;
        }
        
        return null;
    }
    
    // Get player data
    public function fetchPlayerSessions($value) {
        if($this->_secret_key) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://api.mcstatistics.org/v1/player/' . urlencode($value) . '/sessions');
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-MCStatistics-Secret: ' . $this->_secret_key));
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            
            $ch_result = curl_exec($ch);
            $result = json_decode($ch_result);
            curl_close($ch);
            
            return $result;
        }
        
        return null;
    }
    
    // Get player data
    public function fetchPlayerIPHistory($value) {
        if($this->_secret_key) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://api.mcstatistics.org/v1/player/' . urlencode($value) . '/ip_history');
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-MCStatistics-Secret: ' . $this->_secret_key));
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            
            $ch_result = curl_exec($ch);
            $result = json_decode($ch_result);
            curl_close($ch);
            
            return $result;
        }
        
        return null;
    }

    public function getPlayers() {
        if (!$this->isSetup()) {
            return [];
        }

        $this->_cache->setCache('mcstatistics_players');
        if (!$this->_cache->isCached('players')) {
            $header = ['headers' => [
                'X-MCStatistics-Secret' => $this->_secret_key
            ]];

            $request = HttpClient::get('https://api.mcstatistics.org/v1/players', $header);
            if (!$request->hasError()) {
                $json = $request->json();

                $this->_cache->store('players', $json, 120);
                return $json;
            }
        } else {
            return $this->_cache->retrieve('players');
        }

        return [];
    }

    public function getAllLeaderboards() {
        if (!$this->isSetup()) {
            return [];
        }

        $this->_cache->setCache('mcstatistics_leaderboards');
        if (!$this->_cache->isCached('leaderboards')) {
            $header = ['headers' => [
                'X-MCStatistics-Secret' => $this->_secret_key
            ]];

            $request = HttpClient::get('https://api.mcstatistics.org/v1/placeholders?leaderboard=true', $header);
            if (!$request->hasError()) {
                $json = $request->json();

                $this->_cache->store('leaderboards', $json, 120);
                return $json;
            }
        } else {
            return $this->_cache->retrieve('leaderboards');
        }

        return [];
    }

    public function getLeaderboards(string $servername = null, string $leaderboard = null): array {
        if (!$this->isSetup()) {
            return [];
        }

        $leaderboards = [];
        $json = $this->getAllLeaderboards();
        if (isset($json->servers)) {
            foreach ($json->servers as $server) {
                if ($servername != null && strtolower($server->name) != strtolower($servername)) {
                    continue;
                }

                foreach ($server->placeholders as $placeholder) {
                    if ($leaderboard != null && strtolower($placeholder->friendly_name) != strtolower($leaderboard)) {
                        continue;
                    }

                    $leaderboards[] = [
                        'server_name' => Output::getClean($server->name),
                        'friendly_name' => Output::getClean($placeholder->friendly_name),
                        'link' => Url::build('/leaderboard/' . urlencode($server->name) . '/' . urlencode($placeholder->friendly_name))
                    ];
                }
            }
        }

        return $leaderboards;
    }

    public function getLeaderboardPlayers(string $server, string $leaderboard) {
        if (!$this->isSetup()) {
            return [];
        }

        $cache_key = 'leaderboard_' . strtolower($leaderboard) . '_' . strtolower($server);
        $this->_cache->setCache('mcstatistics_leaderboard');
        if (!$this->_cache->isCached('leaderboards')) {
            $header = ['headers' => [
                'X-MCStatistics-Secret' => $this->_secret_key
            ]];

            $request = HttpClient::get('https://api.mcstatistics.org/v1/leaderboard/' . urlencode($leaderboard) . '?server=' . urlencode($server), $header);
            if (!$request->hasError()) {
                $json = $request->json();

                $this->_cache->store($cache_key, $json, 120);
                return $json;
            }
        } else {
            return $this->_cache->retrieve($cache_key);
        }

        return [];
    }

    /*
     *  Check for Module updates
     *  Returns JSON object with information about any updates
     */
    public static function updateCheck() {
        $current_version = Settings::get('nameless_version');
        $uid = Settings::get('unique_id');

		$enabled_modules = Module::getModules();
		foreach($enabled_modules as $enabled_item){
			if($enabled_item->getName() == 'MCStatistics'){
				$module = $enabled_item;
				break;
			}
		}

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_URL, 'https://api.partydragen.com/stats.php?uid=' . $uid . '&version=' . $current_version . '&module=MCStatistics&module_version='.$module->getVersion() . '&domain='. URL::getSelfURL());

        $update_check = curl_exec($ch);
        curl_close($ch);

		$info = json_decode($update_check);
		if (isset($info->message)) {
			die($info->message);
		}
		
        return $update_check;
    }
}