<?php
/**
 * ShaiShai, the distributed microblogging tool
 *
 * Test that you can connect to the API
 *
 * @category  API
 * @package   Shaishai
 * @author    Guofu Xie <guofu85@gmail.com>
 * @copyright 2009-2010 Shaier, Inc.
 * @link      http://www.shaishai.com/
 */

if (!defined('SHAISHAI')) {
    exit(1);
}

require_once INSTALLDIR . '/lib/api.php';

/**
 * Returns the string "ok" in the requested format with a 200 OK HTTP status code.
 *
 * @category  API
 * @package   Shaishai
 * @author    Guofu Xie <guofu85@gmail.com>
 * @copyright 2009-2010 Shaier, Inc.
 * @link      http://www.shaishai.com/
 */

class ApiHelpTestAction extends ApiAction
{

    /**
     * Take arguments for running
     *
     * @param array $args $_REQUEST args
     *
     * @return boolean success flag
     *
     */

    function prepare($args)
    {
        if (! parent::prepare($args)) {return false;}
        return true;
    }

    /**
     * Handle the request
     *
     * @param array $args $_REQUEST data (unused)
     *
     * @return void
     */

    function handle($args)
    {
        parent::handle($args);

        if ($this->format == 'xml') {
            $this->initDocument('xml');
            $this->element('ok', null, 'true');
            $this->endDocument('xml');
        } elseif ($this->format == 'json') {
            $this->initDocument('json');
            print '"ok"';
            $this->endDocument('json');
        } else {
            $this->clientError('API方法未找到',
                404,
                $this->format
            );
        }
    }

}

