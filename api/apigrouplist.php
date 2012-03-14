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

class ApiGroupListAction extends ApiBareAuthAction
{
    var $groups   = null;

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

        $this->user   = $this->getTargetUser($this->arg('id'));
        $this->groups = $this->getGroups();

        return true;
    }

    /**
     * Handle the request
     *
     * Show the user's groups
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

        $sitename   = common_config('site', 'name');
        $title      = sprintf("%s的组", $this->user->uname);
        $taguribase = common_config('integration', 'taguri');
        $id         = "tag:$taguribase:Groups";
        $link       = common_local_url(
            'usergroups',
            array('uname' => $this->user->uname)
        );
        $subtitle   = sprintf(
            "在 %s上, %s所在的组.",
            $sitename,
            $this->user->uname
        );

        switch($this->format) {
        case 'xml':
            $this->showXmlGroups($this->groups);
            break;
        case 'rss':
            $this->showRssGroups($this->groups, $title, $link, $subtitle);
            break;
        case 'atom':
            $selfuri = common_path('') . 'api/shaishai/groups/list/' .
                $this->user->id . '.atom';
            $this->showAtomGroups(
                $this->groups,
                $title,
                $id,
                $link,
                $subtitle,
                $selfuri
            );
            break;
        case 'json':
            $this->showJsonGroups($this->groups);
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
     * Get groups
     *
     * @return array groups
     */

    function getGroups()
    {
        $groups = array();

        $group = $this->user->getGroups(
            ($this->page - 1) * $this->count,
            $this->count,
            $this->since_id,
            $this->max_id,
            $this->since
        );

        while ($group->fetch()) {
            $groups[] = clone($group);
        }

        return $groups;
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
     * When was this feed last modified?
     *
     * @return string datestamp of the latest group the user has joined
     */

    function lastModified()
    {
        if (!empty($this->groups) && (count($this->groups) > 0)) {
            return strtotime($this->groups[0]->created);
        }

        return null;
    }

    /**
     * An entity tag for this list of groups
     *
     * Returns an Etag based on the action name, language, user ID and
     * timestamps of the first and last group the user has joined
     *
     * @return string etag
     */

    function etag()
    {
        if (!empty($this->groups) && (count($this->groups) > 0)) {

            $last = count($this->groups) - 1;

            return '"' . implode(
                ':',
                array($this->arg('action'),
//                      common_language(),
                      $this->user->id,
                      strtotime($this->groups[0]->created),
                      strtotime($this->groups[$last]->created))
            )
            . '"';
        }

        return null;
    }

}
