<?php
/**
 * LShai, the distributed microblogging tool
 *
 * Base class for forms
 *
 * PHP version 5
 *
 */

if (!defined('SHAISHAI')) {
    exit(1);
}

require_once INSTALLDIR.'/lib/widget.php';

/**
 * Base class for forms
 *
 * We have a lot of common forms (subscribe, fave, delete) and this superclass
 * lets us abstract out the basic features of the form.
 *
 * @see      HTMLOutputter
 */

class Form extends Widget
{
    var $enctype = null;

    /**
     * Show the form
     *
     * Uses a recipe to output the form.
     *
     * @return void
     * @see Widget::show()
     */

    function show()
    {
        $attributes = array('id' => $this->id(),
            'class' => $this->formClass(),
            'method' => 'post',
            'action' => $this->action());

        if (!empty($this->enctype)) {
            $attributes['enctype'] = $this->enctype;
        }
        $this->out->elementStart('form', $attributes);
        $this->out->elementStart('fieldset');
        $this->formLegend();
        $this->formData();
        $this->formActions();
        $this->sessionToken();
        $this->out->elementEnd('fieldset');
        $this->out->elementEnd('form');
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
     * Name of the form
     *
     * Sub-classes should overload this with the name of their form.
     *
     * @return void
     */

    function formLegend()
    {
    }

    /**
     * Visible or invisible data elements
     *
     * Display the form fields that make up the data of the form.
     * Sub-classes should overload this to show their data.
     *
     * @return void
     */

    function formData()
    {
    }

    /**
     * Buttons for form actions
     *
     * Submit and cancel buttons (or whatever)
     * Sub-classes should overload this to show their own buttons.
     *
     * @return void
     */

    function formActions()
    {
    }

    /**
     * ID of the form
     *
     * Should be unique on the page. Sub-classes should overload this
     * to show their own IDs.
     *
     * @return int ID of the form
     */

    function id()
    {
        return null;
    }

    /**
     * Action of the form.
     *
     * URL to post to. Should be overloaded by subclasses to give
     * somewhere to post to.
     *
     * @return string URL to post to
     */

    function action()
    {
    }

    /**
     * Class of the form.
     *
     * @return string the form's class
     */

    function formClass()
    {
        return 'form';
    }
}
