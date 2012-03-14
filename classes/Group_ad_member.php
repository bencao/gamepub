<?php
/**
 * Table Definition for group_ad_member
 */
require_once INSTALLDIR.'/classes/Memcached_DataObject.php';

class Group_ad_member extends Memcached_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'group_ad_member';                 // table name
    public $id;                              // int(11)  not_null primary_key
    public $group_id;                        // int(11)  not_null
    public $member_id;                       // int(11)  not_null unique_key

    /* Static get */
    function staticGet($k,$v=NULL) { return Memcached_DataObject::staticGet('Group_ad_member',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
    
    static function saveNew($group_id, $member_id) {
    	$gam = new Group_ad_member();
    	$gam->group_id = $group_id;
    	$gam->member_id = $member_id;
    	
    	return $gam->insert();
    }
}
