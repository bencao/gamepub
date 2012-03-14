<?php
/**
 * Shaishai, the distributed microblog
 *
 * Base class for actions that use the page owner's design
 *
 * PHP version 5
 *
 * @category  Login
 * @package   Shaishai
 * @author    Andray Ma <andray09@gmail.com>
 * @copyright 2009-2010 Shaier, Inc.
 * @link      http://www.shaishai.com/
 */

if (!defined('SHAISHAI')) {
    exit(1);
}

/**
 * Base class for actions that use the page owner's design
 *
 * @category Personal
 * @package  Shaishai
 * @author   Andray Ma <andray09@gmail.com>
 * we don't have owner design currently, so code in this class is commented
 * @copyright 2009-2010 Shaier, Inc.
 * @link      http://www.shaishai.com/
 */

class OwnerDesignAction extends ShaiAction
{
	var $owner;
	
	var $owner_game;
	
	var $owner_game_server;
	
	var $owner_design;
	
	var $is_own;
	
	function prepare($args) {
		if (! parent::prepare($args)) {return false;}
		$nickname = $this->trimmed('uname');
		if ($nickname) {
			$this->owner = User::staticGet('uname', $nickname);
			if (! $this->owner) {
				$this->clientError('访问的用户不存在');
				return;
			}
		} else {
			$this->owner = $this->cur_user;
		}
    	
	    $this->owner_game = Game::staticGet('id' , $this->owner->game_id);
        $this->owner_game_server = Game_server::staticGet('id', $this->owner->game_server_id);
	    
//        if (! $this->is_anonymous) {
//        	// 定义“群组”的个性化名称
//        	SET_GROUP_NAME($this->owner_game->game_group_name);
//        }
        
        $this->is_own = ! $this->is_anonymous && $this->owner->id == $this->cur_user->id;
    	
		$this->owner_design = $this->owner->getDesign();
		return true;
	}
	
	function handle($args) {
		parent::handle($args);
		$this->addPassVariable('owner', $this->owner);
		$this->addPassVariable('owner_design', $this->owner_design);
		$this->addPassVariable('owner_game', $this->owner_game);
        $this->addPassVariable('owner_game_server', $this->owner_game_server);
        $this->addPassVariable('is_own', $this->is_own);
	}
	
}