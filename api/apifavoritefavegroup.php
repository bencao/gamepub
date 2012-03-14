<?php
/**
 * ShaiShai, the distributed microblogging tool
 *
 * Add a notice to a user's list of favorite notices via the API
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
 * Favorites the status specified in the ID parameter as the authenticating user.
 * Returns the favorite status when successful.
 *
 * @category  API
 * @package   Shaishai
 * @author    Guofu Xie <guofu85@gmail.com>
 * @copyright 2009-2010 Shaier, Inc.
 * @link      http://www.shaishai.com/
 */

class ApiFavoriteFaveGroupAction extends ApiAuthAction
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

//        $this->user   = $this->auth_user;
   		$this->user = $this->getTargetUser($this->arg('id'));

        if (empty($this->user)) {
            $this->clientError('无此用户!', 404, $this->format);
            return false;
        }

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

		$favegroup = Fave_group::getFaveGroup($this->user->id);
		
        if ($this->format == 'xml') {
            $this->showXmlFaveGroup($favegroup);
        } elseif ($this->format == 'json') {
            $this->showJsonFaveGroup($favegroup);
        }
    }

}
