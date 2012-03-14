<?php
/**
 * Table Definition for popularize_source
 */
require_once INSTALLDIR.'/classes/Memcached_DataObject.php';

class Popularize_source extends Memcached_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'popularize_source';               // table name
    public $id;                              // int(11)  not_null primary_key auto_increment
    public $areacode;                        // string(3)  not_null
    public $seqno;                           // string(3)  not_null
    public $name;                            // string(64)  

    /* Static get */
    function staticGet($k,$v=NULL) { return Memcached_DataObject::staticGet('Popularize_source',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
    
    static function getSource($areaCode, $seqno) {
    	$ps = new Popularize_source();
    	$ps->whereAdd("areacode = '" . $areaCode . "' AND seqno = '" . $seqno . "'");
    	$ps->find();
    	if ($ps->fetch()) {
    		return $ps;
    	} else {
    		return false;
    	}
    }
    
    static function newSource($areaCode, $seqno, $name) {
    	$ps = new Popularize_source();
    	$ps->areacode = $areaCode;
    	$ps->seqno = $seqno;
		$ps->name = $name;
    	$ps->insert();
    	return $ps;
    }
    
    static function getAreacode() {
    	$ps = new Popularize_source();
    	$ps->selectAdd();
    	$ps->selectAdd('DISTINCT areacode');
    	$ps->find();
    	$result = array();
    	while ($ps->fetch()) {
    		$result[] = $ps->areacode; 
    	} 
    	
    	$ps->free();
    	return $result;
    }
    
	static function getSeqno() {
		
    	$ps = new Popularize_source();
    	$ps->selectAdd();
    	$ps->selectAdd('DISTINCT seqno');
    	$ps->find();
    	$result = array();
    	while ($ps->fetch()) {
    		$result[] = $ps->seqno; 
    	} 
    	
    	$ps->free();
    	
    	return $result;
    	
    }
}
