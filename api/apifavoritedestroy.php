<?php
/**
 * ShaiShai, the distributed microblogging tool
 *
 * Remote a notice from a user's list of favorite notices via the API
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
 * Un-favorites the status specified in the ID parameter as the authenticating user.
 * Returns the un-favorited status in the requested format when successful.
 *
 * @category  API
 * @package   Shaishai
 * @author    Guofu Xie <guofu85@gmail.com>
 * @copyright 2009-2010 Shaier, Inc.
 * @link      http://www.shaishai.com/
 */

class ApiFavoriteDestroyAction extends ApiAuthAction
{

    var $notice = null;

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

        $fave            = new Fave();
        $fave->user_id   = $this->user->id;
        $fave->notice_id = $this->notice->id;

        if (!$fave->find(true)) {
            $this->clientError(
                '此状态还未被收藏!',
                403,
                $this->favorite
            );
            return;
        }

        $result = $fave->delete();

        if (!$result) {
            common_log_db_error($fave, 'DELETE', __FILE__);
            $this->clientError(
                '不能取消收藏.',
                404,
                $this->format
            );
            return;
        }

        $this->user->blowFavesCache();

        if ($this->format == 'xml') {
            $this->showSingleXmlStatus($this->notice);
        } elseif ($this->format == 'json') {
            $this->show_single_json_status($this->notice);
        }
    }

}
