<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

require_once INSTALLDIR.'/lib/playerlist.php';

class CityPeopleHTMLTemplate extends PublictwocolumnHTMLTemplate
{
	var $femaleplayer = null;
	var $maleplayer = null;
	
	function title()
    {
    	$message = '同城游友';
    	switch($this->args['area'])
    	{
    		case 'gameserver': $message .='-'.$this->args['servername'];
    				break;
    		case 'game': $message .='-'.$this->args['gamename'];
    				break;
    		default:$message .=' - ' . common_config('site', 'name') . '平台';
    				break;
    	}
    	return $message;
    }
    
	function showContent() {
		
		$this->showCorehead();
		
		$this->showFemale();
		
		$this->showMale();
		
	}

	function showCorehead() {
		if($this->args['notlocated'] == 1)
		{   
			$this->element('h2',null,'您没有完善您的所在地信息，因此没有成功获取同城用户。');
		}
		$this->elementStart('h2');
		$this->elementStart('ul',array('class' => 'clearfix','id' => 'public_thirdary_nav'));
		if($this->args['area'] == 'all' || !$this->args['area'])
			$this->elementStart('li',array('class' => 'active'));
		else
			$this->elementStart('li');
			$this->element('a',array('href' => common_local_url('citypeople', array('area' => 'all')), 'alt' => common_config('site', 'name') . '平台'), common_config('site', 'name') . '平台');
		$this->elementEnd('li');
		if(common_current_user())
		{
			$this->element('li',null,'|');
			if($this->args['area'] == 'game')
				$this->elementStart('li',array('class' => 'active'));
			else
				$this->elementStart('li');
				$this->element('a',array('href' => common_local_url('citypeople', array('area' => 'game')),'alt' => $this->args['gamename']),$this->args['gamename']);
			$this->elementEnd('li');
			$this->element('li',null,'|');
			if($this->args['area'] == 'gameserver')
				$this->elementStart('li',array('class' => 'active'));
			else
				$this->elementStart('li');
				$this->element('a',array('href' => common_local_url('citypeople', array('area' => 'gameserver')),'alt' => $this->args['servername']),$this->args['servername']);
			$this->elementEnd('li');
		}
		$this->elementEnd('ul');
		$this->elementEnd('h2');
	}
	
	function showFemale() {
		
		$this->femaleplayer = $this->args['femaleplayer'];
		$this->elementStart('dl',array('class' => 'recommendpeople'));
		$this->elementStart('dt',array('class' => 'head'));
		$this->text('同城美女玩家');
		//$this->element('a',array('class' => 'toggle','href' => '#'),'更多');
		$this->elementEnd('dt');
		$this->elementStart('dd');
		$playerlist = new PlayerList($this, $this->femaleplayer, null, $this->cur_user);
        $this->cnt = $playerlist->show();
		$this->elementEnd('dd');
		$this->elementEnd('dl');
		
	}
	
	function showMale() {
		$this->maleplayer = $this->args['maleplayer'];
		$this->elementStart('dl',array('class' => 'recommendpeople'));
		$this->elementStart('dt',array('class' => 'head'));
		$this->text('同城帅哥玩家');
		//$this->element('a',array('class' => 'toggle','href' => '#'),'更多');
		$this->elementEnd('dt');
		$this->elementStart('dd');
		$playerlist = new PlayerList($this, $this->maleplayer, null, $this->cur_user);
        $this->cnt = $playerlist->show();
		$this->elementEnd('dd');
		$this->elementEnd('dl');
		
	}
}
?>