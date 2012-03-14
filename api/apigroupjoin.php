<?php
/**
 * ShaiShai, the distributed microblogging tool
 *
 * Join a group via the API
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
 * Joins the authenticated user to the group speicified by ID
 *
 * @category  API
 * @package   Shaishai
 * @author    Guofu Xie <guofu85@gmail.com>
 * @copyright 2009-2010 Shaier, Inc.
 * @link      http://www.shaishai.com/
 */

class ApiGroupJoinAction extends ApiAuthAction
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

        if ($this->group->hasMember($this->user)) {
            $this->clientError(
                '您已经是该组的成员.',
                403,
                $this->format
            );
            return;
        }

        if ($this->group->hasBlocked($this->user)) {
            $this->clientError(
                '您已经被该组的管理员列入黑名单.',
                403,
                $this->format
            );
            return;
        }

        $member = new Group_member();

        $member->group_id   = $this->group->id;
        $member->user_id = $this->user->id;
        $member->created    = common_sql_now();

        $result = $member->insert();

        if (!$result) {
            common_log_db_error($member, 'INSERT', __FILE__);
            $this->serverError(
                sprintf(
                    '您不能在组%s添加用户%s.',
                 	$this->group->uname,
                    $this->user->uname                   
                )
            );
            return;
        }

        switch($this->format) {
        case 'xml':
            $this->show_single_xml_group($this->group);
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
