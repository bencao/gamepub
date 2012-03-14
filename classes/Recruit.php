<?php
/**
 * Table Definition for recruit
 */
require_once INSTALLDIR.'/classes/Memcached_DataObject.php';

class Recruit extends Memcached_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'recruit';                         // table name
    public $id;
    public $source_id;                       // int(11)  not_null
    public $fullcode;                        // string(11)  not_null primary_key
    public $uid;                             // int(11)  
    public $created;
    public $modified;

    /* Static get */
    function staticGet($k,$v=NULL) { return Memcached_DataObject::staticGet('Recruit',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
    
    static function getBySourceId($source_id) {
    	$r = new Recruit();
    	$r->whereAdd('source_id = ' . $source_id);
    	$r->find();
    	return $r;
    }
    
    static function getFullcodebySeqno($seqno, $areacode = '000', $start_time = null, $end_time = null, $offset = 0, $limit = 20 ) {
    	$recruit = new Recruit();
    	
    	$query = 'select seqno,areacode,fullcode,nickname,recruit.modified from recruit,popularize_source,user where seqno='.$seqno;
    	$query .=' and areacode='.$areacode;
    	if($start_time)
    		$query .= ' and recruit.modified > \'' . date('Y-m-d H:i:s', strtotime($start_time)) . '\'';
    	if($end_time)
    		$query .= ' and recruit.modified < \'' . date('Y-m-d H:i:s', strtotime($end_time)+3600*24) . '\'';
    	$query .= ' and recruit.source_id=popularize_source.id and user.id=recruit.uid order by recruit.modified desc limit '.$offset.', '.$limit;
    	
    	$recruit->query($query);
    	$results = array();
    
    	while($recruit->fetch())
    	{
    		$results[] = array('seqno' => $recruit->seqno,'areacode' => $recruit->areacode,'fullcode' => $recruit->fullcode,'nickname' =>$recruit->nickname,'modified'=>$recruit->modified);
    	}
    	$recruit->free();
    	return $results;
    }
    
 	static function getNumbySeqno($seqno, $areacode = '000', $start_time = null, $end_time = null) {
    	$recruit = new Recruit();
    	
    	$query = 'select count(*) AS num from recruit,popularize_source where seqno='.$seqno;
    	$query .=' and areacode='.$areacode.' and uid is not null';
    	if($start_time)
    		$query .= ' and recruit.modified > \'' . date('Y-m-d H:i:s', strtotime($start_time)) . '\'';
    	if($end_time)
    		$query .= ' and recruit.modified < \'' . date('Y-m-d H:i:s', strtotime($end_time)+3600*24) . '\'';
    	$query .= ' and recruit.source_id=popularize_source.id order by recruit.modified desc';
    	$recruit->query($query);
    	$recruit->fetch();
    	$recruit->free();
    	return $recruit->num;
    }
    
}
