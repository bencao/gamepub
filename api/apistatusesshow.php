<?php
/**
 * ShaiShai, the distributed microblogging tool
 *
 * Show a notice (as a Twitter-style status)
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
 * Returns the notice specified by id as a Twitter-style status and inline user
 *
 * @category  API
 * @package   Shaishai
 * @author    Guofu Xie <guofu85@gmail.com>
 * @copyright 2009-2010 Shaier, Inc.
 * @link      http://www.shaishai.com/
 */

class ApiStatusesShowAction extends ApiAction
{

    var $notice_id = null;
    var $notice    = null;

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

        // 'id' is an undocumented parameter in Twitter's API. Several
        // clients make use of it, so we support it too.

        // show.json?id=12345 takes precedence over /show/12345.json

        $this->notice_id = (int)$this->trimmed('id');

        if (empty($notice_id)) {
            $this->notice_id = (int)$this->arg('id');
        }

        $this->notice = Notice::staticGet((int)$this->notice_id);

        return true;
    }

    /**
     * Handle the request
     *
     * Check the format and show the notice
     *
     * @param array $args $_REQUEST data (unused)
     *
     * @return void
     */

    function handle($args)
    {
        parent::handle($args);

        if (!in_array($this->format, array('xml', 'json'))) {
            $this->clientError('API方法未找到!', $code = 404);
            return;
        }

        $this->showNotice();
    }

    /**
     * Show the notice
     *
     * @return void
     */

    function showNotice()
    {
        if (!empty($this->notice)) {
            if ($this->format == 'xml') {
                $this->showSingleXmlStatus($this->notice);
            } elseif ($this->format == 'json') {
                $this->show_single_json_status($this->notice);
            }
        } else {

            // XXX: Twitter just sets a 404 header and doens't bother
            // to return an err msg

            $deleted = Deleted_notice::staticGet($this->notice_id);

            if (!empty($deleted)) {
                $this->clientError('状态已被删除.',
                    410,
                    $this->format
                );
            } else {
                $this->clientError('没有找到此ID的信息.',
                    404,
                    $this->format
                );
            }
        }
    }

    /**
     * Is this action read only?
     *
     * @param array $args other arguments
     *
     * @return boolean true
     */

    function isReadOnly($args)
    {
        return true;
    }

    /**
     * When was this notice last modified?
     *
     * @return string datestamp of the latest notice in the stream
     */

    function lastModified()
    {
        if (!empty($this->notice)) {
            return strtotime($this->notice->created);
        }

        return null;
    }

    /**
     * An entity tag for this notice
     *
     * Returns an Etag based on the action name, language, and
     * timestamps of the notice
     *
     * @return string etag
     */

    function etag()
    {
        if (!empty($this->notice)) {

            return '"' . implode(
                ':',
                array($this->arg('action'),
//                      common_language(),
                      $this->notice->id,
                      strtotime($this->notice->created))
            )
            . '"';
        }

        return null;
    }

}
