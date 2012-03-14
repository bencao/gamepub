<?php

if (!defined('SHAISHAI')) { exit(1); }

class RandommusicsAction extends ShaiAction
{
	function getStreamRandomMusicsString() {
		return common_stream('mplayer:randoms', array($this, '_getStreamRandomMusicsString'), null, 120);
	}
	
	function _getStreamRandomMusicsString() {
		$notice = Notice::getLatestMusics(0, 20);
		$html = '<randomList>';
		while ($notice && $notice->fetch()) {
			if ($notice) {
				// may be deleted
				$profile = $notice->getProfile();
				$avatar = $profile->getAvatar(AVATAR_STREAM_SIZE);
				$hasFav = $this->cur_user->hasFave($notice) ? 1 : 0;
				
				$str = preg_replace('/\[[^\]]*\]/i', '', $notice->rendered);
				$str = preg_replace('/\<img[^\>]*\>/i', '', $str);
				$str = preg_replace("/\n/", '', $str);
					
				preg_match('/^<span>(?<desc>.*)<\/span> <div class="music_message"><a href="#" link="(?<url>[^"]+)".*$/isU', $str, $matches);
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
		}
		$html .= '</randomList>';
		return $html;
	}
	
    function handle($args)
    {
    	parent::handle($args);
    	
    	$this->view = TemplateFactory::get('XMLTemplate');
    	$this->view->startXML();
    	$this->view->raw($this->getStreamRandomMusicsString());
    	$this->view->endXML();
//		echo $this->getStreamRandomMusicsString();
    }
}

?>