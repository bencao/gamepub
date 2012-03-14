<?php
/**
 * Shaishai, the distributed microblog
 *
 * Form for deleting a favor group
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
 * Form for deleting a favor group
 *
 * @category Personal
 * @package  Shaishai
 * @author    Guofu Xie <guofu85@gmail.com>
 * @copyright 2009-2010 Shaier, Inc.
 * @link      http://www.shaishai.com/
 */

class DeletefavorgroupForm extends Form
{
   /**
     * Notice to favor
     */

    var $notice = null;
    
    var $action = null;

    /**
     * Constructor
     *
     * @param HTMLOutputter $out    output channel
     * @param Notice        $notice notice to favor
     */

    function __construct($out=null, $notice=null, $action=null)
    {
        parent::__construct($out);

        $this->notice = $notice;
        $this->action = $action;
    }

    /**
     * ID of the form
     *
     * @return int ID of the form
     */

    function id()
    {
        return 'delete-'. $this->notice->id;
    }

    /**
     * Action of the form
     *
     * @return string URL of the action
     */

    function action()
    {
        return common_local_url('delete' . $this->action);
    }

    /**
     * Include a session token for CSRF protection
     *
     * @return void
     */

    function sessionToken()
    {
        $this->out->hidden('token', common_session_token());
    }


    /**
     * Legend of the Form
     *
     * @return void
     */
    function formLegend()
    {
        $this->out->element('legend', null, '删除收藏夹');
    }


    /**
     * Data elements
     *
     * @return void
     */

    function formData()
    {
        $this->out->hidden('favorgroup',
                           $this->notice->id,
                           'favorgroup');
        //$this->out->element('a', array('href'=>'haha', 'id'=>'deletenotice'), 'delete');
    }

    /**
     * Action elements
     *
     * @return void
     */

    function formActions()
    {
        $this->out->submit('deletefavorgroup_action-yes','删除', 'submit', 'yes', '删除收藏夹');
    }
    
    /**
     * Class of the form.
     *
     * @return string the form's class
     */
    
    function formClass()
    {
        return 'form_deletefavorgroup';
    }
}