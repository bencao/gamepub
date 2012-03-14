<?php
/**
 * Table Definition for user_popularize
 */
require_once INSTALLDIR.'/classes/Memcached_DataObject.php';

class User_popularize extends Memcached_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'user_popularize';                 // table name
    public $id;                              // int(11)  not_null primary_key auto_increment
    public $uid;                             // int(11)  multiple_key
    public $rid;                             // int(11)  

    /* Static get */
    function staticGet($k,$v=NULL) { return Memcached_DataObject::staticGet('User_popularize',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
    
    static function getRecruitCodesByUid($uid) {
    	$p = new User_popularize();
    	$p->whereAdd('uid = ' . $uid);
    	$p->find();
    	
    	$codes = array();
    	while ($p->fetch()) {
    		$r = Recruit::staticGet('id', $p->rid);
    		$codes[] = $r->fullcode;
    	}
    	
    	return $codes;
    }
}
