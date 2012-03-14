<?php
/**
 * ShaiShai, the distributed microblogging tool
 *
 * Post a notice (update your status) through the API
 *
 * @category  API
 * @package   Shaishai
 * @author    Guofu Xie <guofu85@gmail.com>
 * @copyright 2009-2010 Shaier, Inc.
 * @link      http://www.shaishai.com/
 */

if (!defined('SHAISHAI')) {
    exit(1);
}

require_once INSTALLDIR . '/lib/apiauth.php';

/**
 * Updates the authenticating user's status (posts a notice).
 *
 * @category  API
 * @package   Shaishai
 * @author    Guofu Xie <guofu85@gmail.com>
 * @copyright 2009-2010 Shaier, Inc.
 * @link      http://www.shaishai.com/
 */

class ApiStatusesRetweetAction extends ApiAuthAction
{
	var $source                = null;
    var $status                = null;
    var $in_retweet_from_status_id = null;
    
    var $root_notice = null;

    static $reserved_sources = array('web', 'omb', 'mail', 'xmpp', 'api');

    /**
     * Take arguments for running
     *
     * @param array $args $_REQUEST args
     *
     * @return boolean success flag
     *
     */

    function prepare($args)
    {
        if (! parent::prepare($args)) {return false;}

        $this->user = $this->auth_user;
        $this->preHandle();        

        return true;
    }
    
     function handle($args)
    {
        parent::handle($args);
        
        $this->user->query('BEGIN');
        
        $retweeted = Notice::staticGet('id', $this->in_retweet_from_status_id);
    	$temp = clone($retweeted);
    	$retweeted->retweet_num ++;
    	$retweeted->update($temp);
    	
    	Notice_heat::addHeat($this->in_retweet_from_status_id,3);
    	User_grade::addScore($retweeted->user_id, 2);
    	
    	$notice = Notice::saveNew($this->user->id, $this->status, null, 
        							$this->source, 1, array('retweet_from' => $this->in_retweet_from_status_id));
    			
        if (is_string($notice)) {
            $this->clientError($notice, 400, $this->format);
            return;
        }
    	
    	Notice::addRetweetToInboxes($this->user->id, $this->in_retweet_from_status_id, $notice->created);
   	 	
    	if($this->trimmed('discuss') == 'true'){
    		//$retweeted = Notice::staticGet('id',$this->in_retweet_from_status_id);
    		$temp = clone($retweeted);
    		$retweeted->discussion_num ++;
    		$retweeted->update($temp);
        	$discussion = Discussion::saveNewDis($this->in_retweet_from_status_id, $this->user->id, $this->status, null, $this->source);
    	}    	
    	
    	$this->user->query('COMMIT');
    	
		$this->showNotice($notice);
    }
    
    function preHandle() 
    {
    	$this->status = $this->trimmed('status');
        
    	if (empty($this->status)) {
            $this->clientError(
                '客户端应当提供 参数\'status\'的值.',
                400,
                $this->format
            );
            return false;
        }
        $this->status = common_shorten_links($this->status);
        if (mb_strlen($this->status, 'utf-8') > 280) {
            $this->clientError('此消息太长, 最大长度为280个字.', 400, $this->format);
            return;
        }	

//        $simple_content = common_filter_huoxing($this->status);
//		if (common_banwordCheck($simple_content)) {
//        	$this->clientError('不要发布政治、反动、反社会言论，请重新修改您的内容。(恶意发送将会禁言，删除帐号)', 400, $this->format);
//        	return;
//    	}
        
        $this->source = $this->trimmed('source');

        if (empty($this->source) || in_array($this->source, self::$reserved_sources)) {
            $this->source = 'api';
        }

        $this->in_retweet_from_status_id = $this->trimmed('in_retweet_from_status_id');
        
    }
    
/**
     * Show the resulting notice
     *
     * @return void
     */

    function showNotice($notice)
    {
        if (!empty($notice)) {
            if ($this->format == 'xml') {
                $this->showSingleXmlStatus($notice);
            } elseif ($this->format == 'json') {
                $this->show_single_json_status($notice);
            }
        }
    }
}