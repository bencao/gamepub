<?php
/**
 * LShai, the distributed microblogging tool
 *
 * Form for posting a direct message
 *
 * @category  Form
 * @package   LShai
 */

if (!defined('SHAISHAI')) {
    exit(1);
}

require_once INSTALLDIR.'/lib/form.php';

/**
 * Form for posting a direct message
 *
 * @category Form
 * @package  LShai
 *
 * @see      HTMLOutputter
 */

class MessageForm extends Form
{
    /**
     * User to send a direct message to
     */

    var $to = null;

    /**
     * Pre-filled content of the form
     */

    var $content = null;

    /**
     * Constructor
     *
     * @param HTMLOutputter $out     output channel
     * @param User          $to      user to send a message to
     * @param string        $content content to pre-fill
     */

    function __construct($out=null, $to=null, $content=null)
    {
        parent::__construct($out);

        $this->to      = $to;
        $this->content = $content;
    }

    /**
     * ID of the form
     *
     * @return int ID of the form
     */

    function id()
    {
        return 'form_notice';
    }

    /**
     * Action of the form
     *
     * @return string URL of the action
     */

    function action()
    {
        return common_path('message/new');
    }

    /**
     * Legend of the Form
     *
     * @return void
     */
    function formLegend()
    {
        $this->out->element('legend', null, '发送站内信');
    }

    /**
     * Data elements
     *
     * @return void
     */

    function formData()
    {
        $user = common_current_user();

        //这里是互相关注的人
        $mutual_users = $user->mutuallySubscribedUsers();

        $mutual = array();

        while ($mutual_users->fetch()) {
            if ($mutual_users->id != $user->id) {
                $mutual[$mutual_users->id] = $mutual_users->uname;
            }
        }

        $mutual_users->free();
        unset($mutual_users);

        $this->out->dropdown('to', '发送站内信到', $mutual, null, false,
                             ($this->to) ? $this->to->id : null);

        $this->out->element('textarea', array('id' => 'notice_data-text',
                                            //  'cols' => 35,
                                              'rows' => 3,
                                              'name' => 'content'),
                            ($this->content) ? $this->content : '');
        $this->out->element('span', array('class' => 'mailbox_tip'), '您只能与您的互相关注的人发送悄悄话 :)');
        $this->out->elementStart('dl', 'form_note');
        $this->out->element('dt', null, '还可写');
        $this->out->element('dd', array('id' => 'notice_text-count'),
                            '280');
        $this->out->elementEnd('dl');
    }

    /**
     * Action elements
     *
     * @return void
     */

    function formActions()
    {
//        $this->out->element('input', array('id' => 'notice_action-submit',
//                                           'class' => 'submit',
//                                           'name' => 'message_send',
//                                           'type' => 'submit',
//                                           'value' => '发送'));
    	$this->out->elementStart('div', array('id' => 'submit'));
        $this->out->element('input', array('id' => 'notice_action-submit',
                                           'class' => 'submit',
                                           'name' => 'message_send',
                                           'type' => 'submit',
                                           'value' => ''));
        $this->out->elementEnd('div');        
    }
}
