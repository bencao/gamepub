<?php
/**
 * ShaiShai, the distributed microblogging tool
 *
 * Send a direct message via the API
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
 * Creates a new direct message from the authenticating user to
 * the user specified by id.
 *
 * @category  API
 * @package   Shaishai
 * @author    Guofu Xie <guofu85@gmail.com>
 * @copyright 2009-2010 Shaier, Inc.
 * @link      http://www.shaishai.com/
 */

class ApiDirectMessageNewAction extends ApiAuthAction
{
    var $source  = null;
    var $other   = null;
    var $content = null;

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

        $this->user = $this->auth_user;

        if (empty($this->user)) {
            $this->clientError('无此用户!', 404, $this->format);
            return;
        }

        $this->source = $this->trimmed('source'); // Not supported by Twitter.

        $reserved_sources = array('web', 'mail', 'xmpp', 'api');
        if (empty($thtis->source) || in_array($this->source, $reserved_sources)) {
            $source = 'api';
        }

        $this->content = $this->trimmed('text');

        $this->user  = $this->auth_user;

        $user_param  = $this->trimmed('user');
        $user_id     = $this->arg('user_id');
        $screen_name = $this->trimmed('screen_name');

        if (isset($user_param) || isset($user_id) || isset($screen_name)) {
            $this->other = $this->getTargetUser($user_param);
        }

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
        
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            $this->clientError(
                '本方法需要POST.',
                400,
                $this->format
            );
            return;
        }

        if (empty($this->content)) {
            $this->clientError(
                '站内信为空!',
                406,
                $this->format
            );
        } else {
            $content_shortened = common_shorten_links($this->content);
            if (Message::contentTooLong($content_shortened)) {
                $this->clientError(
                    sprintf(
                        '站内信太长, 最大为%d个字符.',
                        Message::maxContent()
                    ),
                    406,
                    $this->format
                );
                return;
            }
        }
        
        if (empty($this->other)) {
            $this->clientError('接收站内信的用户不存在.', 403, $this->format);
            return;
        } else if (!$this->other->isSubscribed($this->user)) { //subscribedOrSubscriber
            $this->clientError(
                '不能发送站内信给不是您好友的人.',
                403,
                $this->format
            );
            return;
        } else if ($this->user->id == $this->other->id) {

            // Note: sending msgs to yourself is allowed by Twitter

            $errmsg = '不能发送站内信给您自己.';

            $this->clientError($errmsg, 403, $this->format);
            return;
        }
        
        $message = Message::saveNew(
            $this->user->id,
            $this->other->id,
            html_entity_decode($this->content, ENT_NOQUOTES, 'UTF-8'),
            $this->source
        );

        if (is_string($message)) {
            $this->serverError($message);
            return;
        }

//        mail_notify_message($message, $this->user, $this->other);

        if ($this->format == 'xml') {
            $this->showSingleXmlDirectMessage($message);
        } elseif ($this->format == 'json') {
            $this->showSingleJsondirectMessage($message);
        }
    }

}

