<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

class NoticeList extends Widget
{
    /** the current stream of notices being displayed. */

    var $notice = null;
	
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
    	$this->out->elementStart('ol', array('id' => 'notices'));
        $cnt = 0;

        while ($this->notice != null 
        	&& $this->notice->fetch()) {
            $cnt++;
            
            if ($cnt > NOTICES_PER_PAGE) {
                break;
            }
                        
            $item = $this->newListItem($this->notice);
            $item->show();
        }
        
        if (0 == $cnt) {
            $this->out->showEmptyList();
        }
        
        $this->out->elementEnd('ol');
        
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

    function newListItem($notice)
    {
        return new NoticeListItem($notice, $this->out);
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

class NoticeListItem extends Widget
{
    /** The notice this item will show. */

    var $notice = null;

    /** The profile of the author of the notice, extracted once for convenience. */

    var $profile = null;
    
    var $profileUser = null;
    
    var $user = null;
    
    /**
     * constructor
     *
     * Also initializes the profile attribute.
     *
     * @param Notice $notice The notice we'll display
     */

    function __construct($notice, $out=null)
    {
        parent::__construct($out);
        $this->notice  = $notice;
        $this->profile = $notice->getProfile();
        $this->profileUser = $this->profile->getUser();
        $this->user = common_current_user();
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
        
    	// if it's personal notices page, images are not shown
    	// 用继承来实现不显示用户图片的效果
//        if (!$this->noImage) {
            $this->showImage();
//        }        
        
        $this->showNickname();
        
        $this->showNoticeInfo();
        $this->showRoot();
        $this->showNoticeBar();
		$this->showContext();
		
        $this->showEnd();
    }
    
    function showNickname() {
    	$this->out->elementStart('h3'); 
        $this->out->element('a', array('href' => common_path($this->profile->uname),
        			'class' => 'name', 'title' => '去' . $this->profile->nickname . '在' . common_config('site', 'name') . '的主页看看'), $this->profile->nickname);
        if ($this->profile->is_vip) {
        	$this->out->element('strong', null, 'V');
        }
        $this->out->elementEnd('h3');
    }

    function showImage() {
    	$this->out->elementStart('div', array('class' => 'avatar'));
    	
    	$avatar = $this->profile->getAvatar(AVATAR_STREAM_SIZE, AVATAR_STREAM_SIZE);
        $attrs = array('href' => $this->profile->profileurl);
        if (! empty($this->profile->nickname)) {
            $attrs['title'] = $this->profile->nickname . '在' . common_config('site', 'name') . '上的头像';
        }
    	$this->out->elementStart('a', $attrs);
    	$this->out->element('img', array('src' => ($avatar) ? $avatar->displayUrl() : Avatar::defaultImage(AVATAR_STREAM_SIZE, $this->profile->id, $this->profile->sex),
                                         'alt' => $this->profile->uname));
    	$this->out->elementEnd('a');
    	$this->out->elementEnd('div');
    }
    
    function showNoticeInfo()
    {
    	$this->out->elementStart('div', array('class' => 'content'));
    	$this->out->raw($this->notice->rendered);
    	$this->out->elementEnd('div');                                	
    }
    
	function showRoot()
    {
        // XXX: also show context if there are replies to this notice
        if (!empty($this->notice->retweet_from)
            && $this->notice->retweet_from != $this->notice->id) {

            $rootnotice = Notice::staticGet('id',$this->notice->retweet_from);
            if ($rootnotice) {
	            $rootprofile = $rootnotice->getProfile();
				$this->out->elementStart('blockquote',array('class' => 'rounded5', 'nid' => $rootnotice->id));
				$this->out->elementStart('h4');
				$this->out->element('a', array('href' => common_path($rootprofile->uname)), $rootprofile->nickname);
				$this->out->elementEnd('h4');
				$this->out->elementStart('div', 'c');
//				$this->out->elementStart('span');
				$this->out->raw($rootnotice->rendered);
//				$this->out->elementEnd('span');
				
				// 匿名的情况
				if ($this->user && $rootnotice->user_id != $this->user->id) {
					$this->out->elementStart('a', array('href' => common_path('notice/retweet'), 'title' => '转载',
		    			'nickname' => $rootprofile->nickname, 
		    			'class' => 'originretweet'));
					$this->out->raw('原文转载(<em>'.$rootnotice->retweet_num.'</em>)');
					$this->out->elementEnd('a');
					$this->out->text('|');
				}
				if ($this->user){
					$this->out->elementStart('a',array('target' => '_blank', 'class' => 'origindiscuss', 
						'href' => common_path('discussionlist/' . $rootnotice->id)));
					if($rootnotice->discussion_num)
						$this->out->raw('原文评论(<em>'.$rootnotice->discussion_num.'</em>)');
					else 
						$this->out->raw('原文评论(<em>0</em>)');
					$this->out->elementEnd('a');
            	}
				$this->out->elementEnd('div');
				$this->out->elementEnd('blockquote');
            } else {
            	//如果没有找到, 则在Deleted_notice里面查找
            	if(!$rootnotice)
            		$rootnotice = Deleted_notice::staticGet('id', $this->notice->retweet_from);
            	if($rootnotice) {
	            	$rootprofile = Profile::staticGet('id', $rootnotice->user_id);
					$this->out->elementStart('blockquote',array('class' => 'rounded5', 'nid' => $rootnotice->id));
					$this->out->elementStart('h4');
					$this->out->element('a', array('href' => common_path($rootprofile->uname)), $rootprofile->nickname);
					$this->out->elementEnd('h4');
					$this->out->elementStart('div', 'c');
					$this->out->raw($rootnotice->rendered);
					$this->out->elementEnd('div');
					$this->out->elementEnd('blockquote');
            	}
            }
        }
    }
    
    function showNoticeBar()
    {
    	$this->out->elementStart('div', 'bar clearfix');
    	$this->out->elementStart('div', 'info');
    	$this->out->elementStart('a', array('rel' => 'bookmark', 'target' => '_blank',
                                            'href' => common_path('discussionlist/' . $this->notice->id)));
    	$dt = common_date_iso8601($this->notice->created);
        $this->out->element('span', array('class' => 'time timestamp',
                                          'title' => $dt, 'time' => strtotime($this->notice->created)),
                            common_date_string($this->notice->created));
		$this->out->elementEnd('a');         
		$this->showNoticeSource();
		$this->out->elementEnd('div');  	
		
    	$this->showNoticeOptions();
    	$this->out->elementEnd('div');
    }

    function showNoticeOptions()
    {
    	$this->out->elementStart('ul', array('class' => 'op'));
    	if ($this->user) {
            if ($this->notice->user_id == $this->user->id) {
            	// 自己
				$this->showDiscusslink();
            	$this->showNoticeOptionSeparator();
	            $this->showFaveForm();
	            $this->showNoticeOptionSeparator();	
	            $this->showDeleteLink();  
            } else {
            	// 其他登录用户
            	$this->showDiscusslink();
            	$this->showNoticeOptionSeparator();	
            	$this->showRetweetLink();
	            $this->showNoticeOptionSeparator();
            	$this->showFaveForm();
				$this->showNoticeOptionSeparator();	
            	$this->showReplyLink();
            }
        } else {
        	// 匿名用户
        	$this->showAnonymousDiscusslink();
            $this->showNoticeOptionSeparator();	
            $this->showAnonymousRetweetLink();
			$this->showNoticeOptionSeparator();	
            $this->showAnonymousReplyLink();
        }
        $this->out->elementEnd('ul');
    }
    
    function showNoticeOptionSeparator() {
    	$this->out->raw('<li><a>|</a></li>');
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
        $liClass = 'notice';
        
        $this->out->elementStart('li', array('class' => $liClass, 'id' => 'notice-' . $this->notice->id, 'nid' => $this->notice->id));
    }

    /**
     * show the "favorite" form
     *
     * @return void
     */

    function showFaveForm()
    {
            if ($this->user->hasFave($this->notice)) {
	            $this->out->elementStart('li', array('class' => 'disfavor' , 
						'id' => 'notice_disfavor-' . $this->notice->id));
    			$this->out->elementStart('a', array('href' => common_path('notice/disfavor'),
	                 'title' => '取消收藏', 'nid' => $this->notice->id));
	            $this->out->text('取消收藏');
	    		$this->out->elementEnd('a');
	    		$this->out->elementEnd('li');
            } else {	            
	            $this->out->elementStart('li', array('class' => 'favor' , 
								'id' => 'notice_favor-' . $this->notice->id));
	            $this->out->elementStart('a', array('href' => common_path('notice/favor'),
	                 'title' => '收藏', 'nid' => $this->notice->id));
	            $this->out->text('收藏');
	            $this->out->elementEnd('a');
    			$this->out->elementEnd('li');
            }
    }

    /**
     * show the author of a notice
     *
     * By default, this shows the avatar and (linked) uname of the author.
     *
     * @return void
     */

    function showAuthor()
    {
        $this->out->elementStart('span', 'vcard author');
        $attrs = array('href' => $this->profile->profileurl,
                       'class' => 'url');
        if (!empty($this->profile->nickname)) {
            $attrs['title'] = $this->profile->nickname . ' (' . $this->profile->uname . ') ';
        }
        $this->out->elementStart('a', $attrs);
        $this->showAvatar();
        $this->showuname();
        $this->out->elementEnd('a');
        $this->out->elementEnd('span');
    }

    /**
     * show the avatar of the notice's author
     *
     * This will use the default avatar if no avatar is assigned for the author.
     * It makes a link to the author's profile.
     *
     * @return void
     */

    function showAvatar()
    {
        $avatar_size = AVATAR_STREAM_SIZE;
        $avatar = $this->profile->getAvatar($avatar_size);

        $this->out->element('img', array('src' => ($avatar) ?
                                         $avatar->displayUrl() :
                                         Avatar::defaultImage($avatar_size, $this->profile->id, $this->profile->sex),
                                         'class' => 'avatar photo',
                                         'width' => $avatar_size,
                                         'height' => $avatar_size,
                                         'alt' =>
                                         ($this->profile->nickname) ?
                                         $this->profile->nickname :
                                         $this->profile->uname));
    }

    /**
     * show the uname of the author
     *
     * Links to the author's profile page
     *
     * @return void
     */

    function showuname()
    {
        $this->out->element('span', array('class' => 'uname fn'),
                            $this->profile->uname);
    }

    /**
     * show the content of the notice
     *
     * Shows the content of the notice. This is pre-rendered for efficiency
     * at save time. Some very old notices might not be pre-rendered, so
     * they're rendered on the spot.
     *
     * @return void
     */

    function showContent()
    {
        $this->out->elementStart('p', array('class' => 'entry-content'));
        $this->out->raw($this->notice->rendered);
        $this->out->elementEnd('p');
    }

    /**
     * show the link to the main page for the notice
     *
     * Displays a link to the page for a notice, with "relative" time. Tries to
     * get remote notice URLs correct, but doesn't always succeed.
     *
     * @return void
     */

    function showNoticeLink()
    {
        $noticeurl = common_path('discussionlist/' . $this->notice->id);
        // XXX: we need to figure this out better. Is this right?
        if (strcmp($this->notice->uri, $noticeurl) != 0 &&
            preg_match('/^http/', $this->notice->uri)) {
            $noticeurl = $this->notice->uri;
        }
        $this->out->elementStart('dl', 'timestamp');
        $this->out->element('dt', null, '已发布');
        $this->out->elementStart('dd', null);
        $this->out->elementStart('a', array('rel' => 'bookmark',
                                            'href' => $noticeurl));
        $dt = common_date_iso8601($this->notice->created);
        $this->out->element('span', array('class' => 'published timestamp',
                                          'title' => $dt, 'time' => strtotime($this->notice->created)),
                            common_date_string($this->notice->created));

        $this->out->elementEnd('a');
        $this->out->elementEnd('dd');
        $this->out->elementEnd('dl');
    }
    
    function showNoticeSource()
    {
    	$this->out->raw(common_source_link($this->notice->source));
    }

    /**
     * show link to notice this notice is a reply to
     *
     * If this notice is a reply, show a link to the notice it is replying to. The
     * heavy lifting for figuring out replies happens at save time.
     *
     * @return void
     */

    function showContext()
    {
    	if (! empty($this->notice->conversation)
            && $this->notice->conversation != $this->notice->id) {
			$convurl = common_path('conversation/' .$this->notice->id);
            if ($this->user) {
    			$this->out->element('a', array('href' => $convurl, 'target' => '_blank', 
            		'class' => 'conversation'),  '查看相关会话');
    		} else {
            	$this->out->element('a', array('href' => $convurl, 'target' => '_blank', 
            		'class' => 'conversation trylogin'),  '查看相关会话');
            }
        }
        

    }
    
    function showDiscussLink() {       
        $this->out->elementStart('li', 'discuss');
    	$text = '评论';
    	if ($this->notice->discussion_num) {
    		$text = $text . '(<span>' . $this->notice->discussion_num . '</span>)';
    	}
    	
    	$this->out->elementStart('a', array('href' => common_path('discussionlist/' . $this->notice->id), 
    			'title' => '评论', 'nid' => $this->notice->id));
    	$this->out->raw($text);
    	$this->out->elementEnd('a');
    	$this->out->elementEnd('li'); 	        
    }
    
	function showAnonymousDiscussLink() {       
        $this->out->elementStart('li');
    	$text = '评论';
    	if ($this->notice->discussion_num) {
    		$text = $text . '(<span>' . $this->notice->discussion_num . '</span>)';
    	}
    	
    	$this->out->elementStart('a', array('href' => common_path('discussionlist/' . $this->notice->id)));
    	$this->out->raw($text);
    	$this->out->elementEnd('a');
    	$this->out->elementEnd('li'); 	        
    }
    
    /**
     * show a link to retweet to the current notice
     *
     * Should either do the retweet in the current notice form (if available), or
     * link out to the notice-posting form. A little flakey, doesn't always work.
     *
     * @return void
     */

    function showRetweetLink()
    {
        if ($this->user && $this->notice->user_id != $this->user->id) {
        	$this->out->elementStart('li', array('class' => 'retweet'));
    		
        	$attrs = array('href' => common_path('notice/retweet'),
                                                'title' => '转载',
    										'nid' => $this->notice->id,
        									'nickname' => $this->profile->nickname);
        	if (!empty($this->notice->retweet_from) && $this->notice->retweet_from != $this->notice->id) {
            	$attrs['oid'] = $this->notice->retweet_from;
        	}
        	$this->out->elementStart('a', $attrs);
        	
            $this->out->text('转载');
            if($this->notice->retweet_num)
    			$this->out->raw('(<span>' . $this->notice->retweet_num . '</span>)');
    		$this->out->elementEnd('a');    		
    		$this->out->elementEnd('li');
    			
        } else {
        	$this->out->element('li', null, '转载');
        }
    }
    
	function showAnonymousRetweetLink() {       
        $this->out->elementStart('li');
    	$text = '转载';
    	if ($this->notice->retweet_num) {
    		$text = $text . '(<span>' . $this->notice->retweet_num . '</span>)';
    	}
    	
    	$this->out->elementStart('a', array('href' => common_path('register?ivid=' . $this->profile->id), 
    			'class' => 'trylogin', 'rel' => 'nofollow'));
    	$this->out->raw($text);
    	$this->out->elementEnd('a');
    	$this->out->elementEnd('li'); 	        
    }
    
    /**
     * show a link to reply to the current notice
     *
     * Should either do the reply in the current notice form (if available), or
     * link out to the notice-posting form. A little flakey, doesn't always work.
     *
     * @return void
     */

    function showReplyLink()
    {
        $this->out->elementStart('li', 'reply');
    	$this->out->elementStart('a', array('href' => common_path('notice/replyat/' . $this->profile->uname),
    					'nid' => $this->notice->id,
                        'title' => '回复'));
    	$this->out->text('回复');
    	$this->out->elementEnd('a');    		
    	$this->out->elementEnd('li');
    }
    
	function showAnonymousReplyLink() {       
        $this->out->elementStart('li');
    	
    	$this->out->elementStart('a', array('href' => common_path('register?ivid=' . $this->profile->id), 
    			'class' => 'trylogin', 'rel' => 'nofollow'));
    	$this->out->text('回复');
    	$this->out->elementEnd('a');
    	$this->out->elementEnd('li'); 	        
    }

    /**
     * if the user is the author, let them delete the notice
     *
     * @return void
     */

    function showDeleteLink()
    {		
		 $this->out->elementStart('li', 'delete');
	   	 $this->out->element('a', array('href' => common_path('notice/delete'), 'title' => '删除',
        		'nid' => $this->notice->id),
        		'删除');
    	$this->out->elementEnd('li');
    }

    /**
     * finish the notice
     *
     * Close the last elements in the notice list item
     *
     * @return void
     */

    function showEnd()
    {
    	$this->out->element('input', array('class' => 'uname', 'type' => 'hidden', 'value' => $this->profile->uname));
        $this->out->elementEnd('li');
        
    }
}

