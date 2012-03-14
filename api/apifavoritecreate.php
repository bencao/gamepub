<?php
/**
 * ShaiShai, the distributed microblogging tool
 *
 * Add a notice to a user's list of favorite notices via the API
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
 * Favorites the status specified in the ID parameter as the authenticating user.
 * Returns the favorite status when successful.
 *
 * @category  API
 * @package   Shaishai
 * @author    Guofu Xie <guofu85@gmail.com>
 * @copyright 2009-2010 Shaier, Inc.
 * @link      http://www.shaishai.com/
 */

class ApiFavoriteCreateAction extends ApiAuthAction
{
    var $notice = null;
    var $fave_group_id = null;

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
        $this->notice = Notice::staticGet($this->arg('id'));

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

        if (empty($this->notice)) {
            $this->clientError(
                '未找到此ID的状态.',
                404,
                $this->format
            );
            return;
        }

        // Note: Twitter lets you fave things repeatedly via API.
        $this->fave_group_id = $this->trimmed('fave_group_id');
        

        if ($this->user->hasFave($this->notice)) {
            $this->clientError(
                '此状态已经被收藏!',
                403,
                $this->format
            );
            return;
        }

        //添加收藏夹
        $fave = Fave::addNew($this->user, $this->notice, $this->fave_group_id);

        if (empty($fave)) {
            $this->clientError(
                '不能收藏此状态.',
                403,
                $this->format
            );
            return;
        }

        $this->notify($fave, $this->notice, $this->user);
        $this->user->blowFavesCache();

        if ($this->format == 'xml') {
            $this->showSingleXmlStatus($this->notice);
        } elseif ($this->format == 'json') {
            $this->show_single_json_status($this->notice);
        }
    }

    /**
     * Notify the author of the favorite that the user likes their notice
     *
     * @param Favorite $fave   the favorite in question
     * @param Notice   $notice the notice that's been faved
     * @param User     $user   the user doing the favoriting
     *
     * @return void
     */
    function notify($fave, $notice, $user)
    {
        $other = User::staticGet('id', $notice->user_id);
        if ($other && $other->id != $user->id) {
        	$otherProfile = $other->getProfile();
            if ($otherProfile->email && $otherProfile->emailnotifyfav) {
                mail_notify_fave($otherProfile, $user->getProfile(), $notice);
            }
            // XXX: notify by IM
            // XXX: notify by SMS
        }
    }

}
