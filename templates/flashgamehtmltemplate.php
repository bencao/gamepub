<?php

/**
 * Shaishai, the distributed microblog
 *
 * All flash game
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

class FlashgameHTMLTemplate extends FlashgamebaseHTMLTemplate
{
	var $which;
	var $total;
	var $subs;
	var $catName = array(1 => '益智', 2 => '动作', 3 => '射击', 4 => '搞笑', 5 => '体育', 6 => '棋牌');
	var $catEname = array(1 => 'puzzle', 2 => 'act', 3 => 'shoot', 4 => 'fun', 5 => 'sport', 6 => 'chess');
	
    function show($args = array()) 
    {
    	$this->cat = $args['cat'];
    	$this->which = $args['which'];
    	$this->total = $args['total'];
    	$this->subs = $args['subs'];
    	parent::show($args);
    }
    
    function showMiniContent() 
    {
		$this->elementStart('dl', 'minilist');
		$this->elementStart('dt');
		$flashgame_href = common_local_url('flashgame', array('cat'=>$this->cat));
		if ($this->which === "latest") {
			$this->element('a', array('href'=>$flashgame_href), '最多人玩');
			$this->element('a', array('href'=>$flashgame_href.'?which=latest', 'class'=>'active'), '最新发布');
		} else {
			$this->element('a', array('href'=>$flashgame_href, 'class'=>'active'), '最多人玩');
			$this->element('a', array('href'=>$flashgame_href.'?which=latest'), '最新发布');
		}
		$this->elementEnd('dt');
		$this->elementStart('dd');
		
		$this->showFlashList();
		
		$this->elementEnd('dd');
		$this->elementEnd('dl');
		$this->numpagination($this->total, 'flashgame', array('cat'=>$this->cat));
    }
    
    function showFlashList()
    {
    	//show this subs
    	$this->elementStart('ol');
    	while ($this->subs && $this->subs->fetch()) {
    		$this->elementStart('li');
    		$this->elementStart('div', 'avatar');
    		$this->elementStart('a', array('href' => common_local_url('flashplay', array('id' => $this->subs->id))));
    		$this->element('img', array('src' => $this->subs->picpath, 'alt' => $this->subs->title));
    		$this->elementEnd('a');
    		$this->elementEnd('div');
    		$this->elementStart('p', 'nickname');
    		$this->element('a', array('href' => common_local_url('flashplay', array('id' => $this->subs->id))), $this->subs->title);
    		$this->elementEnd('p');
    		$this->element('p', null, '简介：' . $this->subs->introduction);
    		$this->elementStart('p', 'misc');
	    	$rating = $this->subs->getRating();
	    	$this->elementStart('span', array('class' => 'rating', 'title' => $rating . '/100'));
	    	$this->text('人气：');
	    	$this->elementStart('em');
	    	$this->element('b', array('style' => 'width:' . $rating . '%;'));
	    	$this->elementEnd('em');
	    	$this->elementEnd('span');
	    	$this->text('类别：');
	    	$this->element('a', array('href' => common_local_url('flashgame', array('cat' => $this->catEname[$this->subs->type]))), $this->catName[$this->subs->type]);
	    	$this->elementEnd('p');
    		$this->elementEnd('li');
    	}
    	$this->elementEnd('ol');
    }
}