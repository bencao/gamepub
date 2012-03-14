<?php
/**
 * ShaiShai, the distributed microblogging tool
 *
 * Show a the direct messages from or to a user
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
 * Show a list of direct messages from or to the authenticating user
 *
 * @category  API
 * @package   Shaishai
 * @author    Guofu Xie <guofu85@gmail.com>
 * @copyright 2009-2010 Shaier, Inc.
 * @link      http://www.shaishai.com/
 */

class ApiDirectMessageAction extends ApiAuthAction
{
    var $messages     = null;
    var $title        = null;
    var $subtitle     = null;
    var $link         = null;
    var $selfuri_base = null;
    var $id           = null;

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

        if (empty($this->user)) {
            $this->clientError('无此用户!', 404, $this->format);
            return;
        }

        $server   = common_path('');
        $taguribase = common_config('integration', 'taguri');

        if ($this->arg('sent') == 'true') {

            // Action was called by /api/direct_messages/sent.format

            $this->title = sprintf(
                "来自 %s的站内信",
                $this->user->uname
            );
            $this->subtitle = sprintf(
                "来自%s的站内信",
                $this->user->uname
            );
            $this->link = $server . $this->user->uname . '/outbox';
            $this->selfuri_base = common_path('') . 'api/direct_messages/sent';
            $this->id = "tag:$taguribase:SentDirectMessages:" . $this->user->id;
        } else {
			$title = sprintf("发给%s的站内信", $this->user->uname);
            $subtitle = sprintf("发给%s的站内信",
                $this->user->uname);
            $this->link = $server . $this->user->uname . '/inbox';
            $this->selfuri_base = common_path('') . 'api/direct_messages';
            $this->id = "tag:$taguribase:DirectMessages:" . $this->user->id;
        }

        $this->messages = $this->getMessages();

        return true;
    }

    /**
     * Handle the request
     *
     * Show the messages
     *
     * @param array $args $_REQUEST data (unused)
     *
     * @return void
     */

    function handle($args)
    {
        parent::handle($args);
        $this->showMessages();
    }

    /**
     * Show the messages
     *
     * @return void
     */

    function showMessages()
    {
        switch($this->format) {
        case 'xml':
            $this->showXmlDirectMessages();
            break;
        case 'rss':
            $this->showRssDirectMessages();
            break;
        case 'atom':
            $this->showAtomDirectMessages();
            break;
        case 'json':
            $this->showJsonDirectMessages();
            break;
        default:
            $this->clientError('API方法未找到!', $code = 404);
            break;
        }
    }

    /**
     * Get notices
     *
     * @return array notices
     */

    function getMessages()
    {
        $message  = new Message();
        
        if ($this->arg('sent') == 'true') {
            $message->from_user = $this->user->id;
            $message->is_deleted_from = 0;
        } else {
            $message->to_user = $this->user->id;
            $message->is_deleted_to = 0;
        }

        if (!empty($this->max_id)) {
            $message->whereAdd('id <= ' . $this->max_id);
        }

        if (!empty($this->since_id)) {
            $message->whereAdd('id > ' . $this->since_id);
        }

        if (!empty($since)) {
            $d = date('Y-m-d H:i:s', $this->since);
            $message->whereAdd("created > '$d'");
        }

        $message->orderBy('created DESC, id DESC');
        $message->limit((($this->page - 1) * $this->count), $this->count);
        $message->find();

        $messages = array();

        while ($message->fetch()) {
            $messages[] = clone($message);
        }

        return $messages;
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
        if (!empty($this->messages)) {
            return strtotime($this->messages[0]->created);
        }

        return null;
    }

    /**
     * Shows a list of direct messages as Twitter-style XML array
     *
     * @return void
     */

    function showXmlDirectMessages()
    {
        $this->initDocument('xml');
        $this->elementStart('direct-messages', array('type' => 'array'));

        foreach ($this->messages as $m) {
            $dm_array = $this->directMessageArray($m);
            $this->showXmlDirectMessage($dm_array);
        }

        $this->elementEnd('direct-messages');
        $this->endDocument('xml');
    }

    /**
     * Shows a list of direct messages as a JSON encoded array
     *
     * @return void
     */

    function showJsonDirectMessages()
    {
        $this->initDocument('json');

        $dmsgs = array();

        foreach ($this->messages as $m) {
            $dm_array = $this->directMessageArray($m);
            array_push($dmsgs, $dm_array);
        }

        $this->showJsonObjects($dmsgs);
        $this->endDocument('json');
    }

    /**
     * Shows a list of direct messages as RSS items
     *
     * @return void
     */

    function showRssDirectMessages()
    {
        $this->initDocument('rss');

        $this->element('title', null, $this->title);

        $this->element('link', null, $this->link);
        $this->element('description', null, $this->subtitle);
        $this->element('language', null, 'en-us');

        $this->element(
            'atom:link',
            array(
                'type' => 'application/rss+xml',
                'href' => $this->selfuri_base . '.rss',
                'rel' => self
                ),
            null
        );
        $this->element('ttl', null, '40');

        foreach ($this->messages as $m) {
            $entry = $this->rssDirectMessageArray($m);
            $this->showTwitterRssItem($entry);
        }

        $this->endTwitterRss();
    }

    /**
     * Shows a list of direct messages as Atom entries
     *
     * @return void
     */

    function showAtomDirectMessages()
    {
        $this->initDocument('atom');

        $this->element('title', null, $this->title);
        $this->element('id', null, $this->id);

        $selfuri = common_path('') . 'api/direct_messages.atom';

        $this->element(
            'link', array(
            'href' => $this->link,
            'rel' => 'alternate',
            'type' => 'text/html'),
            null
        );
        $this->element(
            'link', array(
            'href' => $this->selfuri_base . '.atom', 'rel' => 'self',
            'type' => 'application/atom+xml'),
            null
        );
        $this->element('updated', null, common_date_iso8601('now'));
        $this->element('subtitle', null, $this->subtitle);

        foreach ($this->messages as $m) {
            $entry = $this->rssDirectMessageArray($m);
            $this->showTwitterAtomEntry($entry);
        }

        $this->endDocument('atom');
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
        if (!empty($this->messages)) {

            $last = count($this->messages) - 1;

            return '"' . implode(
                ':',
                array($this->arg('action'),
//                      common_language(),
                      strtotime($this->messages[0]->created),
                      strtotime($this->messages[$last]->created)
                )
            )
            . '"';
        }

        return null;
    }

}
