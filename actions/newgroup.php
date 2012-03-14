<?php
/**
 * ShaiShai
 * Add a new group
 *
 * @category  Group
 */

if (!defined('SHAISHAI')) {
	exit(1);
}

/**
 * Add a new group
 *
 * This is the form for adding a new group
 *
 * @category Group
 */

class NewgroupAction extends ShaiAction
{
	var $game_msg = null;
	var $life_msg = null;
    
	function handle($args)
	{
		parent::handle($args);
		$this->game_msg = '您可以创建游戏' . GROUP_NAME() . '。';
		$this->life_msg = '您可以创建生活' . GROUP_NAME() . '。';
		$this->judgeOk();
	}

	function showForm($gamegroup, $lifegroup, $game_msg=null, $life_msg=null)
	{
		$this->addPassVariable('gamegroup', $gamegroup);
		$this->addPassVariable('lifegroup', $lifegroup);
		$this->addPassVariable('game_msg', $game_msg);
		$this->addPassVariable('life_msg', $life_msg);
		$this->displayWith('NewgroupHTMLTemplate');
	}

	function judgeOk()
	{
		$gamegroup = false;	
		$lifegroup = false;		
		$user_grade = $this->cur_user->getUserGrade();
        $owned_lg_num = $this->cur_user->getOwnedLifeGroupsNum();
        $owned_gg_num = $this->cur_user->getOwnedGameGroupsNum();
        
        if ($user_grade<2){
        	$this->life_msg = '要创建一个生活' . GROUP_NAME() . '，您必须达到2级。';
        	$lifegroup = false;
        }else if ($owned_lg_num == 1 && $user_grade<7){
        	$this->life_msg = '您已经拥有一个生活' . GROUP_NAME() . '，需要达到7级才能创建第二个生活' . GROUP_NAME() . '。';
            $lifegroup = false;
        }else if ($owned_lg_num > 1){
        	$this->life_msg = '您已经拥有两个生活' . GROUP_NAME() . '，不能再创建新的生活' . GROUP_NAME() . '。';
            $lifegroup = false;
        }else{
        	$lifegroup = true;
        }
        
        
        
        if ($user_grade<2){
        	$this->game_msg = '要创建一个游戏' . GROUP_NAME() . '，您必须达到2级。';
            $gamegroup = false;
        }else if ($owned_gg_num == 1 && $user_grade<4){
        	$this->game_msg = '您已经拥有一个游戏' . GROUP_NAME() . '，需要达到4级才能创建第二个游戏' . GROUP_NAME() . '。';
            $gamegroup = false;
        }else if ($owned_gg_num == 2 && $user_grade<6){
        	$this->game_msg = '您已经拥有两个游戏' . GROUP_NAME() . '，需要达到6级才能创建第三个游戏' . GROUP_NAME() . '。';
            $gamegroup = false;
        }else if ($owned_gg_num == 3 && $user_grade<8){
        	$this->game_msg = '您已经拥有三个游戏' . GROUP_NAME() . '，需要达到8级才能创建第四个游戏' . GROUP_NAME() . '。';
            $gamegroup = false;
        }else if ($owned_gg_num > 3){
        	$this->game_msg = '您已经拥有四个游戏' . GROUP_NAME() . '，不能再创建新的游戏' . GROUP_NAME() . '。';
            $gamegroup = false;
        }else{
        	$gamegroup = true;
        }
        
        $this->showForm($gamegroup, $lifegroup, $this->game_msg, $this->life_msg);
    }

}

