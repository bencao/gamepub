<?php

if (!defined('SHAISHAI')) { exit(1); }

/**
 * Table Definition for logout_log
 */
require_once INSTALLDIR.'/classes/Memcached_DataObject.php';

class Logout_log extends Memcached_DataObject
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'logout_log';                            // table name
    public $id;            // int(4)  primary_key not_null
    public $counts;        // int(4)  primary_key not_null
    public $time;          // timestamp()   not_null default_CURRENT_TIMESTAMP

    /* Static get */
    function staticGet($k,$v=null)
    { return Memcached_DataObject::staticGet('Logout_log',$k,$v); }

    
    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
	static function logNew($user_id) {
		$log = new Logout_log();
		$log->query('BEGIN');
		$log->id = $user_id;
		$log->counts = Logout_log::getLastLogoutCount($user_id) + 1;
		$log->time = common_sql_now();
        if (!$log->insert()) {
            common_log_db_error($log, 'INSERT', __FILE__);
            return false;
        }
        $log->query('COMMIT');
        return $log;
    }
    
    static function getLastLogoutCount($id) {
    	$log = new Logout_log();
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
    
    static function getLastLogoutTime($id) {
    	$log = new Logout_log();
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
        return Memcached_DataObject::pkeyGet('Logout_log', $kv);
    }
    
	static function logNewByUserStatus($user_id, $time) {
		$log = new Logout_log();
		$log->query('BEGIN');
		$log->id = $user_id;
		$log->counts = Logout_log::getLastLogoutCount($user_id) + 1;
		$log->time = $time;
        if (!$log->insert()) {
            common_log_db_error($log, 'INSERT', __FILE__);
            return false;
        }
        $log->query('COMMIT');
        return $log;
    }
    
    //memcache uncache 这一块也要添加上去
    static function getUnloginUser($day) {
    	//$sql = 'select id, max(counts) as last from logout_log where time= $day group by id';    	
    	$log = new Logout_log();
		$log->selectAdd(); // clears it
        $log->selectAdd('id');
        $log->selectAdd('max(counts) as last');
        
        $day =  '\'' . strftime('%Y-%m-%d', $day) . '\'';

		$log->whereAdd('time regexp ' . $day );
        $log->groupBy('id');
        
        $ids = array();        
        if ($log->find()) {
        	while ($log->fetch()) {
        		$ids[] = $log->id;
        	}
        }
        $log->free();
        
        return $ids;    	
    }
}

?>