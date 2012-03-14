<?php
/**
 * Table Definition for notice_search_target
 */
require_once INSTALLDIR.'/classes/Memcached_DataObject.php';

class Notice_search_target extends Memcached_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'notice_search_target';            // table name
    public $id;                              // int(11)  not_null primary_key auto_increment
    public $rid;                             // int(11)  multiple_key
    public $nid;                             // int(11)  multiple_key
    public $uid;                             // int(11)  multiple_key
    public $created;                         // datetime(19)  not_null binary

    /* Static get */
    function staticGet($k,$v=NULL) { return Memcached_DataObject::staticGet('Notice_search_target',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
    
    function saveNew($rid, $nid, $uid=null) {
    	$nst = new Notice_search_target();
    	$nst->rid = $rid;
    	$nst->nid = $nid;
    	$nst->uid = $uid;
    	$nst->created = common_sql_now();
    	return $nst->insert();
    }
}
