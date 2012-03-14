<?php
/**
 * ShaiShai, the distributed microblogging tool
 *
 * Check to see whether a user a member of a group
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

require_once INSTALLDIR . '/lib/apibareauth.php';

/**
 * Returns whether a user is a member of a specified group.
 *
 * @category  API
 * @package   Shaishai
 * @author    Guofu Xie <guofu85@gmail.com>
 * @copyright 2009-2010 Shaier, Inc.
 * @link      http://www.shaishai.com/
 */

class ApiGroupIsMemberAction extends ApiBareAuthAction
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

        $this->user   = $this->getTargetUser(null);
        $this->group  = $this->getTargetGroup(null);

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

       if (empty($this->user)) {
            $this->clientError('无此用户!', 404, $this->format);
            return;
        }

        if (empty($this->group)) {
            $this->clientError('组未找到!', 404, $this->format);
            return false;
        }

        $is_member = $this->group->hasMember($this->user);

        switch($this->format) {
        case 'xml':
            $this->initDocument('xml');
            $this->element('is_member', null, $is_member);
            $this->endDocument('xml');
            break;
        case 'json':
            $this->initDocument('json');
            $this->showJsonObjects(array('is_member' => $is_member));
            $this->endDocument('json');
            break;
        default:
            $this->clientError(
                'API方法未找到!',
                400,
                $this->format
            );
            break;
        }
    }

}
