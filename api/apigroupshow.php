<?php
/**
 * ShaiShai, the distributed microblogging tool
 *
 * Show information about a group
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
 * Outputs detailed information about the group specified by ID
 *
 * @category  API
 * @package   Shaishai
 * @author    Guofu Xie <guofu85@gmail.com>
 * @copyright 2009-2010 Shaier, Inc.
 * @link      http://www.shaishai.com/
 */

class ApiGroupShowAction extends ApiAction
{
    var $group = null;

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

        $this->group = $this->getTargetGroup($this->arg('id'));

        return true;
    }

    /**
     * Handle the request
     *
     * Check the format and show the user info
     *
     * @param array $args $_REQUEST data (unused)
     *
     * @return void
     */

    function handle($args)
    {
        parent::handle($args);

        if (empty($this->group)) {
            $this->clientError(
                '未找到相应的组!',
                404,
                $this->format
            );
            return;
        }

        switch($this->format) {
        case 'xml':
            $this->showSingleXmlGroup($this->group);
            break;
        case 'json':
            $this->showSingleJsonGroup($this->group);
            break;
        default:
            $this->clientError('API方法未找到!', 404, $this->format);
            break;
        }

    }

    /**
     * When was this group last modified?
     *
     * @return string datestamp of the latest notice in the stream
     */

    function lastModified()
    {
        if (!empty($this->group)) {
            return strtotime($this->group->modified);
        }

        return null;
    }

    /**
     * An entity tag for this group
     *
     * Returns an Etag based on the action name, language, and
     * timestamps of the notice
     *
     * @return string etag
     */

    function etag()
    {
        if (!empty($this->group)) {

            return '"' . implode(
                ':',
                array($this->arg('action'),
//                      common_language(),
                      $this->group->id,
                      strtotime($this->group->modified))
            )
            . '"';
        }

        return null;
    }

}
