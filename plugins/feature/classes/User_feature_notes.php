<?php
/**
 * Table Definition for user_feature_notes
 */
require_once INSTALLDIR.'/classes/Memcached_DataObject.php';

class User_feature_notes extends Memcached_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'user_feature_notes';              // table name
    public $id;                              // int(11)  not_null primary_key auto_increment
    public $uid;                             // int(11)  not_null multiple_key
    public $clazz;                           // string(16)  not_null binary
    public $is_read;                         // int(4)  

    /* Static get */
    function staticGet($k,$v=NULL) { return Memcached_DataObject::staticGet('User_feature_notes',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
    
    static function getAvailableFeatureNotesByUserid($uid) {
    	$ufn = new User_feature_notes();
    	$ufn->whereAdd('uid = ' . $uid);
    	$ufn->whereAdd('is_read = 0');
    	$ufn->orderBy('id desc');
    	$ufn->find();
    	
    	$featureNotes = array();
    	
    	while ($ufn->fetch()) {
    		$featureNotes[] = $ufn->clazz;
    	}
    	
    	return $featureNotes;
    }
    
    static function ignoreFeatureNote($uid, $clazz) {
    	$ufn = new User_feature_notes();
    	$ufn->whereAdd('uid = ' . $uid);
    	$ufn->whereAdd("clazz = '" . $clazz . "'");
    	$ufn->whereAdd('is_read = 0');
    	
    	$ufn->find();
    	
    	if ($ufn->fetch()) {
    		$orig = clone($ufn);
    		$ufn->is_read = 1;
    		$ufn->update($orig);
    		
    		return true;
    	} else {
    		return false;
    	}
    }
}
