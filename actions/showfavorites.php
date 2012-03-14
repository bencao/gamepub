<?php
/**
 * Shaishai, the distributed microblog
 *
 * List of favorites
 *
 * PHP version 5
 *
 * @category  Login
 * @package   Shaishai
 * @author    Guofu Xie <guofu85@gmail.com>
 * @copyright 2009-2010 Shaier, Inc.
 * @link      http://www.shaishai.com/
 */

if (!defined('SHAISHAI')) {
    exit(1);
}

/**
 * List of favorites
 *
 * @category Personal
 * @package  Shaishai
 * @author    Guofu Xie <guofu85@gmail.com>
 * @copyright 2009-2010 Shaier, Inc.
 * @link      http://www.shaishai.com/
 */

//自己的收藏, 显示收藏夹
class ShowfavoritesAction extends OwnerDesignAction
{ 
   /** User we're getting the faves of */
    //var $user = null;
    /** Page of the faves we're on */
    var $page = null;
    
    var $fave_user = null;

    /**
     * Is this a read-only page?
     *
     * @return boolean true
     */

    function isReadOnly($args)
    {
        return true;
    }
    
    /**
     * Prepare the object
     *
     * Check the input values and initialize the object.
     * Shows an error page on bad input.
     *
     * @param array $args $_REQUEST data
     *
     * @return boolean success flag
     */

    function prepare($args)
    {
        if (! parent::prepare($args)) {return false;}

        $uname = strtolower($this->arg('uname'));

        $this->fave_user = User::staticGet('uname', $uname);
        $fav_profile = $this->fave_user->getProfile();

        if (!$this->fave_user) {
            $this->clientError('无此用户。');
            return false;
        }
        
        //是否公开收藏
        $cur = common_current_user();
        if ($cur->id != $this->fave_user->id && $fave_profile->sharefavorites == 0) {
        	$this->clientError('此用户的收藏夹设置为隐藏, 您不能查看他的收藏夹。');
            return false;
        }

        common_set_returnto($this->selfUrl());

        return true;
    }

    /**
     * Handle a request
     *
     * Just show the page. All args already handled.
     *
     * @param array $args $_REQUEST data
     *
     * @return void
     */

    function handle($args)
    {
        parent::handle($args);      	
        $fave_group = $this->fave_user->getFaveGroup();
        $this->addPassVariable('fave_group', $fave_group);
		$this->addPassVariable('user', $this->fave_user);
		$this->displayWith('ShowFavoritesHTMLTemplate');      
    }
    
}