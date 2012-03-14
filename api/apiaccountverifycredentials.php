<?php
/**
 * Shaishai, the distributed microblog
 *
 * Test if supplied user credentials are valid.
 *
 * PHP version 5
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
 * Check a user's credentials. Returns an HTTP 200 OK response code and a
 * representation of the requesting user if authentication was successful;
 * returns a 401 status code and an error message if not.
 *
 * @category  API
 * @package   Shaishai
 * @author    Guofu Xie <guofu85@gmail.com>
 * @copyright 2009-2010 Shaier, Inc.
 * @link      http://www.shaishai.com/
 */

class ApiAccountVerifyCredentialsAction extends ApiAuthAction
{

    /**
     * Handle the request
     *
     * Check whether the credentials are valid and output the result
     *
     * @param array $args $_REQUEST data (unused)
     *
     * @return void
     */

    function handle($args)
    {
        parent::handle($args);

        switch ($this->format) {
        case 'xml':
        case 'json':
            $args['id'] = $this->auth_user->id;
            $action_obj = new ApiUserShowAction();
            if ($action_obj->prepare($args)) {
                $action_obj->handle($args);
            }
            break;
        default:
            header('Content-Type: text/html; charset=utf-8');
            print 'Authorized';
        }

    }

}
