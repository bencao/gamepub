<?php
/**
 * ShaiShai, the distributed microblogging tool
 *
 * Base class for showing subscription information in the API
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
 * This class outputs a list of profiles as Twitter-style user and status objects.
 * It is used by the API methods /api/statuses/(friends|followers). To support the
 * social graph methods it also can output a simple list of IDs.
 *
 * @category  API
 * @package   Shaishai
 * @author    Guofu Xie <guofu85@gmail.com>
 * @copyright 2009-2010 Shaier, Inc.
 * @link      http://www.shaishai.com/
 */

class ApiSubscriptionsAction extends ApiBareAuthAction
{
    var $profiles = null;
    var $tag      = null;
    var $lite     = null;
    var $ids_only = null;

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

        $this->tag      = $this->arg('tag');

        // Note: Twitter no longer supports 'lite'
        $this->lite     = $this->arg('lite');

        $this->ids_only = $this->arg('ids_only');

        // If called as a social graph method, show 5000 per page, otherwise 100

        $this->count    = isset($this->ids_only) ?
            5000 : (int)$this->arg('count', 100);

        $this->user = $this->getTargetUser($this->arg('id'));

        if (empty($this->user)) {
            $this->clientError('无此用户!', 404, $this->format);
            return false;
        }

        $this->profiles = $this->getProfiles();

        return true;
    }

    /**
     * Handle the request
     *
     * Show the profiles
     *
     * @param array $args $_REQUEST data (unused)
     *
     * @return void
     */

    function handle($args)
    {
        parent::handle($args);

        if (!in_array($this->format, array('xml', 'json'))) {
            $this->clientError('API方法未找到!', $code = 404);
            return;
        }

        $this->initDocument($this->format);

        if (isset($this->ids_only)) {
            $this->showIds();
        } else {
            $this->showProfiles(isset($this->lite) ? false : true);
        }

        $this->endDocument($this->format);
    }

    /**
     * Get profiles - should get overrrided
     *
     * @return array Profiles
     */

    function getProfiles()
    {
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
     * @return string datestamp of the latest profile in the stream
     */

    function lastModified()
    {
        if (!empty($this->profiles) && (count($this->profiles) > 0)) {
            return strtotime($this->profiles[0]->created);
        }

        return null;
    }

    /**
     * An entity tag for this action
     *
     * Returns an Etag based on the action name, language, user ID, and
     * timestamps of the first and last profiles in the subscriptions list
     * There's also an indicator to show whether this action is being called
     * as /api/statuses/(friends|followers) or /api/(friends|followers)/ids
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
                      $this->user->id,
                      isset($this->ids_only) ? 'IDs' : 'Profiles',
                      strtotime($this->profiles[0]->created),
                      strtotime($this->profiles[$last]->created))
            )
            . '"';
        }

        return null;
    }

    /**
     * Show the profiles as Twitter-style useres and statuses
     *
     * @param boolean $include_statuses Whether to include the latest status
     *                                  with each user. Default true.
     *
     * @return void
     */

    function showProfiles($include_statuses = true)
    {
        switch ($this->format) {
        case 'xml':
            $this->elementStart('users', array('type' => 'array'));
            foreach ($this->profiles as $profile) {
                $this->showProfile(
                    $profile,
                    $this->format,
                    null,
                    $include_statuses
                );
            }
            $this->elementEnd('users');
            break;
        case 'json':
            $arrays = array();
            foreach ($this->profiles as $profile) {
                $arrays[] = $this->twitterUserArray(
                    $profile,
                    $include_statuses
                );
            }
            print json_encode($arrays);
            break;
        default:
            $this->clientError('不支持的格式');
            break;
        }
    }

    /**
     * Show the IDs of the profiles only. 5000 per page. To support
     * the 'social graph' methods: /api/(friends|followers)/ids
     *
     * @return void
     */

    function showIds()
    {
        switch ($this->format) {
        case 'xml':
            $this->elementStart('ids');
            foreach ($this->profiles as $profile) {
                $this->element('id', null, $profile->id);
            }
            $this->elementEnd('ids');
            break;
        case 'json':
            $ids = array();
            foreach ($this->profiles as $profile) {
                $ids[] = (int)$profile->id;
            }
            print json_encode($ids);
            break;
        default:
            $this->clientError('不支持的格式');
            break;
        }
    }

}
