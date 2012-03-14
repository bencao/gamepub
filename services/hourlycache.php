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
		$cache->set(common_cache_key('game:recents50:' . $agi), 
			Game::getRecents50($agi));
	}
}
?>