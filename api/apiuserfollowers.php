<?php
/**
 * ShaiShai, the distributed microblogging tool
 *
 * Show a user's followers (subscribers)
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
 * Ouputs the authenticating user's followers (subscribers), each with
 * current Twitter-style status inline.  They are ordered by the order
 * in which they subscribed to the user, 100 at a time.
 *
 * @category  API
 * @package   Shaishai
 * @author    Guofu Xie <guofu85@gmail.com>
 * @copyright 2009-2010 Shaier, Inc.
 * @link      http://www.shaishai.com/
 */

class ApiUserFollowersAction extends ApiSubscriptionsAction
{
    /**
     * Get the user's subscribers (followers) as an array of profiles
     *
     * @return array Profiles
     */

    function getProfiles()
    {
        $offset = ($this->page - 1) * $this->count;
        $limit =  $this->count + 1;

        $subs = null;

        if (isset($this->tag)) {
            $subs = $this->user->getTaggedSubscribers(
                $this->tag, $offset, $limit
            );
        } else {
            $subs = $this->user->getSubscribers(
                $offset,
                $limit
            );
        }

        $profiles = array();

        if (!empty($subs)) {
            while ($subs->fetch()) {
                $profiles[] = clone($subs);
            }
        }

        return $profiles;
    }

}
