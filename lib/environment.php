<?php

mb_internal_encoding('UTF-8');
date_default_timezone_set('Asia/Chongqing');
set_include_path(INSTALLDIR . '/extlib/'. PATH_SEPARATOR . get_include_path());

function _sn_to_path($sn)
{
    $past_root = substr($sn, 1);
    $last_slash = strrpos($past_root, '/');
    if ($last_slash > 0) {
        $p = substr($past_root, 0, $last_slash);
    } else {
        $p = '';
    }
    return $p;
}

// try to figure out where we are. $server and $path
// can be set by including module, else we guess based
// on HTTP info.

if (isset($server)) {
    $_server = $server;
} else {
    $_server = array_key_exists('SERVER_NAME', $_SERVER) ?
      strtolower($_SERVER['SERVER_NAME']) :
    null;
}

if (isset($path)) {
    $_path = $path;
} else {
    $_path = array_key_exists('SCRIPT_NAME', $_SERVER) ?
      _sn_to_path($_SERVER['SCRIPT_NAME']) :
    null;
}

$config =
  array('site' =>
        array('name' => 'GamePub',
        	  'ename' => 'GamePub',
              'server' => $_server,
              'theme' => 'default',
              'design' =>
              array('backgroundcolor' => '#336600',
                    'contentcolor' => '#444444',
                    'sidebarcolor' => '#333333',
                    'textcolor' => '#ffffff',
                    'linkcolor' => '#B8510C',
              		'navcolor' => '#E5630E',
                    'backgroundimage' => null,
                    'disposition' => 1),
              'path' => $_path,
              'logfile' => null,
              'logo' => null,
              'logdebug' => false,
              'fancy' => true,
              'language' => 'zh_CN',
              'languages' => array('zh_CN'),
              'email' =>
              array_key_exists('SERVER_ADMIN', $_SERVER) ? $_SERVER['SERVER_ADMIN'] : null,
              'closed' => false,
              'inviteonly' => false,
              'private' => false,
              'ssl' => 'never',
              'sslserver' => null,
              'shorturllength' => 30,
              'dupelimit' => 60,	# default for same person saying the same thing
              'textlimit' => 280,
              'emotions' => 
              array(array('src' => '/theme/default/images/emotions/01.gif', 'text' => '鼓掌'),
					array('src' => '/theme/default/images/emotions/02.gif', 'text' => '哭'),
					array('src' => '/theme/default/images/emotions/03.gif', 'text' => '怒'),
					array('src' => '/theme/default/images/emotions/04.gif', 'text' => '微笑'),
					array('src' => '/theme/default/images/emotions/05.gif', 'text' => '汗'),
					array('src' => '/theme/default/images/emotions/06.gif', 'text' => '再见'),
					array('src' => '/theme/default/images/emotions/07.gif', 'text' => '酷'),
					array('src' => '/theme/default/images/emotions/08.gif', 'text' => '惊讶'),
					array('src' => '/theme/default/images/emotions/09.gif', 'text' => '口哨'),
					array('src' => '/theme/default/images/emotions/10.gif', 'text' => '拥抱'),
					array('src' => '/theme/default/images/emotions/11.gif', 'text' => '傻笑'),
					array('src' => '/theme/default/images/emotions/12.gif', 'text' => '惊喜'),
					array('src' => '/theme/default/images/emotions/13.gif', 'text' => '疯了'),
					array('src' => '/theme/default/images/emotions/14.gif', 'text' => '忧伤'),
					array('src' => '/theme/default/images/emotions/15.gif', 'text' => '胜利'),
					array('src' => '/theme/default/images/emotions/16.gif', 'text' => '喜欢'),
					array('src' => '/theme/default/images/emotions/17.gif', 'text' => '大笑'),
					array('src' => '/theme/default/images/emotions/18.gif', 'text' => '发呆'),
					array('src' => '/theme/default/images/emotions/19.gif', 'text' => '切'),
					array('src' => '/theme/default/images/emotions/20.gif', 'text' => '讨厌'),
					array('src' => '/theme/default/images/emotions/21.gif', 'text' => '功夫'),
					array('src' => '/theme/default/images/emotions/22.gif', 'text' => '难过'),
					array('src' => '/theme/default/images/emotions/23.gif', 'text' => '生气'),
					array('src' => '/theme/default/images/emotions/24.gif', 'text' => '郁闷'),
					array('src' => '/theme/default/images/emotions/25.gif', 'text' => '鄙视'),
					array('src' => '/theme/default/images/emotions/26.gif', 'text' => '闭嘴'),
					array('src' => '/theme/default/images/emotions/27.gif', 'text' => '色'))
		),	// end site
        'syslog' =>
        array('appname' => 'shaishai', # for syslog
              'priority' => 'debug', # XXX: currently ignored
              'facility' => LOG_USER,
        	  'level' => 8),
        'queue' =>
        array('enabled' => false,
              'subsystem' => 'db', # default to database, or 'stomp'
              'stomp_server' => null,
              'queue_basename' => 'shaishai',
              'stomp_username' => null,
              'stomp_password' => null,
        	  'stomp_persistent' => true, // keep items across queue server restart, if persistence is enabled
              'stomp_manual_failover' => true, // if multiple servers are listed, treat them as separate (enqueue on one randomly, listen on all)
              'softlimit' => '90%', // total size or % of memory_limit at which to restart queue threads gracefully
              'spawndelay' => 1, // Wait at least N seconds between (re)spawns of child processes to avoid slamming the queue server with subscription startup
              'debug_memory' => false, // true to spit memory usage to log
              'inboxes' => true, // true to do inbox distribution & output queueing from in background via 'distrib' queue
              'breakout' => array(), // List queue specifiers to break out when using Stomp queue.
                                     // Default will share all queues for all sites within each group.
                                     // Specify as <group>/<queue> or <group>/<queue>/<site>,
                                     // using nickname identifier as site.
                                     //
                                     // 'main/distrib' separate "distrib" queue covering all sites
                                     // 'xmpp/xmppout/mysite' separate "xmppout" queue covering just 'mysite'
              'max_retries' => 10, // drop messages after N failed attempts to process (Stomp)
              'dead_letter_dir' => false, // set to directory to save dropped messages into (Stomp)
              ),
        'mail' =>
        array('backend' => 'mail',
              'params' => null),
        'profile' =>
        array('banned' => array()),
        'avatar' =>
        array('server' => null,
              'dir' => INSTALLDIR . '/file/',
              'path' => $_path . '/file/'),
        'background' =>
        array('server' => null,
              'dir' => INSTALLDIR . '/background/',
              'path' => $_path . '/background/'),
        //消息来源判断, 在saveNew里面
        'theme' =>
        array('server' => null,
              'dir' => null,
              'path'=> null),
        //count前的消息小于timespan(以秒计时)
        'throttle' =>
        array('enabled' => false, // whether to throttle edits; false by default
              'count' => 20, // number of allowed messages in timespan
              'timespan' => 600), // timespan for throttling
        'xmpp' =>
        array('enabled' => false,
              'server' => 'INVALID SERVER',
              'port' => 5222,
              'user' => 'update',
              'encryption' => true,
              'resource' => 'uniquename',
              'password' => 'blahblahblah',
              'host' => null, # only set if != server
              'debug' => false, # print extra debug info
              'public' => array()), # JIDs of users who want to receive the public stream
        'memcached' =>
        array('enabled' => false,
              'server' => 'localhost',
              'base' => null,
              'port' => 11211),
        'tag' =>
        array('dropoff' => 864000.0),
        //消息存储方式
        'inboxes' =>
        array('enabled' => true), # on by default for new sites
        'newuser' =>
        array('default' => null,
              'welcome' => null),
        'attachments' =>
        //在本目录的file文件夹下, 同时规定了上传文件的格式
        array('server' => null,
              'dir' => INSTALLDIR . '/file/',
              'path' => $_path . '/file/',
              'supported' => array('image/png',
                                   'image/jpeg',
                                   'image/gif'),
        'file_quota' => 3000000,
        'user_quota' => 50000000,
        'monthly_quota' => 15000000,
        'uploads' => true,
        'filecommand' => '/usr/bin/file',
        ),
        'oohembed' => array('endpoint' => 'http://oohembed.com/oohembed/'),
        'search' =>
        array('type' => 'fulltext'),
        'sessions' =>
        array('handle' => false, // whether to handle sessions ourselves
              'debug' => false), // debugging output for sessions
         'notice' =>
        array('contentlimit' => null),
        'message' =>
        array('contentlimit' => null),
        );
        
require_once('PEAR.php');
$config['db'] = &PEAR::getStaticProperty('DB_DataObject','options');

$config['db'] =
  array('database' => 'YOU HAVE TO SET THIS IN config.php',
        'schema_location' => INSTALLDIR . '/classes',
        'class_location' => INSTALLDIR . '/classes',
        'require_prefix' => 'classes/',
        'class_prefix' => '',
        'mirror' => null,
        'utf8' => true,
        'db_driver' => 'DB',
        'quote_identifiers' => false,
        'type' => 'mysql' );

$_db_name = substr($config['db']['database'], strrpos($config['db']['database'], '/') + 1);

$config['db']['ini_'.$_db_name] = INSTALLDIR.'/classes/'.$_db_name.'.ini';

// From most general to most specific:
// server-wide, then vhost-wide, then for a path,
// finally for a dir (usually only need one of the last two).

if (isset($conffile)) {
    $_config_files = array($conffile);
} else {
    $_config_files[] = INSTALLDIR.'/config.php';
}

$_have_a_config = false;

foreach ($_config_files as $_config_file) {
    if (@file_exists($_config_file)) {
        include_once($_config_file);
        $_have_a_config = true;
    }
}

function _have_config()
{
    global $_have_a_config;
    return $_have_a_config;
}

// XXX: Throw a conniption if database not installed

// Fixup for LShai.ini

function common_config($main, $sub)
{
    global $config;
    return isset($config[$main][$sub]) ? $config[$main][$sub] : false;
}
