<?php

if (!defined('SHAISHAI')) { exit(1); }

define('SHAISHAI_VERSION', 'i');

define('AVATAR_PROFILE_SIZE', 120);
define('AVATAR_STREAM_SIZE', 48);
define('AVATAR_MINI_SIZE', 24);

define('GROUP_LOGO_PROFILE_SIZE', 130);
define('GROUP_LOGO_STREAM_SIZE', 72);
define('GROUP_LOGO_MINI_SIZE', 24);

define('MESSAGES_PER_PAGE', 20);

define('NOTICES_PER_PAGE', 20);
define('RECORDS_PER_PAGE', 50);
define('PROFILES_PER_PAGE', 20);
define('GROUP_NOTICES_PER_PAGE', 30);
define('GROUPS_PER_PAGE', 18);
define('GROUPS_PER_PAGE_GAME', 9);
define('GROUPS_HOTTEST', 9);

define('QUESTIONS_PER_PAGE', 8);

define('UNAME_FMT', '[a-zA-Z]([a-zA-z0-9_]*)');

require INSTALLDIR . '/extlib/DB/DataObject.php';
require INSTALLDIR . '/extlib/DB/DataObject/Cast.php';
require INSTALLDIR . '/extlib/Validate.php';

require INSTALLDIR . '/lib/event.php';
require INSTALLDIR . '/lib/util.php';
require INSTALLDIR . '/lib/templatefactory.php';
require INSTALLDIR . '/lib/shaiaction.php';
require INSTALLDIR . '/lib/theme.php';

function __autoload($class)
{
	if (mb_substr($class, -6) == 'Action') {
		$filename = strtolower(mb_substr($class, 0, -6)) . '.php';
		if (mb_substr($class, 0, 3) == 'Api' 
			&& is_file(INSTALLDIR.'/api/' . $filename)) {
        	require_once(INSTALLDIR.'/api/' . $filename);
		} else if (is_file(INSTALLDIR.'/actions/' . $filename)) {
        	require_once(INSTALLDIR.'/actions/' . $filename);
		} else if (is_file(INSTALLDIR.'/ajax/' . $filename)) {
        	require_once(INSTALLDIR.'/ajax/' . $filename);
		} else if (is_file(INSTALLDIR.'/lib/' . strtolower($class) . '.php')) {
	        require_once(INSTALLDIR.'/lib/' . strtolower($class) . '.php');
	    }
    } else if (mb_substr($class, -12) == 'HTMLTemplate') {
    	require_once(INSTALLDIR.'/templates/' . strtolower($class) . '.php');
    } else if (is_file(INSTALLDIR.'/classes/' . $class . '.php')) {
        require_once(INSTALLDIR.'/classes/' . $class . '.php');
    } else if (is_file(INSTALLDIR.'/lib/' . strtolower($class) . '.php')) {
        require_once(INSTALLDIR.'/lib/' . strtolower($class) . '.php');
    }
}
