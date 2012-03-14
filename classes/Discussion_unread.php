<?php
if (!defined('SHAISHAI')) { exit(1); }
/**
 * Table Definition for discussion_unread
 */
require_once INSTALLDIR.'/classes/Memcached_DataObject.php';

class Discussion_unread extends Memcached_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'discussion_unread';               // table name
    public $id;                              // int(11)  not_null primary_key
    public $notice_id;                       // int(11)  
    public $receiver_id;                       // int(11)  multiple_key
    public $sender_id;                         // int(11)  
    public $discussion_id;                   // int(11)  
    public $created;                         // datetime(19)  binary

    /* Static get */
    function staticGet($k,$v=NULL) { return Memcached_DataObject::staticGet('Discussion_unread',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
    
    static function getDiscussionByUserid($user_id, $limit = 3, $since_time = null) {
    	$dn = new Discussion_unread();
    	$dn->whereAdd('receiver_id = ' . $user_id);
    	if ($since_time) {
    		$dn->whereAdd("created > '" . $since_time . "'");
    	}
    	$dn->orderBy('id asc');
    	$dn->limit(0, $limit);
    	$dn->find();
    	
    	return $dn;
    }
    
    static function setReadByNoticeidAndReceiverid($notice_id, $receiver_id) {
    	$dn = new Discussion_unread();
    	$dn->whereAdd('receiver_id = ' . $receiver_id);
    	$dn->whereAdd('notice_id = ' . $notice_id);
    	
    	$dn->find();
    	
    	if ($dn) {
    		while ($dn->fetch()) {
    			$dn->delete();
    		}
    	}
    	return true;
    }
    
    static function setRead($user_id) {
    	$dn = new Discussion_unread();
    	$dn->query('delete from discussion_unread where receiver_id = ' . $user_id);
    	return true;
    }
}
