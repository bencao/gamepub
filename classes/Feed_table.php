<?php
/**
 * Table Definition for feed_table
 */
require_once INSTALLDIR.'/classes/Memcached_DataObject.php';
require_once 'Zend/Feed/Reader.php';

class Feed_table extends Memcached_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'feed_table';          // table name
    public $id;                              // int(11)  not_null primary_key auto_increment
    public $user_id;                         // int(11)  not_null
    public $uri;                             // varchar(512)
    public $latest_timestamp;                // timestamp(19)  not_null unsigned zerofill binary
    public $created;                         // datetime(19)  not_null binary

    /* Static get */
    function staticGet($k,$v=NULL) { return Memcached_DataObject::staticGet('Feed_table',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
    
    static function importAll() {
		$ft = new Feed_table();
    	$ft->orderBy('id asc');
    	$ft->find();
		while ($ft->fetch()) {
			self::import($ft);
		}
    }
    
    static private function import($ft) {
    	$feed = Zend_Feed_Reader::import($ft->uri);	
		$feedStamp = $feed->getDateModified()->getTimestamp();
		$tableStamp = strtotime($ft->latest_timestamp);
		
		if ($feedStamp > $tableStamp) {
			$toImportEntries = array();
			foreach ($feed as $entry) {
			    if ($entry->getDateModified()->getTimestamp() > $tableStamp) {
			    	$toImportEntries[] = $entry;
			    }
			}
			// 逆序，原顺序是按时间逆序排列的
			$reverseArray = array_reverse($toImportEntries);
			
			foreach ($reverseArray as $iEntry) {
				$notice = Notice::saveNew($ft->user_id, 
			    			$iEntry->getTitle(), $iEntry->getLink(), 
			        		'feed', true, array('addRendered' => '<a href="' . $iEntry->getLink() . '" target="_blank">查看原文</a>', 'created' => common_sql_date($iEntry->getDateModified()->getTimestamp())));
			}
		}
		$origft = clone($ft);
		$ft->latest_timestamp = common_sql_date($feedStamp);
		$ft->update($origft);
    }
    
    static function importByUserId($user_id) {
    	$ft = new Feed_table();
    	$ft->whereAdd('user_id = ' . $user_id);
    	$ft->find();
		while ($ft->fetch()) {
			self::import($ft);
		}
    }
    
    static function getFeedByUserId($user_id) {
    	$ft = new Feed_table();
    	$ft->whereAdd('user_id = ' . $user_id);
    	$ft->find();
    	if ($ft->fetch()) {
    		return $ft;
    	} else {
    		return false;
    	}
    }
    
    static function saveNew($user_id, $uri) {
    	$ft = new Feed_table();
    	$ft->user_id = $user_id;
    	$ft->uri = $uri;
    	return $ft->insert();
    }
    
    static function updateFeed($user_id, $uri) {
    	$ft = new Feed_table();
    	$ft->whereAdd('user_id = ' . $user_id);
    	$ft->find();
    	if ($ft->fetch()) {
    		$old = clone($ft);
    		$ft->uri = $uri;
    		$ft->update($old);
    		return true;
    	} else {
    		return false;
    	}
    }
}
