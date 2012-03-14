<?php

define('HASH_ALGO', 'ripemd160');

function common_cache_key($extra)
{
    $base_key = common_config('memcached', 'base');

    if (empty($base_key)) {
        $base_key = common_keyize(common_config('site', 'ename'));
    }
    return $base_key . ':' . $extra;
}

function common_keyize($str)
{
    $str = strtolower($str);
    $str = preg_replace('/\s/', '_', $str);
    return $str;
}

$mcache = null;

function common_memcache()
{
    if (! common_config('memcached', 'enabled')) {
        return false;
    } else {
        global $mcache;
    	if (! $mcache) {
            $mcache = new Memcache();
            $servers = common_config('memcached', 'server');
            if (is_array($servers)) {
                foreach($servers as $server) {
                    $mcache->addServer($server);
                }
            } else {
                $mcache->addServer($servers);
            }
        }
        return $mcache;
    }
}

?>