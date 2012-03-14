<?php
/**
 * ShaiShai, the distributed microblogging tool
 *
 * Base class for API actions that require "bare auth". Bare auth means
 * authentication is required only if the action is called without an argument
 * or query param specifying user id.
 *
 * PHP version 5
 *
 * @category  API
 * @package   ShaiShai
 * @copyright 2009 ShaiShai, Inc.
 */

if (!defined('SHAISHAI')) {
    exit(1);
}

require_once INSTALLDIR.'/lib/apiauth.php';

/**
 * Actions extending this class will require auth unless a target
 * user ID has been specified
 *
 * @category API
 * @package  ShaiShai
 */

class ApiBareAuthAction extends ApiAuthAction
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
        return true;
    }

    /**
     * Does this API resource require authentication?
     *
     * @return boolean true or false
     */

    function requiresAuth()
    {
        // If the site is "private", all API methods except ShaiShai/config
        // need authentication

        if (common_config('site', 'private')) {
            return true;
        }

        // check whether a user has been specified somehow

        $id           = $this->arg('id');
        $user_id      = $this->arg('user_id');
        $screen_name  = $this->arg('screen_name');

        if (empty($id) && empty($user_id) && empty($screen_name)) {
            return true;
        }

        return false;
    }

}