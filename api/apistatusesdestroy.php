<?php
/**
 * ShaiShai, the distributed microblogging tool
 *
 * Destroy a notice through the API
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

require_once INSTALLDIR . '/lib/apiauth.php';

/**
 * Deletes one of the authenticating user's statuses (notices).
 *
 * @category  API
 * @package   Shaishai
 * @author    Guofu Xie <guofu85@gmail.com>
 * @copyright 2009-2010 Shaier, Inc.
 * @link      http://www.shaishai.com/
 */

class ApiStatusesDestroyAction extends ApiAuthAction
{
    var $status                = null;

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

        $this->user = $this->auth_user;
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
     * Delete the notice and all related replies
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

         if (!in_array($_SERVER['REQUEST_METHOD'], array('POST', 'DELETE'))) {
             $this->clientError('此方法需要POST或DELETE.',
                 400, $this->format);
             return;
         }

         if (empty($this->notice)) {
             $this->clientError('没有找到此ID的信息.',
                 404, $this->format);
             return;
         }

         if ($this->user->id == $this->notice->user_id) {
//             $replies = new Reply;
//             $replies->get('notice_id', $this->notice_id);
//             $replies->delete();
             $this->notice->delete();

             $msg = '删除成功。';
             if ($this->format == 'xml') {
                 //$this->showSingleXmlStatus($this->notice);
                $this->initDocument('xml');
		        $this->elementStart('hash');
	            $this->element('tip', null, $msg);
	            $this->elementEnd('hash');
		        $this->endDocument('xml');
             } elseif ($this->format == 'json') {
                 $this->initDocument('json');
	            $error_array = array('tip' => $msg);
	            print(json_encode($error_array));
	            $this->endDocument('json');
             }
         } else {
             $this->clientError('您不能删除其他用户的信息.',
                 403, $this->format);
         }

        $this->showNotice();
    }

    /**
     * Show the deleted notice
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
        }
    }

}
