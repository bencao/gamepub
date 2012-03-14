<?php
class AjaxError
{
    function showError($message)
    {
    		//$this->element('div', array('class' => 'error'), $this->message);
    		//头文件为xml, 同时传递标签为p, class='error', 这个都要记住.
//    		header('Content-type: text/xml'); 
//    		echo '<p class="error">' . $message . '</p>';
    		
    		header('HTTP/1.1 403');
    		$this->view = TemplateFactory::get('JsonTemplate');
        	$this->view->init_document(array());
        	$this->view->show_json_objects(array('error' => $message));
        	$this->view->end_document();
    }	
}