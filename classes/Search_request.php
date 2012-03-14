<?php
/**
 * Table Definition for search_request
 */
require_once INSTALLDIR.'/classes/Memcached_DataObject.php';

class Search_request extends Memcached_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'search_request';                  // table name
    public $id;                              // int(11)  not_null primary_key auto_increment
    public $q;                               // string(255)  
    public $uid;                             // int(11)  
    public $source;                          // int(11)  multiple_key
    public $created;                         // datetime(19)  not_null binary

    /* Static get */
    function staticGet($k,$v=NULL) { return Memcached_DataObject::staticGet('Search_request',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
    
    /**
     * 
     * @param $source 0 - notice, 1 - people, 2 - group
     * @param $q
     * @param $uid
     */
    static function saveNew($source, $q, $uid = null) {
    	$sr = new Search_request();
    	$sr->q = $q;
    	$sr->source = $source;
    	$sr->uid = $uid;
    	$sr->created = common_sql_now();
    	return $sr->insert();
    }
    
    static function deleteUselessRequest() {
    	$sr = new Search_request();
    	$sr->query("delete from search_request where q like '%@%' or q like '%我的截图%' or q like '%qq%' or q like '%QQ%' or q like '%Qq%' or q like '%qQ%' or q like '%Q币%' or q like '%q币%'");
    	
    	return true;
    }
}
