<?php
/**
 * Table Definition for group_member
 */

class Group_member extends Memcached_DataObject
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'group_member';                    // table name
    public $group_id;                        // int(4)  primary_key not_null
    public $user_id;                      // int(4)  primary_key not_null
    public $is_admin;                        // tinyint(1)
    public $created;                         // datetime()   not_null
    public $modified;                        // timestamp()   not_null default_CURRENT_TIMESTAMP

    /* Static get */
    function staticGet($k,$v=NULL) { return Memcached_DataObject::staticGet('Group_member',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE

    function &pkeyGet($kv)
    {
        return Memcached_DataObject::pkeyGet('Group_member', $kv);
    }
    
    static function getAdminNum($groupid)
    {
    	$qry = 'select count(*) as adminNum from group_member where group_id = %d and is_admin = 1';
        $groupmember = new Group_member();

        $groupmember->query(sprintf($qry, $groupid));
        $groupmember->fetch();
    	$adminNum = intval($groupmember->adminNum);
    	$groupmember->free();
        return $adminNum;
    }
}
