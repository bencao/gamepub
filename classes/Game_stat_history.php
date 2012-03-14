<?php
/**
 * Table Definition for game_stat_history
 */
require_once INSTALLDIR.'/classes/Memcached_DataObject.php';

class Game_stat_history extends Memcached_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'game_stat_history';               // table name
    public $game_id;                         // int(11)  not_null primary_key
    public $his_date;                        // date(10)  not_null primary_key binary
    public $video_num;                       // int(11)  
    public $music_num;                       // int(11)  
    public $pic_num;                         // int(11)  
    public $text_num;                        // int(11)  
    public $user_num;                        // int(11)  

    /* Static get */
    function staticGet($k,$v=NULL) { return Memcached_DataObject::staticGet('Game_stat_history',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
    
    function stathisGetbyidtime($game_id,$day)
    {
    	$his = new Game_stat_history();
    	$his->whereAdd("game_id=".$game_id);
    	$his->whereAdd("his_date='".$day."'");
    	$his->find();
    	$his->fetch();
    	return $his->game_id?$his:"0";
    }
}
