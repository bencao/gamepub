<?php
/**
 * Shaishai, the distributed microblog
 *
 * Show video notices
 *
 * PHP version 5
 *
 * @category  Login
 * @package   Shaishai
 * @author    Guofu Xie <guofu85@gmail.com>
 * @copyright 2009-2010 Shaier, Inc.
 * @link      http://www.shaishai.com/
 */

if (!defined('SHAISHAI')) {
    exit(1);
}

/**
 * Show video notices
 *
 * @category Personal
 * @package  Shaishai
 * @author    Guofu Xie <guofu85@gmail.com>
 * @copyright 2009-2010 Shaier, Inc.
 * @link      http://www.shaishai.com/
 */


class GetvideoAction extends ShaiAction
{
	
    function __construct() {
    	parent::__construct();
		$this->no_anonymous = false;
	}

    function isReadOnly($args)
    {
        return true;
    }

    /**
     * Read and validate arguments
     *
     * @param array $args URL parameters
     *
     * @return boolean success value
     */

    function prepare($args)
    {
        if (! parent::prepare($args)) {return false;}

        common_set_returnto($this->selfUrl());

        return true;
    }

    /**
     * handle request
     *
     * Show the public stream, using recipe method showPage()
     *
     * @param array $args arguments, mostly unused
     *
     * @return void
     */

    function handle($args)
    {
        parent::handle($args);
		
        $video_id = $this->trimmed('id');
        $video = Video::staticGet('id', $video_id);
      	if (empty($video)) {
      		$video = Video::staticGet('id', $video_id);//getVideo($video_id);
      	}
        
        if (!$video) {
            $this->serverError('没有此视频.');
            return;
        }
       
        $notice = Notice::staticGet('id', $video->notice_id);
      	if (!$notice) {
            $this->serverError('没有此消息.');
            return;
        }
        $user = User::staticGet($notice->user_id);
        
        
        $offset = ($this->cur_page - 1) * NOTICES_PER_PAGE;
        $discus_list = Discussion::disListStream($notice->id, $offset);
		$totaldiss = Notice::getDissCount($notice->id);
        
		$this->addPassVariable('dis_list', $discus_list);
		$this->addPassVariable('total',$totaldiss);
			
		$this->addPassVariable('video', $video);
		$this->addPassVariable('root_notice', $notice);
		$this->addPassVariable('owner', $user);
		$this->addPassVariable('owner_profile', $user->getProfile());
		
		$this->displayWith('GetvideoHTMLTemplate');	
    }
}