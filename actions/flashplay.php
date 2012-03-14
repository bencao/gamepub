<?php
/**
 * Shaishai, the distributed microblog
 *
 * Play flash game
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

class FlashplayAction extends ShaiAction
{
	
    function __construct() {
    	parent::__construct();
		$this->no_anonymous = false;
	}

    function isReadOnly($args)
    {
        return true;
    }

    function handle($args)
    {
        parent::handle($args);
		
        $flash_id = $this->trimmed('id');
        $flash = Flash::staticGet('id', $flash_id);

        if (!$flash) {
            $this->serverError('没有此游戏.');
            return;
        }
       
        $notice = Notice::staticGet('id', $flash->notice_id);
      	if (!$notice) {
            $this->serverError('没有此消息.');
            return;
        }
        
        $flash->increaseClick();
        
        $offset = ($this->cur_page - 1) * NOTICES_PER_PAGE;
        $discus_list = Discussion::disListStream($notice->id, $offset);
		$totaldis = Notice::getDissCount($notice->id);
        
		$this->addPassVariable('root_notice', $notice);
		$this->addPassVariable('dis_list', $discus_list);
		$this->addPassVariable('total_dis',$totaldis);
		$this->addPassVariable('flash', $flash);
		$this->addPassVariable('owner_profile', Profile::staticGet($flash->user_id));
		$this->addPassVariable('hot_flash', Flash::getHottestFlash($flash->type, 0, 5));
		
		$this->displayWith('FlashplayHTMLTemplate');	
    }
}