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

class UserInfo extends Widget
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
        $user = common_current_user();

        $this->action->elementStart('ul', array('class' => 'nav'));

        if (Event::handle('StartUserInfoNav', array($this))) {
            $this->out->menuItem(common_path($user->uname . '/subscriptions'), '我关注的人',
                '我关注的人', $action_name == 'subscriptions', 'nav_subscriptions');

            $this->out->menuItem(common_path($user->uname . '/subscribers'), '关注我的人',
                '关注我的人', $action_name == 'subscribers', 'nav_subscribers');
            Event::handle('EndUserInfoNav', array($this));
        }
        $this->action->elementEnd('ul');
    }
}