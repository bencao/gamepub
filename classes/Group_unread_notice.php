<?php

if (!defined('SHAISHAI')) { exit(1); }

/**
 * Table Definition for video
 */
require_once INSTALLDIR.'/classes/Memcached_DataObject.php';

class Group_unread_notice extends Memcached_DataObject
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'group_unread_notice';      	// table name
    public $group_id;                       		// int(4)  primary_key not_null
    public $user_id;								// int primary_key not_null
    public $notice_num; 							// int
    public $modified;         						// datetime
    
    /* Static get */
    function staticGet($k,$v=null)
    { return Memcached_DataObject::staticGet('Group_unread_notice',$k,$v); }
    
	function &pkeyGet($kv)
    {
        return Memcached_DataObject::pkeyGet('Group_unread_notice', $kv);
    }
    
	function setRead($user_id, $group_id)
    {
//    	$gun = new Group_unread_notice();
//    	$sql = 'update group_unread_notice set notice _num = 0 where user_id = ' . $user_id . ' and group_id = ' . $group_id;
//        $gun->query($sql);        
//        $gun->free();
        $gun = Group_unread_notice::pkeyGet(array('user_id' => $user_id,
                                           'group_id' => $group_id));
        if(!empty($gun)) {
        	$orig = clone($gun);
    		$gun->notice_num = 0;
    		$gun->modified = common_sql_now();
    		$gun->update($orig);
        }
    }
    
    function setReadByUserid($user_id) {
    	$gun = new Group_unread_notice();
    	$gun->query('update group_unread_notice set notice_num = 0 where user_id = ' . $user_id);
		return true;
    }
    
    //删除里面的消息可能需要减1
	function addUnread($user_id, $group_id)
    {
    	$gun = Group_unread_notice::pkeyGet(array('user_id' => $user_id,
                                           'group_id' => $group_id));
    	if(empty($gun)) {
    		$gun = new Group_unread_notice();
    		$gun->user_id = $user_id;
    		$gun->group_id = $group_id;
    		$gun->notice_num = 1;
    		$gun->modified = common_sql_now();
    		
    		if (!$gun->insert()) {
	            common_log_db_error($gun, 'INSERT', __FILE__);
	            return false;
      		}      		
    	} else {
    		$orig = clone($gun);
    		$gun->notice_num += 1;
    		$gun->modified = common_sql_now();
    		$gun->update($orig);
    	}
    }
    
	function unReadCount($user_id)
    {
    	$gun = new Group_unread_notice();
        $qry =
          'SELECT notice_num, group_id ' .
          'FROM group_unread_notice ' .
          'WHERE user_id = ' . $user_id;

        $gun->query($qry);
        $guns = array();

        while ($gun->fetch()) {
            $guns[] = array('group_id' => $gun->group_id, 'notice_num' => $gun->notice_num);
        }
        $gun->free();

        return $guns;
    }
    
    function getUnreadGroupsByUserid($user_id, $limit = 8) {
    	$gun = new Group_unread_notice();
    	$gun->whereAdd('user_id = ' . $user_id);
    	$gun->whereAdd('notice_num > 0');
    	$gun->orderBy('notice_num desc');
    	$gun->limit(0, $limit);
    	$gun->find();
    	
    	return $gun;
    }
}