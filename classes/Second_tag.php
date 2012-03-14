<?php
/**
 * Table Definition for second_tag
 */
require_once INSTALLDIR.'/classes/Memcached_DataObject.php';

class Second_tag extends Memcached_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'second_tag';                      // table name
    public $id;                              // int(11)  not_null primary_key auto_increment
    public $name;                            // string(255)  multiple_key
    public $first_tag_id;                    // int(11)  not_null
    public $game_id;                         // int(11)  not_null

    /* Static get */
    function staticGet($k,$v=NULL) { return Memcached_DataObject::staticGet('Second_tag',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
    
    static function getSecondTagByFirstTag($first_tag_id, $game_id)
    {
    	$qry =
          'SELECT id, name ' .
          'FROM second_tag  where first_tag_id = ' . $first_tag_id . ' and game_id = ' . $game_id . ' ORDER BY id ASC ';

        $st = new Second_tag();
        $cnt = $st->query($qry);
        
        $sts = array();
        
        while ($st->fetch()) {
            $sts[$st->id] = $st->name;
        }
        $st->free();
        unset($st);
        return $sts;
    }    
    
	function getGameTagsStruct($game_id)
    {
    	return common_stream('secondtag:tagstruct:' . $game_id, array("Second_tag", "_getGameTagsStruct"), array($game_id), 24 * 3600);
    } 
    
    function _getGameTagsStruct($game_id)
    {
	    $fts = First_tag::getFirstTags($game_id);
    	$first_tag = array();
    	foreach ($fts as $id => $name) {
    		if($name != '自定义') {
    			$second_tag = Second_tag::getSecondTagByFirstTag($id, $game_id);	    			
    			$first_tag[$id] = $second_tag;
    		}
    	}
    	return $first_tag;
    }
}
