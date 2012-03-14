<?php
if (!defined('SHAISHAI')) { exit(1); }

$config['site']['name'] = "GamePub";

$config['site']['fancy'] = true;

//$config['site']['ssl'] = 'sometimes';

$config['site']['server'] = 'www.gamepub.cn';

$config['site']['logfile'] = "/usr/tmp/logs/gamepub.logs";
$config['site']['logdebug'] = true;

$config['site']['yunpingsearch'] = false;
$config['site']['yunpingsearch_port'] = 9093;
$config['site']['yunpingtoken_port'] = 9091;
$config['site']['usetokenizer'] = false;

$config['site']['use_analytics'] = true;
$config['site']['plugins'] = array('feature', 'mission');
$config['site']['features'] = array('hooyou', 'gold');
$config['site']['missions'] = array('welcome', 'usetheme', 'visitpubarea', 'visithof', 'visitgame', 'visitserver', 
	'visithottopic', 'visithotnotice', 'visitfunnypeople', 'visitexperiences', 'visitrequestforhelp', 
	'visitrank', 'visitcitypeople', 'sendtextnotice', 'sendimagenotice', 'sendaudionotice', 'sendvideonotice', 
	'creategroup', 'joingroup', 'visitprofile', 'usehooyou', 'usehooyousc', 
	'usetaggroup', 'invite', 'firstreply', 'addfavor');

// use local database
$config['db']['database'] = "mysql://gp:gp@localhost/gamepubdb";

$config['site']['timezone'] = 'Asia/Chongqing';
$config['site']['language'] = 'zh_CN';

$config['site']['theme'] = 'default';
//$config['site']['invite'] = true;
$config['site']['broughtby'] = '北京晒尔网络科技有限公司';

$config['mail']['notifyfrom'] = 'noreply@gamepub.cn';
$config['mail']['domain'] = 'gamepub.cn';

// else use gmail as smtp server, notice that it will be a bit slow
$config['mail']['backend'] = 'sendmail';
$config['mail']['params'] = array('sendmail_path' => '/usr/local/sbin/sendmail');

$config['mail']['queue_enable'] = true;

$config['inboxes']['enabled'] = 'transitional';
$config['search']['type'] = 'like';

$config['memcached']['enabled'] = false;
$config['memcached']['base'] = 'gp';
$config['memcached']['server'] = '127.0.0.1';
$config['memcached']['port'] = 11211;

$config['sphinx']['enable'] = false;
$config['sphinx']['server'] = '127.0.0.1';
$config['sphinx']['port'] = 3312;

$config['newuser']['default_id'] = '100000';
$config['newuser']['default'] = 'lshaiteam';
$config['newuser']['welcome'] = '欢迎来到GamePub，祝您玩得愉快。';

?>
