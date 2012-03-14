<?php
/**
 * ShaiShai, the distributed microblogging tool
 *
 * Subscribe to a user via the API
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
 * Allows the authenticating users to follow (subscribe) the user specified in
 * the ID parameter.  Returns the befriended user in the requested format when
 * successful.  Returns a string describing the failure condition when unsuccessful.
 *
 * @category  API
 * @package   Shaishai
 * @author    Guofu Xie <guofu85@gmail.com>
 * @copyright 2009-2010 Shaier, Inc.
 * @link      http://www.shaishai.com/
 */

class ApiFriendshipsCreateAction extends ApiAuthAction
{
    var $other  = null;

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

        $this->user   = $this->auth_user;
        $this->other  = $this->getTargetUser((int)$this->trimmed('id'));

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

        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            $this->clientError(
                '本方法需要POST.',
                400,
                $this->format
            );
            return;
        }

        if (!in_array($this->format, array('xml', 'json'))) {
            $this->clientError(
                'API方法未找到!',
                404,
                $this->format
            );
            return;
        }

        if (empty($this->other)) {
            $this->clientError(
                '您不能关注此用户: 该用户未找到.', 
                403,
                $this->format
            );
            return;
        }

        if ($this->user->isSubscribed($this->other)) {
            $errmsg = sprintf(
                '您不能关注此用户: %s已经在您的关注列表中.', 
                $this->other->uname
            );
            $this->clientError($errmsg, 403, $this->format);
            return;
        }

        $result = Subscription::subscribeTo($this->user, $this->other);

        if (is_string($result)) {
            $this->clientError($result, 403, $this->format);
            return;
        }

        $this->initDocument($this->format);     
        $this->showProfile($this->other->getProfile(), $this->format);
        $this->endDocument($this->format);
    }

}
