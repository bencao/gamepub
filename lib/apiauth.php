<?php
/**
 * ShaiShai, the distributed microblogging tool
 *
 * Base class for API actions that require authentication
 *
 * PHP version 5
 *
 * @category  API
 * @package   ShaiShai
 * @copyright 2009 ShaiShai, Inc.
 */

if (!defined('SHAISHAI')) {
    exit(1);
}

require_once INSTALLDIR . '/lib/api.php';

/**
 * Actions extending this class will require auth
 *
 * @category API
 * @package  ShaiShai
 */

class ApiAuthAction extends ApiAction
{

    //var $auth_user = null;

    /**
     * Take arguments for running, and output basic auth header if needed
     *
     * @param array $args $_REQUEST args
     *
     * @return boolean success flag
     *
     */

    function prepare($args)
    {
        if (! parent::prepare($args)) {return false;}

        if ($this->requiresAuth()) {
            $this->checkBasicAuthUser();
        }

        return true;
    }

    /**
     * Does this API resource require authentication?
     *
     * @return boolean true
     */

    function requiresAuth()
    {
        return true;
    }

    /**
     * Check for a user specified via HTTP basic auth. If there isn't
     * one, try to get one by outputting the basic auth header.
     *
     * @return boolean true or false
     */

    function checkBasicAuthUser()
    {
        $this->basicAuthProcessHeader();

        $realm = common_config('site', 'ename') . ' API';

        if (!isset($this->auth_user)) {
            header('WWW-Authenticate: Basic realm="' . $realm . '"');

            // show error if the user clicks 'cancel'

            $this->showBasicAuthError();
            exit;

        } else {
            $uname = $this->auth_user;
            $password = $this->auth_pw;
        	$user = false;
        	
		    if (Event::handle('StartCheckPassword', array($uname, $password, &$user))) {
			    
			    $profile = Profile::getByUNameAndPassword($uname, $password);
			    
		        if ($profile) {
		            if (!empty($password)) { // never allow login with blank password
		                if (0 == strcmp(common_munge_password($password, $profile->id),
		                                $profile->password)) {
		                    //internal checking passed
		                    $user = $profile->getUser();
		                }
		            }
		        }
		        Event::handle('EndCheckPassword', array($uname, $password, $user));
		    }
        	
            if (Event::handle('StartSetApiUser', array(&$user))) {
                $this->auth_user = $user;
                Event::handle('EndSetApiUser', array($user));
            }
            
            if (empty($this->auth_user)) {

                // basic authentication failed
                common_log(
                    LOG_WARNING,
                    'Failed API auth attempt, uname = ' .
                    "$uname"
                );
                $this->showBasicAuthError();
                exit;
            }
        }
        return true;
    }

    /**
     * Read the HTTP headers and set the auth user.  Decodes HTTP_AUTHORIZATION
     * param to support basic auth when PHP is running in CGI mode.
     *
     * @return void
     */

    function basicAuthProcessHeader()
    {
        if (isset($_SERVER['AUTHORIZATION'])
            || isset($_SERVER['HTTP_AUTHORIZATION'])
        ) {
                $authorization_header = isset($_SERVER['HTTP_AUTHORIZATION'])
                ? $_SERVER['HTTP_AUTHORIZATION'] : $_SERVER['AUTHORIZATION'];
        }

        if (isset($_SERVER['PHP_AUTH_USER'])) {
            $this->auth_user = $_SERVER['PHP_AUTH_USER'];
            $this->auth_pw = $_SERVER['PHP_AUTH_PW'];
        } elseif (isset($authorization_header)
            && strstr(substr($authorization_header, 0, 5), 'Basic')) {

            // decode the HTTP_AUTHORIZATION header on php-cgi server self
            // on fcgid server the header name is AUTHORIZATION

            $auth_hash = base64_decode(substr($authorization_header, 6));
            list($this->auth_user, $this->auth_pw) = explode(':', $auth_hash);

            // set all to null on a empty basic auth request

            if ($this->auth_user == "") {
                $this->auth_user = null;
                $this->auth_pw = null;
            }
        } else {
            $this->auth_user = null;
            $this->auth_pw = null;
        }
    }

    /**
     * Output an authentication error message.  Use XML or JSON if one
     * of those formats is specified, otherwise output plain text
     *
     * @return void
     */

    function showBasicAuthError()
    {
        header('HTTP/1.1 401 Unauthorized');
        $msg = '登录失败!';

        if ($this->format == 'xml') {
            header('Content-Type: application/xml; charset=utf-8');
            $this->startXML();
            $this->elementStart('hash');
            $this->element('error', null, $msg);
            $this->element('request', null, $_SERVER['REQUEST_URI']);
            $this->elementEnd('hash');
            $this->endXML();
        } elseif ($this->format == 'json') {
            header('Content-Type: application/json; charset=utf-8');
            $error_array = array('error' => $msg,
                                 'request' => $_SERVER['REQUEST_URI']);
            print(json_encode($error_array));
        } else {
            header('Content-type: text/plain');
            print "$msg\n";
        }
    }

}
