<?php
if (!defined('SHAISHAI')) { exit(1); }

/**
 * Table Definition for login_log
 */
require_once INSTALLDIR.'/classes/Memcached_DataObject.php';

class Myinviterecord extends Memcached_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'myinviterecord';                  // table name
    public $inviter_id;                      // int(11)  not_null primary_key multiple_key
    public $invitee_id;                      // int(11)  not_null primary_key
    public $created;                         // datetime(19)  not_null binary

    /* Static get */
    function staticGet($k,$v=NULL) { return Memcached_DataObject::staticGet('Myinviterecord',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
    
    static function saveNew($inviter_id, $invitee_id) {
    	$m = new Myinviterecord();
    	$m->inviter_id = $inviter_id;
    	$m->invitee_id = $invitee_id;
    	$m->created = common_sql_now();
    	return $m->insert();
    }
    
    static function getInviteesByInviterid($inviter_id) {
    	$m = new Myinviterecord();
    	$m->whereAdd('inviter_id = ' . $inviter_id);
    	$m->find();
    	
    	$ids = array();
    	while ($m->fetch()) {
    		$ids[] = $m->invitee_id;
    	}
    	
    	return Profile::getProfileByIds($ids);
    }
    
    static function getInviteeNumByInviterid($inviter_id, $since = null) {
    	$m = new Myinviterecord();
    	$m->whereAdd('inviter_id = ' . $inviter_id);
    	if ($since) {
    		$m->whereAdd("created > '" . $since . "'");
    	}
    	$m->find();
    	
    	if ($m) {
    		return $m->N;
    	} else {
    		return 0;
    	}
    }
}
