<?php

if (!defined('SHAISHAI')) { exit(1); }

/**
 * Table Definition for login_log
 */
require_once INSTALLDIR.'/classes/Memcached_DataObject.php';

class Login_log extends Memcached_DataObject
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'login_log';                            // table name
    public $id;            // int(4)  primary_key not_null
    public $counts;        // int(4)  primary_key not_null
    public $time;          // timestamp()   not_null default_CURRENT_TIMESTAMP

    /* Static get */
    function staticGet($k,$v=null)
    { return Memcached_DataObject::staticGet('Login_log',$k,$v); }

    
    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
	static function logNew($user) {
		$log = new Login_log();
		$log->query('BEGIN');
		$log->id = $user->id;
		$log->counts = Login_log::getLastLoginCount($user->id) + 1;
		$log->time = common_sql_now();
        if (!$log->insert()) {
            common_log_db_error($log, 'INSERT', __FILE__);
            return false;
        }
        $log->query('COMMIT');
        return $log;
    }
    
    static function getLastLoginCount($id) {
    	$log = new Login_log();
		$log->selectAdd(); // clears it
        $log->selectAdd('counts');

        $log->limit(0, 1);
        $log->orderBy('counts DESC');
        $log->whereAdd('id = '.$id);
        $rtid = 0;
        if ($log->find()) {
        	if ($log->fetch()) {
        		$rtid = $log->counts;
        	}
        }
        return $rtid;
    }
    
//    	登录的次数
//    SELECT id, max(counts) AS maxcount
//	FROM `login_log`
//	GROUP BY id
//	ORDER BY maxcount DESC
//	LIMIT 0 , 30	
	
    static function getLoginCountOrder($limit) {
    	$log = new Login_log();
		$log->selectAdd(); // clears it
        $log->selectAdd('id');
        $log->selectAdd('max(counts) AS maxcount');

        $log->limit(0, $limit);
        $sub->groupBy('id');
        $log->orderBy('maxcount DESC');
        $log->whereAdd('id = '.$id);
        $logs = array();
        if ($log->find()) {
        	if ($log->fetch()) {
        		$logs[] = array($log->id, $log->maxcount);
        	}
        }
        $log->free();
        return $logs;
    }
    
    static function getLastLoginTime($id) {
    	$log = new Login_log();
		$log->selectAdd(); // clears it
        $log->selectAdd('time');

        $log->limit(0, 1);
        $log->orderBy('counts DESC');
        $log->whereAdd('id = '.$id);
        $rttime = common_sql_now();
        if ($log->find()) {
        	if ($log->fetch()) {
        		$rttime = $log->time;
        	}
        }
        return $rttime;
    }

    function &pkeyGet($kv)
    {
        return Memcached_DataObject::pkeyGet('Login_log', $kv);
    }
}

?>