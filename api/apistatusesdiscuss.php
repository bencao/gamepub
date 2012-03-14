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

class ApiStatusesDiscussAction extends ApiAuthAction
{
	var $source                = null;
    var $status                = null;
    var $in_discuss_to_status_id = null;
    
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
        
    	$discussion = Discussion::saveNewDis($this->in_discuss_to_status_id, $this->user->id, $this->status, null, $this->source);
	        
		$orig = clone($this->root_notice);
		$this->root_notice->discussion_num ++;
		$this->root_notice->update($orig);
		
		$this->showNotice();
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
        
//    	$simple_content = common_filter_huoxing($this->status);
//		if (common_banwordCheck($simple_content)) {
//        	$this->clientError('不要发布政治、反动、反社会言论，请重新修改您的内容。(恶意发送将会禁言，删除帐号)', 400, $this->format);
//        	return;
//    	}
    	
        $this->source = $this->trimmed('source');

        if (empty($this->source) || in_array($this->source, self::$reserved_sources)) {
            $this->source = 'api';
        }

        $this->in_discuss_to_status_id
            = intval($this->trimmed('in_discuss_to_status_id'));
            
    	$this->root_notice = Notice::staticGet('id', $this->in_discuss_to_status_id);
        
        if (! $this->root_notice) {
        	$this->clientError('回复的消息不存在', 400, $this->format);
            return;
        }
        		
    }
    
/**
     * Show the resulting notice
     *
     * @return void
     */

    function showNotice()
    {
        if (!empty($this->root_notice)) {
            if ($this->format == 'xml') {
                $this->showSingleXmlStatus($this->root_notice);
            } elseif ($this->format == 'json') {
                $this->show_single_json_status($this->root_notice);
            }
        }
    }
}