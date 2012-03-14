<?php


// this daemon service should be called every a minute.

define('SHAISHAI', true);
define('INSTALLDIR', realpath(dirname(__FILE__) . '/..'));

require INSTALLDIR . '/lib/environment.php';
require INSTALLDIR . '/lib/cache.php';
require INSTALLDIR . '/lib/common.php';
require INSTALLDIR . '/lib/router.php';

require_once 'Mail.php';
require_once 'Mail/Queue.php';

$db_options['type']       = 'db';
$db_options['dsn']        = common_config('db', 'database');
$db_options['mail_table'] = 'mail_queue';
	
$mail_options['driver']   = common_config('mail', 'backend');
$mail_options = array_merge($mail_options, (common_config('mail', 'params')) ?
	                                 common_config('mail', 'params') :
	                                 array());



$mail_queue =& Mail_Queue::factory($db_options, $mail_options);

// How many mails could we send each time
 $max_ammount_mails = 20;
 $mail_queue->sendMailsInQueue($max_ammount_mails);
//$mail_queue->sendMailsInQueue();

?>