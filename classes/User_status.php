<?php

if (!defined('SHAISHAI')) { exit(1); }

/**
 * Table Definition for video
 */
require_once INSTALLDIR.'/classes/Memcached_DataObject.php';

class User_status extends Memcached_DataObject
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'user_status';      	// table name
    public $id;                       			// int(4)  primary_key not_null
    public $online;						// datetime not null
    
    /* Static get */
    function staticGet($k,$v=null)
    { return Memcached_DataObject::staticGet('User_status',$k,$v); }

    
    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE

    function setTime($user_id) {
    	$time =  strftime('%Y-%m-%d %H:%M:%S', time());
    	$userstatus = User_status::staticGet('id', $user_id);
    	
    	if(empty($userstatus)) {
    		$userstatus = new User_status();
			$qry = 'insert into user_status (id, online) values ('. $user_id . ',\'' . $time . '\')';
			$userstatus->query($qry);
    	} else {
    	    $orig= clone($userstatus);
    		$userstatus->online = $time;
    		$userstatus->update($orig);  		
    	}
    }
    
    function getOffline() {
    	$userstatus = new User_status();
       	
    	//14-30分钟, 如果他在这段时间内退出的话, 也就是说在logout_log里面有记录的话, 则不用记录
//       	$userstatus->whereAdd('now() - online < ' . 1000*60*30 );
//       	$userstatus->whereAdd('now() - online >= ' . 1000*60*14);
//       	$userstatus->whereAdd('exists (select id from logout_log where id = '. $ .')');

//    	$before = 60*30;
//    	$after = 60*14;    	
//    	$now = now();
//    	$qry = 'select * from user_status where ('. $now .'-online)<' . $before . ' and ('. $now .'-online)>=' .
//    			$after . ' and not exists (select id from logout_log where logout_log.id=user_status.id and'.
//    			' ('. $now .'-time)<' . $before . ' and ('. $now .'-time)>=' . $after  . ')';
    	
    	$qry =	'select * from user_status where time_to_sec(timediff(now(), online)) between 60*14  and 60*30 and not exists (select id from logout_log where logout_log.id=user_status.id and time_to_sec(timediff(now(), time)) between 60*14 and 60*30)';
    	//common_debug($qry);
    	$userstatus->query($qry);
       	//$userstatus->find();    	
    	return $userstatus;
    }
    
    static function userLogout() {    
    	$userstatus = User_status::getOffline();
    	while ($userstatus && $userstatus->fetch() ) {
            Logout_log::logNewByUserStatus($userstatus->id, $userstatus->online);
        }
    }
}