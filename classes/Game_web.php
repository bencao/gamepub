<?php
/**
 * Table Definition for game_web
 */
require_once INSTALLDIR.'/classes/Memcached_DataObject.php';

class Game_web extends Memcached_DataObject  
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'game_web';                        // table name
    public $id;                              // int(11)  not_null primary_key auto_increment
    public $name;                            // string(255)  not_null
    public $website;                         // string(255)  not_null
    public $detail;                          // string(255)  not_null
    public $game_id;                         // int(11)  not_null
    public $user_id;                         // int(11)  not_null
    public $created;                         // datetime(19)  not_null binary
    public $modified;                        // timestamp(19)  not_null unsigned zerofill binary timestamp
    public $click_count;                     // int(11)  not_null
    public $is_valid;                        // int(4)  not_null

    /* Static get */
    function staticGet($k,$v=NULL) { return Memcached_DataObject::staticGet('Game_web',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
    
	function getGameWebs($gameid, $offset=0, $limit=20) {
    	$gameweb_ids = common_stream('game_web:getGameWebs:' . $gameid, array('Game_web', '_getGameWebs'), array($gameid, $offset, $limit), 3600 * 24);
    	$gameweb = new self();
    	$gameweb->whereAdd('id in (' . implode(',',$gameweb_ids). ')');
		$gameweb->orderBy('click_count DESC');
        $gameweb->find();
        return $gameweb;
    }
    
    function _getGameWebs($game_id, $offset, $limit)
    {
    	$gameweb = new self();
    	$gameweb->whereAdd('is_valid = 1');
    	$gameweb->whereAdd('game_id=' . $game_id);
    	$gameweb->orderBy('click_count DESC');
    	$gameweb->limit($offset, $limit);
    	$gameweb->find();
    	$game_webs = array();
		while ($gameweb->fetch()) {
			$game_webs[] = $gameweb->id;
		}
		$gameweb->free();
		return $game_webs;
    }
    
    function getGameWebTotal($game_id)
    {
    	$gameweb = new self();
    	$gameweb->whereAdd('is_valid = 1');
    	$gameweb->whereAdd('game_id=' . $game_id);
        return $gameweb->count();
    }
    
    function saveNew($name, $website, $detail, $game_id, $user_id='100000')
    {
		$gameweb = new self();
		$gameweb->name = $name;
		$gameweb->website = $website;
		$gameweb->detail = $detail;
		$gameweb->created = common_sql_now();
		$gameweb->game_id = $game_id;
		$gameweb->user_id = $user_id;
		$result = $gameweb->insert();
		
		if (! $result) {
			common_log_db_error($gameweb, 'INSERT GAME_WEB');
    		return '申请站点失败，请稍后再试';
		}
	    return null;
    }
    
    function increaseClickById($gameweb_id)
    {
    	$gameweb = self::staticGet('id', $gameweb_id);
    	if ($gameweb) {
    		$orig = clone($gameweb);
    		$gameweb->click_count++;
    		$gameweb->update($orig);
    		return true;
    	}
    	return false;
    }
}
