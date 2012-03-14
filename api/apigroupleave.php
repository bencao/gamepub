<?php
/**
 * ShaiShai, the distributed microblogging tool
 *
 * Leave a group via the API
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
 * Removes the authenticated user from the group specified by ID
 *
 * @category  API
 * @package   Shaishai
 * @author    Guofu Xie <guofu85@gmail.com>
 * @copyright 2009-2010 Shaier, Inc.
 * @link      http://www.shaishai.com/
 */

class ApiGroupLeaveAction extends ApiAuthAction
{
    var $group   = null;

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

        $this->user  = $this->auth_user;
        $this->group = $this->getTargetGroup($this->arg('id'));

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
                '本方法需要POST.',
                400,
                $this->format
            );
            return;
        }

        if (empty($this->user)) {
            $this->clientError('无此用户!', 404, $this->format);
            return;
        }

        if (empty($this->group)) {
            $this->clientError('组未找到!', 404, $this->format);
            return false;
        }

        $member = new Group_member();

        $member->group_id   = $this->group->id;
        $member->user_id = $this->auth->id;

        if (!$member->find(true)) {
            $this->serverError('您不是该组的成员.');
            return;
        }

        $result = $member->delete();

        if (!$result) {
            common_log_db_error($member, 'INSERT', __FILE__);
            $this->serverError(
                sprintf(
                    '您不能%s组的%s成员Could not remove user %s to group %s.',
                    $this->$group->uname,
                    $this->user->uname
                )
            );
            return;
        }

        switch($this->format) {
        case 'xml':
            $this->showSingleXmlGroup($this->group);
            break;
        case 'json':
            $this->showSingleJsonGroup($this->group);
            break;
        default:
            $this->clientError(
                'API方法未找到!',
                404,
                $this->format
            );
            break;
        }
    }

}
