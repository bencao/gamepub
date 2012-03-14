<?php
/**
 * Table Definition for qq_card_one_yuan
 */
require_once INSTALLDIR.'/classes/Memcached_DataObject.php';

class Qq_card_one_yuan extends Memcached_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'qq_card_one_yuan';                // table name
    public $id;                              // int(11)  not_null primary_key auto_increment
    public $card_no;                         // string(64)  not_null binary
    public $card_password;                   // string(64)  not_null binary
    public $uid;                            // int(11)  multiple_key
    public $ip;
    public $modified;                        // datetime(19)  not_null binary
    public $created;                         // datetime(19)  not_null binary

    /* Static get */
    function staticGet($k,$v=NULL) { return Memcached_DataObject::staticGet('Qq_card_one_yuan',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
    
    static function fetchACard($uid, $ip=null) {
    	$qcoy = new Qq_card_one_yuan();
    	$qcoy->whereAdd('uid is null');
    	$qcoy->orderBy('id asc');
    	$qcoy->limit(0, 1);
    	$qcoy->find();
    	$result = $qcoy->fetch();
    	
    	if ($result) {
    		$qo = clone($qcoy);
    		$qcoy->uid = $uid;
    		$qcoy->ip = $ip;
    		$qcoy->modified = common_sql_now();
    		$qcoy->update($qo);
    		return $qcoy;
    	} else {
    		return false;
    	}
    }
    
    static function getCountByIPFeature($feature) {
    	$qcoy = new Qq_card_one_yuan();
    	$qcoy->whereAdd("ip like '" . $feature . "'");
    	
    	return $qcoy->count();
    }
}
