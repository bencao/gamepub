<?php
/**
 * Table Definition for game_big_zone
 */
require_once INSTALLDIR.'/classes/Memcached_DataObject.php';

class Game_big_zone extends Memcached_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'game_big_zone';                   // table name
    public $id;                              // int(11)  not_null primary_key auto_increment
    public $name;                            // string(255)  multiple_key
    public $game_id;                         // int(11)  not_null

    /* Static get */
    function staticGet($k,$v=NULL) { return Memcached_DataObject::staticGet('Game_big_zone',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
    
    function getGame() {
    	return Game::staticGet('id', $this->game_id);
    }
}
