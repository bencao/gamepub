<?php

// This daemon service should be called every week to clear
// messages which are out of date

define('SHAISHAI', true);
define('INSTALLDIR', realpath(dirname(__FILE__) . '/..'));

require INSTALLDIR . '/lib/environment.php';
require INSTALLDIR . '/lib/cache.php';
require INSTALLDIR . '/lib/common.php';
require INSTALLDIR . '/lib/router.php';

require_once INSTALLDIR . '/classes/System_message.php';
require_once INSTALLDIR . '/classes/Receive_sysmes.php';

System_message::clearOutOfDates();
Receive_sysmes::clearOutOfDates();

?>