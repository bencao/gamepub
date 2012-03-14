<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

/**
 * Group search action class.
 *
 * @category Action
 * @package  ShaiShai
 */
class GroupsearchAction extends SearchAction
{
    
	var $isAdvance;
	
	function prepare($args)
	{
		if (! parent::prepare($args)) {return false;}
		$this->isAdvance = $this->trimmed('advance', false);
    	$this->addPassVariable('is_advance', $this->isAdvance);
    	return true;
	}
	
	function doSearch($args)
    {
    	$user_group = new User_group();
    	
    	if ($this->isAdvance) {
    		if ($this->trimmed('bynickname') || $this->trimmed('bydescription') || $this->trimmed('bycategory')
				|| $this->trimmed('loc') || $this->trimmed('sex') || $this->trimmed('byowner')
				|| $this->trimmed('game')) {
				
				$idGroup = new User_group();
				$keywords = common_tokenize($this->q);
				
				if ($this->trimmed('bynickname')) {
					foreach ($keywords as $k) {
						$idGroup->whereAdd("user_group.nickname like '%" . addslashes($k) . "%'", 'OR');
					}
				}
				if ($this->trimmed('bydescription')) {
					foreach ($keywords as $k) {
						$idGroup->whereAdd("user_group.description like '%" . addslashes($k) . "%'", 'OR');
					}
				}
				if ($this->trimmed('bycategory')) {
					foreach ($keywords as $k) {
						$idGroup->whereAdd("user_group.category like '%" . addslashes($k) . "%'", 'OR');
					}
				}
				
				if ($this->trimmed('byowner')) {
					$user = new User();
					$idGroup->joinAdd($user);
					foreach ($keywords as $k) {
						$idGroup->whereAdd("user.nickname like '%" . addslashes($k) . "%'", 'OR');
					}
				}
				
				if ($this->trimmed('game')) {
					$game = new Game();
					$idGroup->joinAdd($game);
					$idGroup->whereAdd("game.name like '%" . addslashes($this->trimmed('game')) . "%'");
				}
				
				if ($this->trimmed('loc')) {
					$idGroup->whereAdd("user_group.location like '%" . addslashes($this->trimmed('loc')) . "%'");
				}
				if ($this->trimmed('type')) {
					if (preg_match('/^\d+$/', $this->trimmed('type'))) {
						$idGroup->whereAdd("user_group.grouptype = " . $this->trimmed('type'));
					}
				}
				
				// 排除待审核
				$idGroup->whereAdd('user_group.validity <> 0');
				
				$idGroup->selectAdd();
				$idGroup->selectAdd('distinct user_group.id as id');
				
				$idGroup->find();
				
				if ($idGroup->N == 0) {
					$user_group->whereAdd('1=0');
				} else {
					while ($idGroup->fetch()) {
						$user_group->whereAdd('id = ' . $idGroup->id, 'OR');
					}
				}
			} else {
				$user_group->whereAdd('1=0');
			}
    	} else {
	        $search_engine = $user_group->getSearchEngine('leshai_group');
	        $search_engine->set_sort_mode('chron');
	        // Ask for an extra to see if there's more.
	        $search_engine->limit((($this->cur_page-1) * PROFILES_PER_PAGE), PROFILES_PER_PAGE + 1);
	        if (false === $search_engine->query($this->q)) {
	            return false;
	        }
    	}
    	
        $this->total = $user_group->count();
        $user_group->limit((($this->cur_page-1) * PROFILES_PER_PAGE), PROFILES_PER_PAGE + 1);
        $user_group->find();
        $this->resultset = $user_group;
        
        // 保留搜索日志
		$this->srid = Search_request::saveNew(2, $this->q, $this->cur_user ? $this->cur_user->id : null);
		
		$this->addPassVariable('srid', $this->srid);
        
        return true;
    }
    
    function getViewName() {
    	return 'GroupsearchHTMLTemplate';
    }

}

