<?php

define('SHAISHAI', true);
define('INSTALLDIR', realpath(dirname(__FILE__) . '/..'));

require INSTALLDIR . '/lib/environment.php';
require INSTALLDIR . '/lib/cache.php';
require INSTALLDIR . '/lib/common.php';
require INSTALLDIR . '/lib/router.php';

$cache = common_memcache();
//$cache->delete(common_cache_key('user_group:notice_ids:' . $group_inbox->group_id));

for($i=1; $i<5; $i++) {
	$cache->delete(common_cache_key('user_group:notice_ids:100000:'.$i));
	$cache->delete(common_cache_key('user_group:notice_ids:100000:'.$i.';last'));
}
	
