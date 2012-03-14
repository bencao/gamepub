<?php
/**
 * Table Definition for group_invitation
 */
require_once INSTALLDIR.'/classes/Memcached_DataObject.php';

class Group_invitation extends Memcached_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'group_invitation';                // table name
    public $code;                            // string(32)  not_null primary_key binary
    public $groupid;                         // int(11)  not_null multiple_key
    public $inviteeid;                       // int(11)  not_null
    public $created;                         // datetime(19)  not_null binary

    /* Static get */
    function staticGet($k,$v=NULL) { return Memcached_DataObject::staticGet('Group_invitation',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
    
    static function isInvitSend($groupid, $userid) {
    	$qry = 'SELECT * FROM group_invitation WHERE groupid = '.
    	       $groupid. ' AND inviteeid = '.$userid;
    	$giv = new Group_invitation();
    	$giv->query($qry);
    	return ($giv->N > 0)? true:false;
    }
    
    static function saveNew($groupid, $userid) {
    	$group_invit = new Group_invitation();
		$group_invit->code = common_confirmation_code(64);
		$group_invit->groupid = $groupid;
		$group_invit->inviteeid = $userid;
		$group_invit->created = common_sql_now();
		$result = $group_invit->insert();
		
    	if (!$result) {
            common_log_db_error($group_invit, 'INSERT', __FILE__);
            return false;
        }
        
        return $group_invit->code;
    }
}
