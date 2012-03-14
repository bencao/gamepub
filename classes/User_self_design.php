<?php
/**
 * Table Definition for user_self_design
 */
require_once INSTALLDIR.'/classes/Memcached_DataObject.php';

class User_self_design extends Memcached_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'user_self_design';                // table name
    public $design_id;                       // int(11)  
    public $user_id;                         // int(11)  multiple_key

    /* Static get */
    function staticGet($k,$v=NULL) { return Memcached_DataObject::staticGet('User_self_design',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
    
    function deleteDesign($user_id, $design_id) {
    	$usd = new User_self_design();
    	$usd->design_id = $design_id;
    	$usd->user_id = $user_id;
    	$usd->query('BEGIN');
    	
    	if (! $usd->delete()) {
    		$usd->query('ROLLBACK');
    		return false;
    	}
    	
    	$design = new Design();
    	$design->id = $design_id;
    	if (! $design->delete()) {
    		$usd->query('ROLLBACK');
    		return false;
    	}
    	
    	$user = User::staticGet('id', $user_id);
    	$game = Game::staticGet('id', $user->game_id);
    	
    	if ($user->design_id == $design_id) {
    		$orig = clone($user);
    		$user->design_id = $game->design_id;
    		$user->update($orig);
    	}
    	
    	$usd->query('COMMIT');
    	
    	return true;
    }
    
    function _getDesignIdsByUserId($user_id) {
    	$usd = new User_self_design();
    	$usd->whereAdd('user_id = ' . $user_id);
    	$usd->find();
    	
    	$ids = array();
    	while ($usd->fetch()) {
    		$ids[] = $usd->design_id;
    	}
    	$usd->free();
    	
    	return $ids;
    }
    
    function getDesignsByUser($user) {
    	$d = new Design();
    	$d->whereAdd('id in (' . implode(',', self::_getDesignIdsByUserId($user->id)) . ')');
    	
    	$d->find();
    	
    	return $d;
    }
    
    function saveNew($user, $design) {
    	$usd = new User_self_design();
	    $usd->design_id = $design->id;
	    $usd->user_id = $user->id;
	    $result = $usd->insert();
	    
	    if (! $result) {
	    	 common_log_db_error($usd, 'INSERT', __FILE__);
	    	 return false;
	    }
	    return true;
	    
    }
}
