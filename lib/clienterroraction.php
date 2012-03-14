<?php
/**
 * Shaishai, the distributed microblog
 *
 * Client error action
 *
 * PHP version 5
 *
 * @category  Login
 * @package   Shaishai
 * @author    Guofu Xie <guofu85@gmail.com>
 * @author    Ben Cao <benb88@gmail.com>
 * @copyright 2009-2010 Shaier, Inc.
 * @link      http://www.shaishai.com/
 */

if (!defined('SHAISHAI')) {
    exit(1);
}

/**
 * Client error action
 *
 * @category Personal
 * @package  Shaishai
 * @author    Guofu Xie <guofu85@gmail.com>
 * @copyright 2009-2010 Shaier, Inc.
 * @link      http://www.shaishai.com/
 */

class ClientErrorAction extends ShaiAction
{
	var $code    = null;
    var $message = null;
//    var $status  = null;
    var $default = null;
    
     static $status  = array(400 => 'Bad Request',
                               401 => 'Unauthorized',
                               402 => 'Payment Required',
                               403 => 'Forbidden',
                               404 => 'Not Found',
                               405 => 'Method Not Allowed',
                               406 => 'Not Acceptable',
                               407 => 'Proxy Authentication Required',
                               408 => 'Request Timeout',
                               409 => 'Conflict',
                               410 => 'Gone',
                               411 => 'Length Required',
                               412 => 'Precondition Failed',
                               413 => 'Request Entity Too Large',
                               414 => 'Request-URI Too Long',
                               415 => 'Unsupported Media Type',
                               416 => 'Requested Range Not Satisfiable',
                               417 => 'Expectation Failed');
    
	function __construct($message='Error', $code=400)
    {
    	$this->code = $code;
        $this->message = $message;
        $this->no_anonymous = false;
        $this->cache_allowed = false;

        // XXX: hack alert: usually we aren't going to
        // call this page directly, but because it's
        // an action it needs an args array anyway
//        $this->prepare($_REQUEST);
        $this->args =& common_copy_args($_REQUEST);
        
        $this->cur_user = common_current_user();
        
        $this->is_anonymous = empty($this->cur_user);
        
        $this->cur_page = ($this->arg('page')) ? ($this->arg('page')+0) : 1;
        
        if ($this->is_anonymous) {
        	// 默认显示为群组
        	SET_GROUP_NAME('群组');
        	SET_JOB_NAME('职业');
        } else {
        	$this->cur_game = Game::staticGet('id', $this->cur_user->game_id);
        	SET_GROUP_NAME($this->cur_game->game_group_name);
        	SET_JOB_NAME($this->cur_game->game_job_name);
        }
        
    	if ($_REQUEST != null) {
        	$this->paras = array_merge($_REQUEST);
        } else {
        	$this->paras = array();
        }
        
        $this->default = 400;
    }
    
    // XXX: Should these error actions even be invokable via URI?

    function handle($args=null)
    {
        parent::handle($args);

//        $this->code = $this->trimmed('code');

        if (!$this->code || $this->code < 400 || $this->code > 499) {
            $this->code = $this->default;
        }

       // $this->message = $this->trimmed('message');

        if (!$this->message) {
            $this->message = "客户端错误" . $this->code;
        }

        $this->addPassVariable('status_string', self::$status[$this->code]);
        $this->addPassVariable('message', $this->message);
        $this->addPassVariable('code', $this->code);
        $this->displayWith('ErrorHTMLTemplate');
    }
    
}