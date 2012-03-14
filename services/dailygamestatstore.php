<?php

// This daemon service should be called every day to record user's score
// So we can rank the users by there score increment
// Mainly used for the ranking list

define('SHAISHAI', true);
define('INSTALLDIR', realpath(dirname(__FILE__) . '/..'));

require INSTALLDIR . '/lib/environment.php';
require INSTALLDIR . '/lib/cache.php';
require INSTALLDIR . '/lib/common.php';
require INSTALLDIR . '/lib/router.php';

require_once INSTALLDIR . '/classes/Game_stat.php';

Game_stat::dailyRecord();

?>