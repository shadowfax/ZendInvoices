<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
	protected function _initDoctype()
	{
		$this->bootstrap('view');
        $view = $this->getResource('view');
        $view->doctype('XHTML1_STRICT');
        
        $this->bootstrap('frontController');
        $view->headLink()->appendStylesheet($view->baseUrl('/css/layout.css'));
        $view->headLink()->appendStylesheet($view->baseUrl('/css/navigation.css'));
	}

}

