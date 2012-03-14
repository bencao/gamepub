<?php

define('SHAISHAI', true);
define('INSTALLDIR', realpath(dirname(__FILE__) . '/..'));

require INSTALLDIR . '/lib/environment.php';
require INSTALLDIR . '/lib/cache.php';
require INSTALLDIR . '/lib/common.php';
require INSTALLDIR . '/lib/router.php';

$cache = common_memcache();
	
if (! empty($cache)) {	
	
	$all_game_ids = Game::listAllGameIds();
	foreach ($all_game_ids as $agi) {
		// 缓存每个游戏内最活跃的50个玩家id，按活跃度由高到低有序排列
		$cache->set(common_cache_key('game:activetop50:' . $agi), 
			Game::getActiveTop50($agi));
			
		$cache->set(common_cache_key('game:random100male:' . $agi), 
			Game::getRandom100($agi,'M'));
			
		$cache->set(common_cache_key('game:random100female:' . $agi), 
			Game::getRandom100($agi,'F'));
			
		$cache->set(common_cache_key('game:videotop20:' . $agi), 
			Game::getVideoTop20($agi));
			
		$cache->set(common_cache_key('game:picturetop20:' . $agi), 
			Game::getPicTop20($agi));
			
		$cache->set(common_cache_key('game:texttop20:' . $agi), 
			Game::getTextTop20($agi));
		
	}
	
	$all_server_ids = Game_server::listAllServerIds();
	foreach ($all_server_ids as $asi) {
		// 缓存每个服务器内最活跃的50个玩家id，按活跃度由高到低有序排列
		$cache->set(common_cache_key('gameserver:activetop50:' . $asi), 
			Game_server::getActiveTop50($asi));
	
		$cache->set(common_cache_key('gameserver:populartop50male:' . $asi), 
			Game_server::getPopularTop50($asi, 'M'));
	
		$cache->set(common_cache_key('gameserver:populartop50female' . $asi), 
			Game_server::getPopularTop50($asi, 'F'));

		$cache->set(common_cache_key('gameserver:random100male:' . $asi), 
			Game_server::getRandom100($asi, 'M'));
			
		$cache->set(common_cache_key('gameserver:random100female:' . $asi), 
			Game_server::getRandom100($asi, 'F'));
			
		$cache->set(common_cache_key('gameserver:videotop20' . $asi), 
			Game_server::getVideoTop20($asi));
			
		$cache->set(common_cache_key('gameserver:picturetop20' . $asi), 
			Game_server::getPicTop20($asi));
		
		$cache->set(common_cache_key('gameserver:texttop20' . $asi), 
			Game_server::getTextTop20($asi));
	}
	
	
		$cache->set(common_cache_key('webuser:activetop50:'), 
			User::getActiveTop50());
	
		
		$cache->set(common_cache_key('webuser:random100male:'), 
			Profile::getRandom100('M'));
	
		
		$cache->set(common_cache_key('webuser:random100female'), 
			Profile::getRandom100('F'));
			
		
		$cache->set(common_cache_key('webuser:videotop20'), 
			Notice::getVideoTop20());
			
		
		$cache->set(common_cache_key('webuser:picturetop20'), 
			Notice::getPicTop20());
		
		$cache->set(common_cache_key('webuser:texttop20'), 
			Notice::getTextTop20());
			
}
?>