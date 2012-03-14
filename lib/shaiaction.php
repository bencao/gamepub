<?php
if (!defined('SHAISHAI')) {
    exit(1);
}

class ShaiAction
{
	var $args;
	var $view;
	var $paras;
	var $no_anonymous;
	
	var $cur_user = null;
	var $cur_page = null;
	var $is_anonymous = null;
	
	var $cache_allowed = true;
	var $cache_result = null;
	
	function __construct() {
		$this->no_anonymous = true;
	}
	
	/**
     * For initializing members of the class.
     *
     * @param array $argarray misc. arguments
     *
     * @return boolean true
     */
    function prepare($argarray)
    {
        $this->args =& common_copy_args($argarray);
        if ($this->no_anonymous 
        	&& ! common_current_user()) {
            $this->notLoggedInError('请先登录, 才能进行操作');
            return false;
        }
        
        $exceptions = array('uploadfile', 'login', 'shareoutlink', 'noticesearch', 'delauth');
        
        // CRSF protection
        if ($_SERVER['REQUEST_METHOD'] == 'POST'
        	&& ! in_array($this->trimmed('action'), $exceptions)) {
	        $token = $this->trimmed('token');
	        if (!$token || $token != common_session_token()) {
	            $this->clientError('您的会话令牌有误，请重试。', 404);
	            return false;
	        }
        }
        
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
        
        if($this->arg('page') && !is_numeric($this->arg('page'))) {
	        $this->clientError('您访问的页数不存在.', 403);
	        return;
	    }
        
        
//        common_debug('curpage=' . $this->cur_page);
    	if ($argarray != null) {
        	$this->paras = array_merge($argarray);
        } else {
        	$this->paras = array();
        }
        
        return true;
    }
    
	/**
     * Return true if read only.
     *
     * MAY override
     *
     * @param array $args other arguments
     *
     * @return boolean is read only action?
     */

    function isReadOnly($args)
    {
        return false;
    }
    
	/**
     * Returns query argument or default value if not found
     *
     * @param string $key requested argument
     * @param string $def default value to return if $key is not provided
     *
     * @return boolean is read only action?
     */
    function arg($key, $def=null)
    {
        if (array_key_exists($key, $this->args)) {
            return $this->args[$key];
        } else {
            return $def;
        }
    }
    
/**
     * Returns trimmed query argument or default value if not found
     *
     * @param string $key requested argument
     * @param string $def default value to return if $key is not provided
     *
     * @return boolean is read only action?
     */
    function trimmed($key, $def=null)
    {
        $arg = $this->arg($key, $def);
        return is_string($arg) ? trim($arg) : $arg;
    }

    /**
     * Handler method
     *
     * @param array $argarray is ignored since it's now passed in in prepare()
     *
     * @return boolean is read only action?
     */
    function handle($argarray=null)
    {
        $lm   = $this->lastModified();
        $etag = $this->etag();
        if ($etag) {
            header('ETag: ' . $etag);
        }
        if ($lm) {
            header('Last-Modified: ' . date(DATE_RFC1123, $lm));
            if (array_key_exists('HTTP_IF_MODIFIED_SINCE', $_SERVER)) {
                $if_modified_since = $_SERVER['HTTP_IF_MODIFIED_SINCE'];
                $ims = strtotime($if_modified_since);
                if ($lm <= $ims) {
                    $if_none_match = (array_key_exists('HTTP_IF_NONE_MATCH', $_SERVER)) ?
                      $_SERVER['HTTP_IF_NONE_MATCH'] : null;
                    if (!$if_none_match ||
                        !$etag ||
                        $this->_hasEtag($etag, $if_none_match)) {
                        header('HTTP/1.1 304 Not Modified');
                        // Better way to do this?
                        exit(0);
                    }
                }
            }
        }
        
        // 传给视图
        $this->addPassVariable('cur_user', $this->cur_user);
        $this->addPassVariable('cur_page', $this->cur_page);
        $this->addPassVariable('cache_allowed', $this->cache_allowed);
    }
    
/**
     * Boolean understands english (yes, no, true, false)
     *
     * @param string $key query key we're interested in
     * @param string $def default value
     *
     * @return boolean interprets yes/no strings as boolean
     */
    function boolean($key, $def=false)
    {
        $arg = strtolower($this->trimmed($key));

        if (is_null($arg)) {
            return $def;
        } else if (in_array($arg, array('true', 'yes', '1', 'on'))) {
            return true;
        } else if (in_array($arg, array('false', 'no', '0'))) {
            return false;
        } else {
            return $def;
        }
    }
    
/**
     * Server error
     *
     * @param string  $msg  error message to display
     * @param integer $code http error code, 500 by default
     *
     * @return nothing
     */

    function serverError($msg, $code=500)
    {
        $action = $this->trimmed('action');
        throw new ServerException($msg, $code);
    }
    
    function notLoggedInError($msg, $code=200) {
    	$action = $this->trimmed('action');
       // common_debug("Server error '$code' on '$action': $msg", __FILE__);
       	common_set_returnto($this->selfUrl());
       	require_once INSTALLDIR.'/lib/notloggedinexception.php';
        throw new NotLoggedInException($msg, $code);
    }

    /**
     * Client error
     *
     * @param string  $msg  error message to display
     * @param integer $code http error code, 400 by default
     *
     * @return nothing
     */

    function clientError($msg, $code=400)
    {
        $action = $this->trimmed('action');
        throw new ClientException($msg, $code);
    }

    /**
     * Returns the current URL
     *
     * @return string current URL
     */

    function selfUrl()
    {
        $action = $this->trimmed('action');
        $args   = $this->args;
        unset($args['action']);
        if (common_config('site', 'fancy')) {
            unset($args['p']);
        }
        if (array_key_exists('submit', $args)) {
            unset($args['submit']);
        }
        foreach (array_keys($_COOKIE) as $cookie) {
            unset($args[$cookie]);
        }

        return common_local_url($action, $args);
    }
    
	/**
     * Return last modified, if applicable.
     *
     * MAY override
     *
     * @return string last modified http header
     */
    function lastModified()
    {
        // For comparison with If-Last-Modified
        // If not applicable, return null
        return null;
    }
    
	/**
     * Return etag, if applicable.
     *
     * MAY override
     *
     * @return string etag http header
     */
    function etag()
    {
        return null;
    }
    
	/**
     * Has etag? (private)
     *
     * @param string $etag          etag http header
     * @param string $if_none_match ifNoneMatch http header
     *
     * @return boolean
     */

    function _hasEtag($etag, $if_none_match)
    {
        $etags = explode(',', $if_none_match);
        return in_array($etag, $etags) || in_array('*', $etags);
    }
    
    
    function displayWith($viewname)
    {
    	$this->view = TemplateFactory::get($viewname);
    	$this->addPassVariable('template_name', substr(strtolower($viewname), 0, - 12));
    	$this->view->show($this->paras);
    }
    
    function addPassVariable($varname, $varvalue)
    {
    	$this->paras[$varname] = $varvalue;
    }
    
    function rmPassVariable($varname) {
    	unset($this->paras[$varname]);
    }
    
	function showJsonResult($datas) {
    	$this->view = TemplateFactory::get('JsonTemplate');
        $this->view->init_document($this->paras);
        $this->view->show_json_objects($datas);
        $this->view->end_document();
    }
}

?>