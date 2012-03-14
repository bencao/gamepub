<?php
/**
 * ShaiShai, the distributed microblogging tool
 *
 * Show information about the relationship between two users
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
 * Outputs detailed information about the relationship between two users
 *
 * @category  API
 * @package   Shaishai
 * @author    Guofu Xie <guofu85@gmail.com>
 * @copyright 2009-2010 Shaier, Inc.
 * @link      http://www.shaishai.com/
 */

class ApiFriendshipsShowAction extends ApiBareAuthAction
{
    var $source = null;
    var $target = null;

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

        $source_id          = (int)$this->trimmed('source_id');
        $source_screen_name = $this->trimmed('source_screen_name');
        $target_id          = (int)$this->trimmed('target_id');
        $target_screen_name = $this->trimmed('target_screen_name');

        if (!empty($source_id)) {
            $this->source = User::staticGet($source_id);
        } elseif (!empty($source_screen_name)) {
            $this->source = User::staticGet('uname', $source_screen_name);
        } else {
            $this->source = $this->auth_user;
        }

        if (!empty($target_id)) {
            $this->target = User::staticGet($target_id);
        } elseif (!empty($target_screen_name)) {
            $this->target = User::staticGet('uname', $target_screen_name);
        }

        return true;
    }


    /**
     * Determines whether this API resource requires auth.  Overloaded to look
     * return true in case source_id and source_screen_name are both empty
     *
     * @return boolean true or false
     */

    function requiresAuth()
    {
        if (common_config('site', 'private')) {
            return true;
        }

        $source_id          = $this->trimmed('source_id');
        $source_screen_name = $this->trimmed('source_screen_name');

        if (empty($source_id) && empty($source_screen_name)) {
            return true;
        }

        return false;
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

        if (!in_array($this->format, array('xml', 'json'))) {
            $this->clientError('API方法未找到!', 404);
            return;
        }

        if (empty($this->source)) {
            $this->clientError(
                '不能确定请求的用户.',
                404
             );
            return;
        }

        if (empty($this->target)) {
            $this->clientError(
                '不能找到目标用户.',
                404
            );
            return;
        }

        $result = $this->twitterRelationshipArray($this->source, $this->target);

        switch ($this->format) {
        case 'xml':
            $this->initDocument('xml');
            $this->showTwitterXmlRelationship($result[relationship]);
            $this->endDocument('xml');
            break;
        case 'json':
            $this->initDocument('json');
            print json_encode($result);
            $this->endDocument('json');
            break;
        default:
            break;
        }

    }

}
