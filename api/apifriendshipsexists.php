<?php
/**
 * ShaiShai, the distributed microblogging tool
 *
 * Show whether there is a friendship between two users
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
 * Tests for the existence of friendship between two users. Will return true if
 * user_a follows user_b, otherwise will return false.
 *
 * @category  API
 * @package   Shaishai
 * @author    Guofu Xie <guofu85@gmail.com>
 * @copyright 2009-2010 Shaier, Inc.
 * @link      http://www.shaishai.com/
 */

class ApiFriendshipsExistsAction extends ApiAction
{
    var $user_a = null;
    var $user_b = null;

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

        $user_a_id = $this->trimmed('user_a');
        $user_b_id = $this->trimmed('user_b');

        $this->user_a = $this->getTargetUser($user_a_id);

        if (empty($this->user_a)) {
//            common_debug('gargargra');
        }

        $this->user_b = $this->getTargetUser($user_b_id);

        return true;
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

        if (empty($this->user_a) || empty($this->user_b)) {
            $this->clientError('应当提供两个用户的ID及名字.',
                400,
                $this->format
            );
            return;
        }

        $result = $this->user_a->isSubscribed($this->user_b);

        switch ($this->format) {
        case 'xml':
            $this->initDocument('xml');
            $this->element('friends', null, $result);
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
