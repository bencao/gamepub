<?php
/**
 * Table Definition for missions
 */
require_once INSTALLDIR.'/classes/Memcached_DataObject.php';

class Missions extends Memcached_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'missions';                        // table name
    public $id;                              // int(11)  not_null primary_key
    public $uid;                             // int(11)  not_null multiple_key
    public $mission_clazz;                   // string(64)  not_null binary
    public $status;                          
    public $modified;                        // datetime(19)  not_null binary
    public $created;                         // datetime(19)  not_null binary

    /* Static get */
    function staticGet($k,$v=NULL) { return Memcached_DataObject::staticGet('Missions',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
    
    static function getStatus($uid, $mission_clazz) {
    	$m = new Missions();
    	$m->whereAdd('uid = ' . $uid);
    	$m->whereAdd("mission_clazz = '" . $mission_clazz . "'");
    	$m->find();
    	
    	if ($m->fetch()) {
    		return $m->status;
    	} else {
    		return false;
    	}
    }
    
    static function updateStatus($uid, $mission_clazz, $status) {
    	$m = new Missions();
    	$m->whereAdd('uid = ' . $uid);
    	$m->whereAdd("mission_clazz = '" . $mission_clazz . "'");
    	$m->find();
    	
    	$m->fetch();
    	
    	$id = $m->id;
    	$m->query('UPDATE missions SET status = ' . $status . ' WHERE id = ' . $id);
    	
//    	$orig = clone($m);
//    	$m->status = $status;
//    	$m->update($orig);
    }
    
    static function getMissionsCountByUserid($uid) {
    	$m = new Missions();
    	$m->whereAdd('uid = ' . $uid);
    	return $m->count();
    }
    
    static function getFirstNotFinishedIndex($uid) {
    	$m = new Missions();
//    	$m->selectAdd();
//    	$m->selectAdd('id');
    	$m->whereAdd('uid = ' . $uid);
    	$m->orderBy('id asc');
    	$m->find();
    	
    	$index = 0;
    	while ($m->fetch()) {
    		if ($m->status != 2) {
    			break;
    		}
    		$index ++;
    	}
    	
    	return $index;
    }
    
    static function getAllMissionsByUserid($uid, $offset=0, $limit=false) {
    	$m = new Missions();
    	$m->whereAdd('uid = ' . $uid);
    	$m->orderBy('id asc');
    	if ($limit) {
    		$m->limit($offset, $limit);
    	}
    	$m->find();
    	
    	return $m;
    }
    
	static function getStartedMissionsByUserid($uid) {
    	$m = new Missions();
    	$m->whereAdd('uid = ' . $uid);
    	$m->whereAdd('status = 1');
    	$m->orderBy('id asc');
    	$m->find();
    	
    	return $m;
    }
    
	static function getNewMissionsByUserid($uid) {
    	$m = new Missions();
    	$m->whereAdd('uid = ' . $uid);
    	$m->whereAdd('status = 0');
    	$m->orderBy('id asc');
    	$m->find();
    	
    	return $m;
    }
    
	static function getFinishedMissionsByUserid($uid) {
    	$m = new Missions();
    	$m->whereAdd('uid = ' . $uid);
    	$m->whereAdd('status = 2');
    	$m->orderBy('id asc');
    	$m->find();
    	
    	return $m;
    }
}
