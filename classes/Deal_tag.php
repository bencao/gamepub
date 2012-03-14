<?php
/**
 * Table Definition for deal_tag
 */
require_once INSTALLDIR.'/classes/Memcached_DataObject.php';

class Deal_tag extends Memcached_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'deal_tag';                        // table name
    public $id;                              // int(11)  not_null primary_key auto_increment
    public $name;                            // string(255)  
    public $game_id;                         // int(11)  not_null

    /* Static get */
    function staticGet($k,$v=NULL) { return Memcached_DataObject::staticGet('Deal_tag',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
	function getDealTagsByGameid($game_id)
    {
    	$deal_tag = new Deal_tag();
    
    	$deal_tag->whereAdd('game_id = '.$game_id);
    	
    	$deal_tag->find();
    	$tags = array();
    	while($deal_tag->fetch())
    	{
    		$tags[] = array('id'=>$deal_tag->id,'name'=>$deal_tag->name);
    	}
    	
    	$deal_tag->free();
    	return $tags;
    }
}
