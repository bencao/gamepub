<?php
/**
 * ShaiShai, the distributed microblogging tool
 *
 * List a group's members
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
 * List 20 newest members of the group specified by name or ID.
 *
 * @category  API
 * @package   Shaishai
 * @author    Guofu Xie <guofu85@gmail.com>
 * @copyright 2009-2010 Shaier, Inc.
 * @link      http://www.shaishai.com/
 */

class ApiGroupMembershipAction extends ApiAction
{
    var $group    = null;
    var $profiles = null;

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

        $this->group    = $this->getTargetGroup($this->arg('id'));
        $this->profiles = $this->getProfiles();

        return true;
    }

    /**
     * Handle the request
     *
     * Show the members of the group
     *
     * @param array $args $_REQUEST data (unused)
     *
     * @return void
     */

    function handle($args)
    {
        parent::handle($args);

        // XXX: RSS and Atom

        switch($this->format) {
        case 'xml':
            $this->showTwitterXmlUsers($this->profiles);
            break;
        case 'json':
            $this->showJsonUsers($this->profiles);
            break;
        default:
            $this->clientError('API方法未找到',
                404,
                $this->format
            );
            break;
        }
    }

    /**
     * Fetch the members of a group
     *
     * @return array $profiles list of profiles
     */

    function getProfiles()
    {
        $profiles = array();

        $profile = $this->group->getMembers(
            ($this->page - 1) * $this->count,
            $this->count,
            $this->since_id,
            $this->max_id,
            $this->since
        );

        while ($profile->fetch()) {
            $profiles[] = clone($profile);
        }

        return $profiles;
    }

    /**
     * Is this action read only?
     *
     * @param array $args other arguments
     *
     * @return boolean true
     */

    function isReadOnly($args)
    {
        return true;
    }

    /**
     * When was this list of profiles last modified?
     *
     * @return string datestamp of the lastest profile in the group
     */

    function lastModified()
    {
        if (!empty($this->profiles) && (count($this->profiles) > 0)) {
            return strtotime($this->profiles[0]->created);
        }

        return null;
    }

    /**
     * An entity tag for this list of groups
     *
     * Returns an Etag based on the action name, language
     * the group id, and timestamps of the first and last
     * user who has joined the group
     *
     * @return string etag
     */

    function etag()
    {
        if (!empty($this->profiles) && (count($this->profiles) > 0)) {

            $last = count($this->profiles) - 1;

            return '"' . implode(
                ':',
                array($this->arg('action'),
//                      common_language(),
                      $this->group->id,
                      strtotime($this->profiles[0]->created),
                      strtotime($this->profiles[$last]->created))
            )
            . '"';
        }

        return null;
    }

}
