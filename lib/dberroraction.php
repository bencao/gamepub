<?php
/**
 * DB error action.
 *
 * PHP version 5
 *
 * @category Action
 * @package  LShai
 */

if (!defined('SHAISHAI')) {
    exit(1);
}

require_once INSTALLDIR.'/lib/servererror.php';

/**
 * Class for displaying DB Errors
 *
 * This only occurs if there's been a DB_DataObject_Error that's
 * reported through PEAR, so we try to avoid doing anything that connects
 * to the DB, so we don't trigger it again.
 *
 * @category Action
 * @package  LShai
 */

class DBErrorAction extends ServerErrorAction
{
    function __construct($message='错误', $code=500)
    {
        parent::__construct($message, $code);
    }

    function title()
    {
        return common_config('site', 'name') . '发生异常';
    }

    function getLanguage()
    {
        // Don't try to figure out user's language; just show the page
        return common_config('site', 'language');
    }

    function showPrimaryNav()
    {
        // don't show primary nav
    }
}
