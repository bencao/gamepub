<?php
/**
 * ShaiShai, the distributed open-source microblogging tool
 *
 * A version stamp for the API
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
 * Returns a version number for this version of ShaiShai, which
 * should make things a bit easier for upgrades.
 * URL: http://identi.ca/api/ShaiShai/version.(xml|json)
 * Formats: xml, js
 *
 * @category  API
 * @package   Shaishai
 * @author    Guofu Xie <guofu85@gmail.com>
 * @copyright 2009-2010 Shaier, Inc.
 * @link      http://www.shaishai.com/
 */

class ApiShaiShaiVersionAction extends ApiAction
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

        switch ($this->format) {
        case 'xml':
            $this->initDocument('xml');
            $this->element('version', null, SHAISHAI_VERSION);
            $this->endDocument('xml');
            break;
        case 'json':
            $this->initDocument('json');
            print '"'.SHAISHAI_VERSION.'"';
            $this->endDocument('json');
            break;
        default:
            $this->clientError(
                'API方法未找到!',
                404,
                $this->format
            );
            break;
        }
    }

}

