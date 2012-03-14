<?php
/**
 * ShaiShai, the distributed microblogging tool
 *
 * Dump of configuration variables
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
 * Gives a full dump of configuration variables for this instance
 * of ShaiShai, minus variables that may be security-sensitive (like
 * passwords).
 * Formats: xml, json
 *
 * @category  API
 * @package   Shaishai
 * @author    Guofu Xie <guofu85@gmail.com>
 * @copyright 2009-2010 Shaier, Inc.
 * @link      http://www.shaishai.com/
 */

class ApiShaiShaiConfigAction extends ApiAction
{
    var $keys = array(
        'site' => array('name', 'server', 'theme', 'path', 'fancy', 'language',
                        'email', 'broughtby', 'broughtbyurl', 'closed',
                        'inviteonly', 'private'),
        'license' => array('url', 'title', 'image'),
        'uname' => array('featured'),
        'throttle' => array('enabled', 'count', 'timespan'),
        'xmpp' => array('enabled', 'server', 'user')
    );

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
            $this->elementStart('config');

            // XXX: check that all sections and settings are legal XML elements

//            common_debug(var_export($this->keys, true));

            foreach ($this->keys as $section => $settings) {
                $this->elementStart($section);
                foreach ($settings as $setting) {
                    $value = common_config($section, $setting);
                    if (is_array($value)) {
                        $value = implode(',', $value);
                    } else if ($value === false) {
                        $value = 'false';
                    } else if ($value === true) {
                        $value = 'true';
                    }
                    $this->element($setting, null, $value);
                }
                $this->elementEnd($section);
            }
            $this->elementEnd('config');
            $this->endDocument('xml');
            break;
        case 'json':
            $result = array();
            foreach ($this->keys as $section => $settings) {
                $result[$section] = array();
                foreach ($settings as $setting) {
                    $result[$section][$setting]
                        = common_config($section, $setting);
                }
            }
            $this->initDocument('json');
            $this->showJsonObjects($result);
            $this->endDocument('json');
            break;
        default:
            $this->clientError('API方法未找到!',
                404,
                $this->format
            );
            break;
        }
    }

}

