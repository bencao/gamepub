<?php
/**
 * Shaishai, the distributed microblog
 *
 * Sets which device Twitter delivers updates to for the authenticating user. 
 *
 * PHP version 5
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
 * Sets which channel (device) StatusNet delivers updates to for
 * the authenticating user. Sending none as the device parameter
 * will disable IM and/or SMS updates.
 *
 * @category  API
 * @package   Shaishai
 * @author    Guofu Xie <guofu85@gmail.com>
 * @copyright 2009-2010 Shaier, Inc.
 * @link      http://www.shaishai.com/
 */

class ApiAccountUpdateDeliveryDeviceAction extends ApiAuthAction
{
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

        $this->user   = $this->auth_user;
        $this->device = $this->trimmed('device');

        return true;
    }

    /**
     * Handle the request
     *
     * See which request params have been set, and update the user settings
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
                '此方法需要POST.',
                400, $this->format
            );
            return;
        }

        if (!in_array($this->format, array('xml', 'json'))) {
            $this->clientError(
                'API方法未找到!',
                404,
                $this->format
            );
            return;
        }

        // Note: Twitter no longer supports IM

        if (!in_array(strtolower($this->device), array('sms', 'im', 'none'))) {
            $this->clientError( '需要参数名为 device, 值为sms, im或者none' );
            return;
        }

        if (empty($this->user)) {
            $this->clientError('无此用户!',  404, $this->format);
            return;
        }

        $original = clone($this->user);

        //现在没有这两个参数?
        if (strtolower($this->device) == 'sms') {
            $this->user->smsnotify = true;
        } elseif (strtolower($this->device) == 'im') {
            $this->user->jabbernotify = true;
        } elseif (strtolower($this->device == 'none')) {
            $this->user->smsnotify    = false;
            $this->user->jabbernotify = false;
        }

        $result = $this->user->update($original);

        if ($result === false) {
            common_log_db_error($this->user, 'UPDATE', __FILE__);
            $this->serverError('不能更新此用户.');
            return;
        }

        $profile = $this->user->getProfile();

        $twitter_user = $this->twitterUserArray($profile, true);

        // Note: this Twitter API method is retarded because it doesn't give
        // any success/failure information. Twitter's docs claim that the
        // notification field will change to reflect notification choice,
        // but that's not true; notification> is used to indicate
        // whether the auth user is following the user in question.

        if ($this->format == 'xml') {
            $this->initDocument('xml');
            $this->showTwitterXmlUser($twitter_user);
            $this->endDocument('xml');
        } elseif ($this->format == 'json') {
            $this->initDocument('json');
            $this->showJsonObjects($twitter_user);
            $this->endDocument('json');
        }
    }

}
