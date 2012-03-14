<?php
/**
 * Shaishai, the distributed microblog
 *
 * Show all notices of a game
 *
 * PHP version 5
 *
 * @category  Game
 * @package   Shaishai
 * @author    Guofu Xie <guofu85@gmail.com>
 * @copyright 2009-2010 Shaier, Inc.
 * @link      http://www.shaishai.com/
 */

if (!defined('SHAISHAI')) {
    exit(1);
}

/**
 * Show all notices of a game
 *
 * @category Game
 * @package  Shaishai
 * @author    Guofu Xie <guofu85@gmail.com>
 * @copyright 2009-2010 Shaier, Inc.
 * @link      http://www.shaishai.com/
 */

class GameAction extends ShaiAction
{ 
	function __construct() {
		parent::__construct();
		$this->no_anonymous = false;
	}
	
    function prepare($args)
    {
        if (! parent::prepare($args)) {return false;}        
        
        common_set_returnto($this->selfUrl());
        
        return true;
    }
    
    function isReadOnly($args)
    {
        return true;
    }

    function handle($args)
    {
        parent::handle($args);
        
        $this->_validate();
		
		$game_id = $this->trimmed('gameid');
		
		$tag = $this->trimmed('tag');
		$since_id = $this->trimmed('since_id');
		$game = Game::staticGet('id', $game_id);
		if(!$game) {
			$this->clientError('目前没有此游戏.', 403);
	        return;
		}
		$second_tag = $this->trimmed('second_tag');

		$filter_content = $this->trimmed('filter_content');
        $filter_content_flag = 0;
       	if($filter_content) {
       		$filter_content_flag = 1;
        	if($filter_content == 5) 
    			$filter_content = 0;
       	}
		
       	if($since_id) {
       		if($filter_content_flag && $second_tag) {
	    		 $notice = Notice::getTaggedNotices(($this->cur_page-1)*NOTICES_PER_PAGE, NOTICES_PER_PAGE + 1,
	     	  			$since_id, 0, null, $filter_content, 0, $game_id, 0, $second_tag);	
	     	} else if($filter_content_flag) {
	    		 $notice = Notice::getTaggedNotices(($this->cur_page-1)*NOTICES_PER_PAGE, NOTICES_PER_PAGE + 1,
	     	  			$since_id, 0, null, $filter_content, 0, $game_id, 0);	
	     	} else if($tag) {
	     		$notice = Notice::getTaggedNotices(($this->cur_page-1)*NOTICES_PER_PAGE, NOTICES_PER_PAGE + 1, $since_id, 0,
	        			null, 0, $tag, $game_id, 0);
	     	} else if($second_tag) {
	     		$notice = Notice::getTaggedNotices(($this->cur_page-1)*NOTICES_PER_PAGE, NOTICES_PER_PAGE + 1, $since_id, 0,
	        			null, 0, 0, $game_id, 0, $second_tag);
	     	} else {
	     	  	$notice = Notice::getTaggedNotices(($this->cur_page-1)*NOTICES_PER_PAGE, NOTICES_PER_PAGE + 1, $since_id, 0,
	     	  			null, 0, 0, $game_id, 0);
	     	}
       	} else {
       		if($filter_content_flag && $second_tag) {
	    		 $notice = Notice::getTaggedNotices(($this->cur_page-1)*NOTICES_PER_PAGE, NOTICES_PER_PAGE + 1,
	     	  			0, 0, null, $filter_content, 0, $game_id, 0, $second_tag);	
	     	} else if($filter_content_flag) {
	    		 $notice = Notice::getTaggedNotices(($this->cur_page-1)*NOTICES_PER_PAGE, NOTICES_PER_PAGE + 1,
	     	  			0, 0, null, $filter_content, 0, $game_id, 0);	
	     	} else if($tag) {
	     		$notice = Notice::getTaggedNotices(($this->cur_page-1)*NOTICES_PER_PAGE, NOTICES_PER_PAGE + 1, 0, 0,
	        			null, 0, $tag, $game_id, 0);
	     	} else if($second_tag) {
	     		$notice = Notice::getTaggedNotices(($this->cur_page-1)*NOTICES_PER_PAGE, NOTICES_PER_PAGE + 1, 0, 0,
	        			null, 0, 0, $game_id, 0, $second_tag);
	     	} else {
	     	  	$notice = Notice::getTaggedNotices(($this->cur_page-1)*NOTICES_PER_PAGE, NOTICES_PER_PAGE + 1, 0, 0,
	     	  			null, 0, 0, $game_id, 0);
	     	}
       	}
     	
		if($filter_content_flag) {
       		if($filter_content == 0) 
    			$filter_content = 5;
		}
		
		$game_hot_profileIds = common_random_fetch(Game::getActiveTop50($game->id), 20);
		$game_hot_profiles = Profile::getProfileByIds($game_hot_profileIds);
		
		
		if ($this->cur_user) {
        	$game_hot_groups = User_group::getGameGroupsByGameHottest($game_id, 0, 5);
		} else {
			$game_hot_groups = null;
		}
		
		$this->addPassVariable('user', $this->cur_user);
    	$this->addPassVariable('notice', $notice);
		$this->addPassVariable('filter_content_flag', $filter_content_flag);
    	$this->addPassVariable('filter_content', $filter_content);
    	$this->addPassVariable('tag', $tag);
    	$this->addPassVariable('game', $game);
    	$this->addPassVariable('second_tag', $second_tag);
    	
    	$this->addPassVariable('hotgroups', $game_hot_groups);
    	$this->addPassVariable('hotprofiles', $game_hot_profiles);
    	
        if ($this->boolean('ajax')) {
        	if($this->cur_page > 1) {
	    		$all_view = TemplateFactory::get('GameHTMLTemplate');
				$all_view->ajaxShowPageNotices($this->paras);     		
	    	}  else if($since_id) {
				$all_view = TemplateFactory::get('GameHTMLTemplate');
				$all_view->ajaxShowNoticeSinceId($this->paras); 
	    	}  
        } else {        	
        	//此游戏中最新的消息id, 需要改动这个
        	//$latest_notice_id = $this->user->latestNoticeId();
        	//$this->addPassVariable('latest_notice_id', $latest_notice_id);
    		$this->displayWith('GameHTMLTemplate');    		
		} 
    }
    
    function _validate()
    {
        if($this->trimmed('since_id') && !is_numeric($this->trimmed('since_id'))) {
            $this->clientError('您不能访问此链接.', 403);
            return;
        }
        
		if($this->trimmed('filter_content') 
			&& !is_numeric($this->trimmed('filter_content'))) {
            $this->clientError('您不能访问此链接.', 403);
            return;
        }
        
    	if($this->trimmed('tag') && !is_numeric($this->trimmed('tag'))) {
            $this->clientError('您不能访问此链接.', 403);
            return;
        }
        
    	if($this->trimmed('gameid') && !is_numeric($this->trimmed('gameid'))) {
            $this->clientError('您不能访问此链接.', 403);
            return;
        }
		
    }
}