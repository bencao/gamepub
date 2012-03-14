<?php
/**
 * Table Definition for hotwords
 */
require_once INSTALLDIR.'/classes/Memcached_DataObject.php';


class Hotwords extends Memcached_DataObject
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'hotwords';                        // table name
    public $id;                              // int(100)  not_null primary_key auto_increment
    public $word;                            // string(50)  not_null binary
    public $score;
    public $created;                         // datetime(19)  not_null binary

    /* Static get */
    function staticGet($k,$v=NULL) { return Memcached_DataObject::staticGet('Hotwords',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
    
 	static function getHotWords($limit=10)
    {
    	$hotwordsin = new Hotwords();
    	$qry = "select * from hotwords WHERE created > '" . common_sql_date(time() - 24 * 3600) . "' ORDER BY score DESC LIMIT 0,". $limit;
    	$hotwordsin->query($qry);
		$hotwords = array();
		while ($hotwordsin->fetch()) {
			$hotwords[] = clone($hotwordsin);
		}
		$hotwordsin->free();
		return $hotwords;
    }
    
	static function getHotWordtexts($limit=10)
    {
    	$hotwordsin = new Hotwords();
    	$qry = "select * from hotwords WHERE created > '" . common_sql_date(time() - 24 * 3600) . "' ORDER BY score DESC LIMIT 0,". $limit;
    	$hotwordsin->query($qry);
		$hotwords = array();
		while ($hotwordsin->fetch()) {
			$hotwords[] = trim($hotwordsin->word);
		}
		$hotwordsin->free();
		return $hotwords;
    }
    
    static function getHotWord($word) {
    	$hotwordsin = new Hotwords();
    	$qry = 'select * from hotwords where word like \'' . $word . '%\' LIMIT 0,1';
 //   	common_debug($qry);
    	$hotwordsin->query($qry);
    	$hotword = null;
		if ($hotwordsin->fetch()) {
			$hotword = clone($hotwordsin);
		}
		$hotwordsin->free();
		return $hotword;
    }
}
