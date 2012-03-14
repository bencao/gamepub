<?php
/**
 * Shaishai, the distributed microblog
 *
 * Show user all notices
 *
 * PHP version 5
 *
 * @category  Login
 * @package   Shaishai
 * @author    Guofu Xie <guofu85@gmail.com>
 * @copyright 2009-2010 Shaier, Inc.
 * @link      http://www.shaishai.com/
 */

if (!defined('SHAISHAI')) {
    exit(1);
}

/**
 * Show user all notices
 *
 * @category Personal
 * @package  Shaishai
 * @author    Guofu Xie <guofu85@gmail.com>
 * @copyright 2009-2010 Shaier, Inc.
 * @link      http://www.shaishai.com/
 */


if (!defined('SHAISHAI')) {
    exit(1);
}

require_once INSTALLDIR.'/lib/noticelist.php';

class NoticeUnreadList extends Widget
{
    /** the current stream of notices being displayed. */

    var $notice = null;

    public $unreadsub_count = 0;
    /**
     * constructor
     *
     * @param Notice $notice stream of notices from DB_DataObject
     */

    function __construct($notice, $out=null)
    {
        parent::__construct($out);
        $this->notice = $notice;
    }

    /**
     * show the list of notices
     *
     * "Uses up" the stream by looping through it. So, probably can't
     * be called twice on the same list.
     *
     * @return int count of notices listed.
     */

    function show()
    {
        $this->out->elementStart('div', array('id' =>'notices_primary'));
        $this->out->element('h2', null, '消息');
        $this->out->elementStart('ol', array('class' => 'notices xoxo'));

        $cnt = 1;
		$this->notice->fetch();
		$user_id = $this->notice->user_id;
		
        while ($cnt <= NOTICES_PER_PAGE) {
            $cnt++;

            if ($cnt > NOTICES_PER_PAGE) {
                break;
            }
            $this->unreadsub_count++;
            $user_id = $this->notice->user_id;
            $item = $this->newUnreadListItem($this->notice, 0);
            $item->show();
            while ($this->notice->fetch() && $this->notice->user_id == $user_id) {
            	$item = $this->newUnreadListItem($this->notice, 1);
            	$item->show();
            }
        }

        $this->out->elementEnd('ol');
        $this->out->elementEnd('div');

        return $cnt;
    }

    /**
     * returns a new list item for the current notice
     *
     * Recipe (factory?) method; overridden by sub-classes to give
     * a different list item class.
     *
     * @param Notice $notice the current notice
     *
     * @return NoticeListItem a list item for displaying the notice
     */

    function newUnreadListItem($notice, $is_read)
    {
        return new NoticeUnreadListItem($notice, $this->out, $is_read);
    }
}

/**
 * widget for displaying a single notice
 *
 * This widget has the core smarts for showing a single notice: what to display,
 * where, and under which circumstances. Its key method is show(); this is a recipe
 * that calls all the other show*() methods to build up a single notice. The
 * ProfileNoticeListItem subclass, for example, overrides showAuthor() to skip
 * author info (since that's implicit by the data in the page).
 *
 * @category UI
 * @package  LShai
 * @see      NoticeList
 * @see      ProfileNoticeListItem
 */

class NoticeUnreadListItem extends NoticeListItem
{   
    var $is_read = 0;

    /**
     * constructor
     *
     * Also initializes the profile attribute.
     *
     * @param Notice $notice The notice we'll display
     */

    function __construct($notice, $out=null, $is_read)
    {
        parent::__construct($notice, $out);
        $this->is_read = $is_read;
    }

    /**
     * recipe function for displaying a single notice.
     *
     * This uses all the other methods to correctly display a notice. Override
     * it or one of the others to fine-tune the output.
     *
     * @return void
     */

    function show()
    {
        $this->showStart();
        $this->showNotice();
        $this->showNoticeInfo();
        $this->showNoticeOptions();
        $this->showEnd();
    }

    function showNoticeOptions()
    {
        $user = common_current_user();
        if ($user) {
            $this->out->elementStart('div', 'notice-options');
            $this->showFaveForm();
            $this->showReplyLink();
            $this->showDeleteLink();
            if($this->is_read == 1)
            	$this->showMoreUnread();
            $this->out->elementEnd('div');
        }
    }

    /**
     * start a single notice.
     *
     * @return void
     */

    function showStart()
    {
        // XXX: RDFa
        // TODO: add notice_type class e.g., notice_video, notice_image
        if($this->is_read == 0){
        	$this->out->elementStart('li', array('class' => 'hentry notice readprofile-' . $this->profile->id,
                                             'id' => 'notice-' . $this->notice->id,
        									 'style' => 'display:none'));
        } else {
        	$this->out->elementStart('li', array('class' => 'hentry notice',
                                             'id' => 'notice-' . $this->notice->id));
        }
    }
    
    //传入某个用户名, 然后设置其已读
    function showMoreUnread() {
            if (common_current_user()) {
            $this->out->elementStart('dl', 'read_more_notice');
            $this->out->element('dt', null, '更多');
            $this->out->elementStart('dd');
            $this->out->elementStart('a', array('href' => '#',
                                                'title' => '更多'));
            $this->out->text('更多');
            $this->out->element('span', 'subed_id', $this->profile->id);
            $this->out->elementEnd('a');
            $this->out->elementEnd('dd');
            $this->out->elementEnd('dl');
        }
    }
}
