<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
	protected function _initDoctype()
	{
		$this->bootstrap('view');
        $view = $this->getResource('view');
        $view->setEncoding('UTF-8');
        $view->doctype('XHTML1_STRICT');
        $view->headMeta()->appendHttpEquiv('Content-Type', 'text/html;charset=utf-8');
        $view->headMeta()->appendHttpEquiv('Author', 'Juan Pedro Gonzalez Gutierrez');
        $view->headMeta()->appendHttpEquiv('Copyright', '(c) Juan Pedro Gonzalez Gutierrez');
        
        $this->bootstrap('frontController');
        $view->headLink()->appendStylesheet($view->baseUrl('/css/layout.css'));
        $view->headLink()->appendStylesheet($view->baseUrl('/css/navigation.css'));
	}

}

