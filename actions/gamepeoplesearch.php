<?php
/**
 * Game people search action class.
 *
 * PHP version 5
 *
 * @category Action
 * @package  ShaiShai
 */

if (!defined('SHAISHAI')) {
    exit(1);
}

/**
 * Game people search action class.
 *
 * @category Action
 * @package  ShaiShai
 */
class GamepeoplesearchAction extends SearchAction
{
	var $isAdvance;
	
	function prepare($args)
	{
		if (! parent::prepare($args)) {return false;}
    	return true;
	}
	
	function doSearch($args)
    {
        $profile = new Profile();
        
		if ($this->trimmed('sex') || $this->trimmed('agebegin') || $this->trimmed('ageend')
			|| $this->trimmed('loc') || $this->trimmed('game') || $this->trimmed('zone')
			|| $this->trimmed('server')) {
			
			$idProfile = new Profile();
			$keywords = common_tokenize($this->q);

			if ($this->trimmed('loc')) {
				$idProfile->whereAdd("profile.location like '%" . addslashes($this->trimmed('loc')) . "%'");
			}
			if ($this->trimmed('sex')) {
				$idProfile->whereAdd("profile.sex = '" . addslashes($this->trimmed('sex')) . "'");
			}
			//怎么判断年龄?
//			if ($this->trimmed('agebegin') && $this->trimmed('ageend')) {
//				$idProfile->whereAdd("profile.sex = '" . addslashes($this->trimmed('sex')) . "'");
//			}
			if ($this->trimmed('game')) {
				$game = new Game();
				$idProfile->joinAdd($game);
				$idProfile->whereAdd("game.name like '%" . addslashes($this->trimmed('game')) . "%'");
			}			
			if ($this->trimmed('zone')) {
				$game_big_zone = new Game_big_zone();
				$idProfile->joinAdd($game_big_zone);
				$idProfile->whereAdd("game_big_zone.name like '%" . addslashes($this->trimmed('zone')) . "%'");
			}
			if ($this->trimmed('server')) {
				$game_server = new Game_server();
				$idProfile->joinAdd($game_server);
				$idProfile->whereAdd("game_server.name like '%" . addslashes($this->trimmed('server')) . "%'");
			}
			
			$idProfile->selectAdd();
			$idProfile->selectAdd('distinct profile.id as id');
			
			$idProfile->find();
			
			if ($idProfile->N == 0) {
				$profile->whereAdd('1=0');
			} else {
				while ($idProfile->fetch()) {
					$profile->whereAdd('id = ' . $idProfile->id, 'OR');
				}
			}
		} else {
			$profile->whereAdd('1=0');
		}
    	
        $this->total = $profile->count();
        $profile->find();
        $this->resultset = $profile;
        
        // 保留搜索日志
		$this->srid = Search_request::saveNew(1, $this->q, $this->cur_user ? $this->cur_user->id : null);
		
		$this->addPassVariable('srid', $this->srid);
		
        return true;
    }
    
    function getViewName() {
    	return 'GamefriendsHTMLTemplate';
    }
}
