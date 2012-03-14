<?php
/**
 */

if (!defined('SHAISHAI')) {
    exit(1);
}

require_once INSTALLDIR.'/lib/form.php';

/**
 * Form for subscribing to a user
 *
 * @category Form
 * @package  LShai
 * 
 * @see      UnsubscribeForm
 */

class SubscribeForm extends Form
{
    /**
     * Profile of user to subscribe to
     */

    var $profile = null;

    /**
     * Constructor
     *
     * @param HTMLOutputter $out     output channel
     * @param Profile       $profile profile of user to subscribe to
     */

    function __construct($out=null, $profile=null)
    {
        parent::__construct($out);

        $this->profile = $profile;
    }

    /**
     * ID of the form
     *
     * @return int ID of the form
     */

    function id()
    {
        return 'subscribe-' . $this->profile->id;
    }


    /**
     * class of the form
     *
     * @return string of the form class
     */

    function formClass()
    {
        return 'form_user_subscribe';
    }


    /**
     * Action of the form
     *
     * @return string URL of the action
     */

    function action()
    {
        return common_path('main/subscribe');
    }


    /**
     * Legend of the Form
     *
     * @return void
     */
    function formLegend()
    {
        $this->out->element('legend', null, '关注此用户');
    }

    /**
     * Data elements of the form
     *
     * @return void
     */

    function formData()
    {
        $this->out->hidden('subscribeto-' . $this->profile->id,
                           $this->profile->id,
                           'subscribeto');
    }

    /**
     * Action elements
     *
     * @return void
     */

    function formActions()
    {
		  $this->out->element('input', array('class' => 'submit button94 orange94', 'type' => 'submit', 'value' => '关注', 'title' => '关注此用户'));
    }
}
