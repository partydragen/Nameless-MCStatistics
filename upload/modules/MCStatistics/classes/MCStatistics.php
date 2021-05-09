<?php
/*
 *	Made by Partydragen
 *  https://github.com/partydragen/Nameless-MCStatistics
 *  https://partydragen.com/
 *
 *  License: MIT
 */

class MCStatistics {
    
    private $_secret_key,
            $_cache,
            $_configuration;
            
    public function __construct($cache) {
        $this->_cache = $cache;
        $this->_configuration = new Configuration($cache);
        
        $secret_key = $this->_configuration->get('mcstatistics', 'secret_key');
        if(!empty($secret_key)) {
            $this->_secret_key = $secret_key;
        }
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
    
    /*
     *  Check for Module updates
     *  Returns JSON object with information about any updates
     */
    public static function updateCheck($current_version = null) {
        $queries = new Queries();

        // Check for updates
        if (!$current_version) {
            $current_version = $queries->getWhere('settings', array('name', '=', 'nameless_version'));
            $current_version = $current_version[0]->value;
        }

        $uid = $queries->getWhere('settings', array('name', '=', 'unique_id'));
        $uid = $uid[0]->value;
		
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
        curl_setopt($ch, CURLOPT_URL, 'https://api.partydragen.com/stats.php?uid=' . $uid . '&version=' . $current_version . '&module=MCStatistics&module_version='.$module->getVersion() . '&domain='. Util::getSelfURL());

        $update_check = curl_exec($ch);
        curl_close($ch);

		$info = json_decode($update_check);
		if (isset($info->message)) {
			die($info->message);
		}
		
        return $update_check;
    }
}