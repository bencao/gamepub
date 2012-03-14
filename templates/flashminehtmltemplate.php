<?php

/**
 * Shaishai, the distributed microblog
 *
 * My flash game
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

class FlashmineHTMLTemplate extends FlashgameHTMLTemplate
{
    function title()
    {
        return '我的小游戏 ';
    }
    
    function showMiniContent() 
    {
    	$this->elementStart('dl', 'minilist');
		$this->element('dt', 'title', '我总共发布'.$this->total.'个游戏');
		$this->elementStart('dd');
		$this->showFlashList();
		$this->elementEnd('dd');
		$this->elementEnd('dl');
		$this->numpagination($this->total, 'flashmine');
    }
}