<?php
/**
 * People search action class.
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
 * People search action class.
 *
 * @category Action
 * @package  ShaiShai
 */
class PeoplesearchAction extends SearchAction
{
	var $isAdvance;
	
	function prepare($args)
	{
		if (! parent::prepare($args)) {return false;}
		$this->isAdvance = $this->trimmed('advance', false);
    	$this->addPassVariable('is_advance', $this->isAdvance ? true : false);
    	return true;
	}
	
	function doSearch($args)
    {
        $profile = new Profile();
        
        if ($this->isAdvance) {
			if ($this->trimmed('bynickname') || $this->trimmed('bybio') || $this->trimmed('byinterest')
				|| $this->trimmed('loc') || $this->trimmed('sex') || $this->trimmed('byno')
				|| $this->trimmed('game')) {
				
				$idProfile = new Profile();
				$keywords = common_tokenize($this->q);
				
				if ($this->trimmed('bynickname')) {
					foreach ($keywords as $k) {
						$idProfile->whereAdd("profile.nickname like '%" . addslashes($k) . "%'", 'OR');
					}
				}
				if ($this->trimmed('bybio')) {
					foreach ($keywords as $k) {
						$idProfile->whereAdd("profile.bio like '%" . addslashes($k) . "%'", 'OR');
					}
				}
				if ($this->trimmed('byinterest')) {
					$interest = new User_interest();
					$idProfile->joinAdd($interest);
					foreach ($keywords as $k) {
						$idProfile->whereAdd("user_interest.interest like '%" . addslashes($k) . "%'", 'OR');
					}
				}
				
				if ($this->trimmed('byno')) {
					foreach ($keywords as $k) {
						if (preg_match('/^\d+$/', $k)) {
							$idProfile->whereAdd("profile.qq = " . $k, 'OR');
						}
					}
				}
				
				if ($this->trimmed('game')) {
					$game = new Game();
					$idProfile->joinAdd($game);
					$idProfile->whereAdd("game.name like '%" . addslashes($this->trimmed('game')) . "%'");
				}
				
				if ($this->trimmed('loc')) {
					$idProfile->whereAdd("profile.location like '%" . addslashes($this->trimmed('loc')) . "%'");
				}
				if ($this->trimmed('sex')) {
					$idProfile->whereAdd("profile.sex = '" . addslashes($this->trimmed('sex')) . "'");
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
    	} else {
        	$search_engine = $profile->getSearchEngine('leshai_people');
	    	$search_engine->set_sort_mode('chron');
	        // Ask for an extra to see if there's more.
	        $search_engine->limit((($this->cur_page - 1)*PROFILES_PER_PAGE), PROFILES_PER_PAGE + 1);
	    	if (false === $search_engine->query($this->q)) {
	        	return false;
	        } 
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
    	return 'PeoplesearchHTMLTemplate';
    }
}
