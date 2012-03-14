<?php
/**
 * ShaiShai, the distributed microblogging tool
 *
 * Show a user's profile information
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

require_once INSTALLDIR . '/lib/api.php';

/**
 * Ouputs information for a user, specified by ID or screen name.
 * The user's most recent status will be returned inline.
 *
 * @category  API
 * @package   Shaishai
 * @author    Guofu Xie <guofu85@gmail.com>
 * @copyright 2009-2010 Shaier, Inc.
 * @link      http://www.shaishai.com/
 */

class ApiUserShowAction extends ApiAction
{
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

        $email = $this->arg('email');

        // XXX: email field deprecated in Twitter's API

        if (!empty($email)) {
            $this->user = Profile::staticGet('email', $email)->getUser();
        } else {
            $this->user = $this->getTargetUser($this->arg('id'));
        }

        return true;
    }

    /**
     * Handle the request
     *
     * Check the format and show the user info
     *
     * @param array $args $_REQUEST data (unused)
     *
     * @return void
     */

    function handle($args)
    {
        parent::handle($args);

        if (empty($this->user)) {
            $this->clientError('未找到.', 404, $this->format);
            return;
        }

        if (!in_array($this->format, array('xml', 'json'))) {
            $this->clientError('API方法未找到!', $code = 404);
            return;
        }

        $profile = $this->user->getProfile();

        if (empty($profile)) {
            $this->clientError('用户没有个人信息.');
            return;
        }
        
//        if($this->auth_user && $this->auth_user->id == $this->user->id) {
//	        $orig = clone($profile);
//	        $profile->token = common_session_token();
//	        $profile->update($orig);
//        	$twitter_user = $this->twitterUserArray($this->user->getProfile(), true, true);
//        } else {
//        	$twitter_user = $this->twitterUserArray($this->user->getProfile(), true);
//        }

        $orig = clone($profile);
        $profile->token = common_session_token();
        $profile->update($orig);
        //加token信息, 加个参数
        //自己用户可以的，但查到是其他用户就可能会有问题
        $twitter_user = $this->twitterUserArray($this->user->getProfile(), true, true);

        if ($this->format == 'xml') {
            $this->initDocument('xml');
            $this->showTwitterXmlUser($twitter_user);
            $this->endDocument('xml');
        } elseif ($this->format == 'json') {
            $this->initDocument('json');
            $this->showJsonObjects($twitter_user);
            $this->endDocument('json');
        }

    }

}
