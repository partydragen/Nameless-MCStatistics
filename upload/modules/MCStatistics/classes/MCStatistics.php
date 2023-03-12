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

    public function __construct() {
        $secret_key = Util::getSetting('secret_key', '', 'MCStatistics');
        if(!empty($secret_key)) {
            $this->_secret_key = $secret_key;
        }
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
            curl_setopt($ch, CURLOPT_URL, 'https://api.mcstatistics.org/v1/player/' . $value);
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
            curl_setopt($ch, CURLOPT_URL, 'https://api.mcstatistics.org/v1/player/' . $value . '/fields');
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
            curl_setopt($ch, CURLOPT_URL, 'https://api.mcstatistics.org/v1/player/' . $value . '/sessions');
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
            curl_setopt($ch, CURLOPT_URL, 'https://api.mcstatistics.org/v1/player/' . $value . '/ip_history');
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
    
    /*
     *  Check for Module updates
     *  Returns JSON object with information about any updates
     */
    public static function updateCheck($current_version = null) {
        $current_version = Util::getSetting('nameless_version');
        $uid = Util::getSetting('unique_id');

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