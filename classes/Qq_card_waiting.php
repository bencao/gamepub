<?php
/**
 * Table Definition for qq_card_waiting
 */
require_once INSTALLDIR.'/classes/Memcached_DataObject.php';

class Qq_card_waiting extends Memcached_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'qq_card_waiting';                 // table name
    public $id;                              // int(11)  not_null primary_key auto_increment
    public $uid;                             // int(11)  not_null
    public $ip;

    /* Static get */
    function staticGet($k,$v=NULL) { return Memcached_DataObject::staticGet('Qq_card_waiting',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
    
    static function saveNew($uid, $ip) {
    	$qcw = new Qq_card_waiting();
    	$qcw->uid = $uid;
    	$qcw->ip = $ip;
    	$qcw->insert();
    	return true;
    }
}
