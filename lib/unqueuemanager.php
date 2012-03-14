<?php
/**
 * Shaishai, the distributed microblog
 *
 * new notice form
 *
 * PHP version 5
 *
 * @category  Notice
 * @package   Shaishai
 * @author    Guofu Xie <guofu85@gmail.com>
 * @copyright 2009-2010 Shaier, Inc.
 * @link      http://www.shaishai.com/
 */

class UnQueueManager
{
    function enqueue($object, $queue)
    {
        $notice = $object;

        switch ($queue)
        {
//         case 'omb':
//            if ($this->_isLocal($notice)) {
//            	//这里加载失败, 需要重新考虑
//                require_once(INSTALLDIR.'/lib/omb.php');
//                omb_broadcast_remote_subscribers($notice);
//            }
//            break;
         case 'public':
            if ($this->_isLocal($notice)) {
                require_once(INSTALLDIR.'/lib/jabber.php');
                jabber_public_notice($notice);
            }
            break;
         case 'twitter':
            if ($this->_isLocal($notice)) {
                broadcast_twitter($notice);
            }
            break;
         case 'facebook':
            if ($this->_isLocal($notice)) {
                require_once INSTALLDIR . '/lib/facebookutil.php';
                return facebookBroadcastNotice($notice);
            }
            break;
         case 'sms':
            require_once(INSTALLDIR.'/lib/mail.php');
            mail_broadcast_notice_sms($notice);
            break;
         case 'jabber':
            require_once(INSTALLDIR.'/lib/jabber.php');
            jabber_broadcast_notice($notice);
            break;
         default:
            throw ServerException("UnQueueManager: Unknown queue: $type");
        }
    }

    function _isLocal($notice)
    {
        return true;
    }
}