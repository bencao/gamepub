<?php
/**
 * ShaiShai, the distributed microblogging tool
 *
 * Block a user via the API
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
 * Blocks the user specified in the ID parameter as the authenticating user.
 * Destroys a friendship to the blocked user if it exists. Returns the
 * blocked user in the requested format when successful.
 *
 * @category  API
 * @package   Shaishai
 * @author    Guofu Xie <guofu85@gmail.com>
 * @copyright 2009-2010 Shaier, Inc.
 * @link      http://www.shaishai.com/
 */

class ApiBlockCreateAction extends ApiAuthAction
{
    var $other   = null;

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
        $this->other  = $this->getTargetUser($this->arg('id'));

        return true;
    }

    /**
     * Handle the request
     *
     * Save the new message
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
                '此方法需要POST.',
                400,
                $this->format
            );
            return;
        }

        if (empty($this->user) || empty($this->other)) {
            $this->clientError('无此用户!', 404, $this->format);
            return;
        }

        if ($this->user->hasBlocked($this->other)
            || $this->user->block($this->other)
        ) {
            $this->initDocument($this->format);
            $this->showProfile($this->other, $this->format);
            $this->endDocument($this->format);
        } else {
            $this->serverError('列入黑名单失败.', 500, $this->format);
        }

    }

}

