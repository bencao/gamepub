<?php

// This daemon service should be called everyday to give excellent user award
// For detail, please refer to the document -- 用户积分系统规则参考.doc
// follower > 1000 && msgnum/day > 4 , add 35 scores
// follower > 100 && msgnum/day > 4 , add 20 scores
// follower > 50 && msgnum/day > 4 , add 10 scores
    
define('SHAISHAI', true);
define('INSTALLDIR', realpath(dirname(__FILE__) . '/..'));

require INSTALLDIR . '/lib/environment.php';
require INSTALLDIR . '/lib/cache.php';
require INSTALLDIR . '/lib/common.php';
require INSTALLDIR . '/lib/router.php';

require_once INSTALLDIR . '/classes/User_grade.php';

User_grade::dailyAward();

?>