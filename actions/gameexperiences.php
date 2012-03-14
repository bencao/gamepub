<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

class GameexperiencesAction extends GamebasicAction
{
    function isReadOnly($args)
    {
        return true;
    }

    function handle($args)
    {
        parent::handle($args);
        
        $exprnotices = Notice::getFirstTaggedNotices(($this->cur_page-1)*NOTICES_PER_PAGE, NOTICES_PER_PAGE + 1, 
	        		0, 0, null, 0, 3, $this->cur_game->id, 0);
		$this->addPassVariable('notice',$exprnotices);
		
		if ($this->boolean('ajax')) {
			if($this->cur_page > 1) {
	    		$exprView = TemplateFactory::get('GameexperiencesHTMLTemplate');
				$exprView->showPageNotices($this->paras);     		
	    	}
		} else {
    		$this->displayWith('GameexperiencesHTMLTemplate');
		} 
    }
}
