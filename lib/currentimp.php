<?php
/**
 * LShai, the distributed microblogging tool
 *
 * Menu for user's infomation
 *
 * PHP version 5
 *
 * @category  Menu
 * @package   ShaiShai
 */

if (!defined('SHAISHAI')) {
    exit(1);
}

require_once INSTALLDIR.'/lib/widget.php';

/**
 * TODO: This is an temp navigation, Need to be removed after developing.
 */

class CurrentImp extends Widget
{
    var $action = null;

    /**
     * Construction
     *
     * @param Action $action current action, used for output
     */

    function __construct($action=null)
    {
        parent::__construct($action);
        $this->action = $action;
    }

    /**
     * Show the menu
     *
     * @return void
     */

    function show()
    {

        $this->action->elementStart('ul', array('class' => 'nav'));

        if (Event::handle('StartSiteResourceNav', array($this))) {
        	if (common_config('invite', 'enabled')) {
                    $this->out->menuItem(common_path('main/invite'),
                                    '邀请','邀请好友与您一起探索' . common_config('site', 'name'),
                                    false, 'nav_invitecontact');
                }
        }
            Event::handle('EndSiteResourceNav', array($this));
        //}
        $this->action->elementEnd('ul');
    }
}