<?php
/**
 * LShai, the distributed microblogging tool
 *
 * Base class for UI widgets
 *
 * @category  Widget
 * @package   LShai
 */

if (!defined('SHAISHAI')) {
    exit(1);
}

/**
 * Base class for UI widgets
 *
 * A widget is a cluster of HTML elements that provide some functionality
 * that's used on different parts of the site. Examples would be profile
 * lists, notice lists, navigation menus (tabsets) and common forms.
 *
 * @category Widget
 * @package  LShai
 *
 * @see      HTMLOutputter
 */

class Widget
{
    /**
     * HTMLOutputter to use for output
     */

    var $out = null;

    /**
     * Prepare the widget for use
     *
     * @param HTMLOutputter $out output helper, defaults to null
     */

    function __construct($out=null)
    {
        $this->out = $out;
    }

    /**
     * Show the widget
     *
     * Emit the HTML for the widget, using the configured outputter.
     *
     * @return void
     */

    function show()
    {
    }
}
