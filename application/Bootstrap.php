<?php
/**
 * ZendInvoices
 *
 * Copyright (c) 2012 Juan Pedro Gonzalez Gutierrez.
 *
 * All rights reserved. This program and the accompanying materials
 * are made available under the terms of the GNU Public License v3.0
 * which accompanies this distribution, and is available at
 * http://www.gnu.org/licenses/gpl.html
 *
 * Contributors:
 *    Juan Pedro Gonzalez Gutierrez - initial API and implementation
 *    
 */

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
        
        $this->bootstrap('locale');
        $translator = $this->getResource('locale');
        $view->Lang = $translator->getLanguage();
	}
	
	protected function _initLog()
	{
		if ($this->hasPluginResource('log')) {
			$logResource = $this->getPluginResource('log');
			$log = $logResource->getLog();
			Zend_Registry::set('log', $log);
		}
	}
	
	/*
	protected function _initMail()
	{
		$mailTransport = new Zend_Mail_Transport_Smtp("localhost", array('port' => 25));
		Zend_Mail::setDefaultTransport($mailTransport);
	}
	*/
	
	protected function _initPlugins()
	{
		$this->bootstrap('frontController');
		$frontController = $this->getResource('frontController');
		
		$frontController->registerPlugin(new Invoices_Controller_Plugin_Acl());
	}
	
	
	protected function _initUser()
	{
		$auth = Zend_Auth::getInstance();
		Zend_Registry::set('Current_User', new CoreFramework_User());
	}
}

