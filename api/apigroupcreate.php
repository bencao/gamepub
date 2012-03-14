<?php
/**
 * ShaiShai, the distributed microblogging tool
 *
 * Create a group via the API
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
 * Make a new group. Sets the authenticated user as the administrator of the group.
 *
 * @category  API
 * @package   Shaishai
 * @author    Guofu Xie <guofu85@gmail.com>
 * @copyright 2009-2010 Shaier, Inc.
 * @link      http://www.shaishai.com/
 */

class ApiGroupCreateAction extends ApiAuthAction
{
    var $group       = null;
    var $uname    = null;
    var $nickname    = null;
    var $homepage    = null;
    var $description = null;
    var $location    = null;
    var $aliasstring = null;
    var $aliases     = null;

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

        $this->uname    = $this->arg('uname');
        $this->nickname    = $this->arg('full_name');
        $this->homepage    = $this->arg('homepage');
        $this->description = $this->arg('description');
        $this->location    = $this->arg('location');
        $this->aliasstring = $this->arg('aliases');

        return true;
    }

    /**
     * Handle the request
     *
     * Save the new group
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

        if ($this->validateParams() == false) {
            return;
        }

        $group = new User_group();

        $group->query('BEGIN');

        $group->uname    = $this->uname;
        $group->nickname    = $this->nickname;
        $group->homepage    = $this->homepage;
        $group->description = $this->description;
        $group->location    = $this->location;
        $group->created     = common_sql_now();

        $result = $group->insert();

        if (!$result) {
            common_log_db_error($group, 'INSERT', __FILE__);
            $this->serverError(
                '您不能创建组.',
                500,
                $this->format
            );
            return;
        }

        $result = $group->setAliases($this->aliases);

        if (!$result) {
            $this->serverError(
                '您不能设置别名.',
                500,
                $this->format
            );
            return;
        }

        $member = new Group_member();

        $member->group_id   = $group->id;
        $member->user_id = $this->user->id;
        $member->is_admin   = 1;
        $member->created    = $group->created;

        $result = $member->insert();

        if (!$result) {
            common_log_db_error($member, 'INSERT', __FILE__);
            $this->serverError(
                '您不能设置组的成员关系.',
                500,
                $this->format
            );
            return;
        }

        $group->query('COMMIT');

        switch($this->format) {
        case 'xml':
            $this->showSingleXmlGroup($group);
            break;
        case 'json':
            $this->showSingleJsonGroup($group);
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

    /**
     * Validate params for the new group
     *
     * @return void
     */

    function validateParams()
    {
        $valid = Validate::string(
            $this->uname, array(
                'min_length' => 1,
                'max_length' => 64,
                'format' => UNAME_FMT
            )
        );

        if (!$valid) {
            $this->clientError(                
                    '名字只能为小写字母, 数组和非空字符.',
                403,
                $this->format
            );
            return false;
        } elseif ($this->groupunameExists($this->uname)) {
            $this->clientError(
                '名字已被使用, 请试其他.',
                403,
                $this->format
            );
            return false;
        } else if (!User_group::alloweduname($this->uname)) {
            $this->clientError(
                '非法的名字.',
                403,
                $this->format
            );
            return false;

        } elseif (
            !is_null($this->homepage)
            && strlen($this->homepage) > 0
            && !Validate::uri(
                $this->homepage, array(
                    'allowed_schemes' =>
                    array('http', 'https')
                )
            )) {
            $this->clientError(
                '个人网页不是合法的链接.',
                403,
                $this->format
            );
            return false;
        } elseif (
            !is_null($this->nickname)
            && mb_strlen($this->nickname) > 255) {
                $this->clientError(
                    '全名太长(大于255个字符).',
                    403,
                    $this->format
                );
            return false;
        } elseif (User_group::descriptionTooLong($this->description)) {
            $this->clientError(
                sprintf(
                    '描述太长(大于%d字符).',
                    User_group::maxDescription()
                ),
                403,
                $this->format
            );
            return false;
        } elseif (
            !is_null($this->location)
            && mb_strlen($this->location) > 255) {
                $this->clientError(
                    '地域太长(大于255个字符).',
                    403,
                    $this->format
                );
            return false;
        }

        if (!empty($this->aliasstring)) {
            $this->aliases = array_map(
                'strtolower',
                array_unique(preg_split('/[\s,]+/', $this->aliasstring))
            );
        } else {
            $this->aliases = array();
        }

        if (count($this->aliases) > common_config('group', 'maxaliases')) {
            $this->clientError(
                sprintf(
                    '太长的别名, 最大为 %d.',
                    common_config('group', 'maxaliases')
                ),
                403,
                $this->format
            );
            return false;
        }

        foreach ($this->aliases as $alias) {

            $valid = Validate::string(
                $alias, array(
                    'min_length' => 1,
                    'max_length' => 64,
                    'format' => UNAME_FMT
                )
            );

            if (!$valid) {
                $this->clientError(
                    sprintf('非法的别名: "%s"', $alias),
                    403,
                    $this->format
                );
                return false;
            }
            if ($this->groupunameExists($alias)) {
                $this->clientError(
                    sprintf(
                        '别名"%s"已经被使用, 请使用其他.',
                        $alias
                    ),
                    403,
                    $this->format
                );
                return false;
            }

            // XXX assumes alphanum unames

            if (strcmp($alias, $this->uname) == 0) {
                $this->clientError(
                    '别名不能与名字重复.',
                    403,
                    $this->format
                );
                return false;
            }
        }

        // Evarything looks OK

        return true;
    }

    /**
     * Check to see whether a uname is already in use by a group
     *
     * @param String $uname The uname in question
     *
     * @return boolean true or false
     */

    function groupunameExists($uname)
    {
        $group = User_group::staticGet('uname', $uname);

        if (!empty($group)) {
            return true;
        }

        $alias = Group_alias::staticGet('alias', $uname);

        if (!empty($alias)) {
            return true;
        }

        return false;
    }

}
