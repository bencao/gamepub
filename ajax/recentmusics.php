<?php

if (!defined('SHAISHAI')) { exit(1); }

class RecentmusicsAction extends ShaiAction
{	
	function getStreamRecentMusicsString() {
		
		$notices = Music_history::getRecentMusicNotices($this->cur_user->id, 0, 20);
		
		$html = '<recentList><index>0</index>';
		
		if ($notices) {
			foreach ($notices as $notice) {
				if ($notice) {
					// may be deleted
					$profile = $notice->getProfile();
					$avatar = $profile->getAvatar(AVATAR_PROFILE_SIZE);
					$hasFav = $this->cur_user->hasFave($notice) ? 1 : 0;
					
					// 去除tag和表情
					$str = preg_replace('/\[[^\]]*\]/i', '', $notice->rendered);
					$str = preg_replace('/\<img[^\>]*\>/i', '', $str);
					$str = preg_replace("/\n/", '', $str);
					
					if(preg_match('/^<span>(?<desc>.*)<\/span> <div class="music_message"><a href="#" link="(?<url>[^"]+)".*$/isU', $str, $matches)) {
					
						$desc = trim($matches['desc']);
						if (empty($desc)) {
							$desc = '无标题 - ' . $notice->id;
						}
						
						$html .= '<mp3><id>' . $notice->id . '</id>'
							. '<description>' . $desc . '</description>' 
							. '<url>' . $matches['url'] . '</url>'
							. '<people>' . $profile->nickname . '</people>'
							. '<img>' . (($avatar) ? $avatar->displayUrl() : Avatar::defaultImage(AVATAR_STREAM_SIZE, $profile->id, $profile->sex)) . '</img>'
							. '<num>' . $notice->discussion_num . '</num>'
							. '<fav>' . $hasFav . '</fav>'
							. '<msg>/discussionlist/' . $notice->id . '</msg>'
							. '</mp3>';
					}
					$notice->free();
				}
			}
		}
		$html .= '</recentList>';
		return $html; 
	}
	
    function handle($args)
    {
    	parent::handle($args);
    	
    	$this->view = TemplateFactory::get('XMLTemplate');
    	$this->view->startXML();
    	$this->view->raw($this->getStreamRecentMusicsString());
    	$this->view->endXML();
    }
}

?>