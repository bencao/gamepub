<?php
/**
 * Table Definition for group_application
 */
require_once INSTALLDIR.'/classes/Memcached_DataObject.php';

class Group_application extends Memcached_DataObject
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'group_application';               // table name
    public $groupid;                         // int(11)  not_null primary_key multiple_key
    public $inviteeid;                       // int(11)  not_null primary_key
    public $message;                         // varchar(40)

    /* Static get */
    function staticGet($k,$v=NULL) { return Memcached_DataObject::staticGet('Group_application',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
    
    function &pkeyGet($kv)
    {
        return Memcached_DataObject::pkeyGet('Group_application', $kv);
    }
    
    static function getApplyNum($groupid)
    {
    	$qry = 'select count(*) as appnum from group_application where groupid = %d';
        $groupapp = new Group_application();

        $groupapp->query(sprintf($qry, $groupid));
        $groupapp->fetch();
    	$appNum = intval($groupapp->appnum);
    	$groupapp->free();
        return $appNum;
    }
    
    static function deleteApply($groupid, $userid) 
    {
    	$groupApp = new Group_application();
        $groupApp->groupid   = $groupid;
        $groupApp->inviteeid = $userid;
        
        if ($groupApp->find()) {
        	$result = $groupApp->delete();
	        if (!$result) {
	        	common_log_db_error($groupApp, 'DELETE', __FILE__);
	        	return false;
	        }
        }
        
        return true;
    }
}
