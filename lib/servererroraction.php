<?php
/**
 * Shaishai, the distributed microblog
 *
 * Server error action
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
 * Server error action
 *
 * @category Personal
 * @package  Shaishai
 * @author    Guofu Xie <guofu85@gmail.com>
 * @copyright 2009-2010 Shaier, Inc.
 * @link      http://www.shaishai.com/
 */

class ServerErrorAction extends ShaiAction
{
	var $code    = null;
    var $message = null;
//    var $status  = null;
    var $default = null;
    
    static $status  = array(500 => 'Internal Server Error',
                               501 => 'Not Implemented',
                               502 => 'Bad Gateway',
                               503 => 'Service Unavailable',
                               504 => 'Gateway Timeout',
                               505 => 'HTTP Version Not Supported');
                               
	function __construct($message='Error', $code=400)
    {
    	$this->code = $code;
        $this->message = $message;
        $this->cache_allowed = false;

        // XXX: hack alert: usually we aren't going to
        // call this page directly, but because it's
        // an action it needs an args array anyway
//        $this->prepare($_REQUEST);      

        $this->default = 500;
    }
    
    // XXX: Should these error actions even be invokable via URI?

    function handle($args=null)
    {
        parent::handle($args);

        if (!$this->code || $this->code < 500 || $this->code > 599) {
            $this->code = $this->default;
        }

        if (!$this->message) {
            $this->message = "服务器错误 $this->code";
        }
        
        $this->addPassVariable('status_string', self::$status[$this->code]);
        $this->addPassVariable('message', $this->message);
        $this->addPassVariable('code', $this->code);
        $this->displayWith('ErrorHTMLTemplate');
    }
    
}