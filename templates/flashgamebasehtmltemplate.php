<?php

/**
 * Shaishai, the distributed microblog
 *
 * flash game base template
 *
 * PHP version 5
 *
 * @category  FlashGame
 * @package   Shaishai
 * @author    AGun Chan <agunchan@gmail.com>
 * @copyright 2009-2010 Shaier, Inc.
 * @link      http://www.shaishai.com/
 */

if (!defined('SHAISHAI')) {
    exit(1);
}

class FlashgamebaseHTMLTemplate extends PublictwocolumnHTMLTemplate
{
	var $catnames = array('all'=>'全部', 'puzzle'=>'益智', 'act'=>'动作', 'shoot'=>'射击', 'fun'=>'搞笑', 'sport'=>'体育', 'chess'=>'棋牌');
	var $cat = 'all';
	
	function title()
    {
    	return 'GamePub小游戏';
    }
    
	function metaKeywords() 
	{
		return '小游戏，在线小游戏，GamePub小游戏';
	}
	
	function metaDescription() 
	{
		return 'GamePub小游戏是游戏酒馆游友强烈推荐的绿色、安全、免费的在线小游戏，上传小游戏，与游友一起分享快乐';
	}
	
	function showContent() 
	{
		$this->elementStart('div', 'miniwrap');
		
		//show category
		$this->elementStart('ul', 'category clearfix');
		foreach ($this->catnames as $cat=>$name) {
			$this->elementStart('li');
			$catgame_href = common_local_url('flashgame', array('cat'=>$cat));
			if ($cat === $this->cat) {
				$this->element('a', array('href'=>$catgame_href, 'class'=>'active'), $name);
			}
			else {
				$this->element('a', array('href'=>$catgame_href), $name);
			}
			$this->elementEnd('li');
//			$index++;
		}
		$this->elementEnd('ul');
		
		$this->element('a', array('class'=>'toupload button76 orange76', 'href'=>common_local_url('flashupload')), '上传游戏');
    	$this->element('a', array('class'=>'myminis button76 orange76', 'href'=>common_local_url('flashmine')), '我的游戏');
		//show mini content
		$this->showMiniContent();
		
		$this->elementEnd('div');
    }
    
    /**
     * 子类需要重写此方法
     */
    function showMiniContent() 
    {
    	
    }
    
}