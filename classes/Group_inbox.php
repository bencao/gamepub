<?php
/**
 * Table Definition for group_inbox
 */

class Group_inbox extends Memcached_DataObject
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'group_inbox';                     // table name
    public $group_id;                        // int(4)  primary_key not_null
    public $notice_id;                       // int(4)  primary_key not_null
    public $created;                         // datetime()   not_null

    /* Static get */

    function staticGet($k,$v=NULL) { return Memcached_DataObject::staticGet('Group_inbox',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE

    function &pkeyGet($kv)
    {
        return Memcached_DataObject::pkeyGet('Group_inbox', $kv);
    }
    
    function getGroupId($notice_id) {
    	$inbox = new Group_inbox;
    	$inbox->notice_id = $notice_id;
        $inbox->limit(0, 1);
        $id = null;
    	if ($inbox->find()) {
           	if($inbox->fetch()) {
                $id = $inbox->group_id;
            }
        }
        return $id;
    }
}
