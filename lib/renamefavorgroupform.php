<?php
/**
 * Shaishai, the distributed microblog
 *
 * Form for deleting a notice
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

require_once INSTALLDIR.'/lib/form.php';

/**
 * Form for deleting a notice
 *
 * @category Personal
 * @package  Shaishai
 * @author    Guofu Xie <guofu85@gmail.com>
 * @copyright 2009-2010 Shaier, Inc.
 * @link      http://www.shaishai.com/
 */

class RenamefavorgroupForm extends Form
{
    /**
     * Notice to favor
     */

    var $notice = null;

    /**
     * Constructor
     *
     * @param HTMLOutputter $out    output channel
     * @param Notice        $notice notice to favor
     */

    function __construct($out=null, $notice=null)
    {
        parent::__construct($out);

        $this->notice = $notice;
    }

    /**
     * ID of the form
     *
     * @return int ID of the form
     */

    function id()
    {
        return 'rename-' . $this->notice->id;
    }

    /**
     * Action of the form
     *
     * @return string URL of the action
     */

    function action()
    {
        return common_path('main/renamefavorgroup');
    }

    /**
     * Include a session token for CSRF protection
     *
     * @return void
     */

    function sessionToken()
    {
        $this->out->hidden('token-' . $this->notice->id,
                           common_session_token());
    }


    /**
     * Legend of the Form
     *
     * @return void
     */
    function formLegend()
    {
        $this->out->element('legend', null, '收藏夹重命名');
    }


    /**
     * Data elements
     *
     * @return void
     */

    function formData()
    {
        $this->out->hidden('favorgroup-n'.$this->notice->id,
                           $this->notice->id,
                           'favorgroup');
    }

    /**
     * Action elements
     *
     * @return void
     */

    function formActions()
    {
        //$this->out->submit('renamefavorgroup-submit-' . $this->notice->id, '重命名收藏夹', 'submit', null, '重命名收藏夹');
        $this->out->element('a',array('id' => 'renamefavorgroup-' . $this->notice->id, 'class' => 'renamefavorgroup',
        					'href' => '#'), '重命名收藏夹'); //, 'onclick' => 'RenameFavorGroup(this)'
    }
    
    /**
     * Class of the form.
     *
     * @return string the form's class
     */
    
    function formClass()
    {
        return 'form_renamefavorgroup';
    }
}
