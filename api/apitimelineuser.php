<?php
/**
 * ShaiShai, the distributed microblogging tool
 *
 * Show a user's timeline
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
 * Returns the most recent notices (default 20) posted by the authenticating
 * user. Another user's timeline can be requested via the id parameter. This
 * is the API equivalent of the user profile web page.
 *
 * @category  API
 * @package   Shaishai
 * @author    Guofu Xie <guofu85@gmail.com>
 * @copyright 2009-2010 Shaier, Inc.
 * @link      http://www.shaishai.com/
 */

class ApiTimelineUserAction extends ApiBareAuthAction
{

    var $notices = null;

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

        $this->user = $this->getTargetUser($this->arg('id'));

        if (empty($this->user)) {
            $this->clientError('无此用户!', 404, $this->format);
            return;
        }

        $this->notices = $this->getNotices();

        return true;
    }

    /**
     * Handle the request
     *
     * Just show the notices
     *
     * @param array $args $_REQUEST data (unused)
     *
     * @return void
     */

    function handle($args)
    {
        parent::handle($args);
        $this->showTimeline();
    }

    /**
     * Show the timeline of notices
     *
     * @return void
     */

    function showTimeline()
    {
        $profile = $this->user->getProfile();
        
        $sitename   = common_config('site', 'name');
        $title      = sprintf("%s的时间线", $this->user->uname);
        $taguribase = common_config('integration', 'taguri');
        $id         = "tag:$taguribase:UserTimeline:".$this->user->id;
        $link       = common_path($this->user->uname);
        $subtitle   = sprintf('来自$s的更新!', $this->user->uname);

        // FriendFeed's SUP protocol
        // Also added RSS and Atom feeds

//        $suplink = common_local_url('sup', null, null, $this->user->id);
//        header('X-SUP-ID: ' . $suplink);

        switch($this->format) {
        case 'xml':
            $this->showXmlTimeline($this->notices);
            break;
        case 'rss':
            $this->showRssTimeline(
                $this->notices, $title, $link,
                $subtitle, $suplink
            );
            break;
        case 'atom':
            if (isset($apidata['api_arg'])) {
                $selfuri = common_path('') .
                    'api/statuses/user_timeline/' .
                    $apidata['api_arg'] . '.atom';
            } else {
                $selfuri = common_path('') .
                    'api/statuses/user_timeline.atom';
            }
//            if (isset($this->arg('id'))) {
//                $selfuri = common_path('') .
//                    'api/statuses/user_timeline/' .
//                    $this->arg('id') . '.atom';
//            } else {
//                $selfuri = common_path('') .
//                    'api/statuses/user_timeline.atom';
//            }
            $this->showAtomTimeline(
                $this->notices, $title, $id, $link,
                $subtitle, $suplink, $selfuri
            );
            break;
        case 'json':
            $this->showJsonTimeline($this->notices);
            break;
        default:
            $this->clientError('API方法未找到!', $code = 404);
            break;
        }

    }

    /**
     * Get notices
     *
     * @return array notices
     */

    function getNotices()
    {
        $notices = array();

        $notice = $this->user->getNotices(
            ($this->page-1) * $this->count, $this->count,
            $this->since_id, $this->max_id, $this->since
        );

        while ($notice->fetch()) {
            $notices[] = clone($notice);
        }

        return $notices;
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
     * @return string datestamp of the latest notice in the stream
     */

    function lastModified()
    {
        if (!empty($this->notices) && (count($this->notices) > 0)) {
            return strtotime($this->notices[0]->created);
        }

        return null;
    }

    /**
     * An entity tag for this stream
     *
     * Returns an Etag based on the action name, language, user ID, and
     * timestamps of the first and last notice in the timeline
     *
     * @return string etag
     */

    function etag()
    {
        if (!empty($this->notices) && (count($this->notices) > 0)) {

            $last = count($this->notices) - 1;

            return '"' . implode(
                ':',
                array($this->arg('action'),
//                      common_language(),
                      $this->user->id,
                      strtotime($this->notices[0]->created),
                      strtotime($this->notices[$last]->created))
            )
            . '"';
        }

        return null;
    }

}
