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

class CoreFramework_Mail extends Zend_Mail
{
	
	/**
	 * Enter description here ...
	 * @var Zend_View
	 */
	protected static $_defaultView;
	
	public function __construct($charset = 'iso-8859-1')
	{
		parent::__construct($charset);
		$this->_view = self::getDefaultView();
	}
	
	protected static function getDefaultView()
	{
		if (is_null(self::$_defaultView)) {
			self::$_defaultView = new Zend_View();
			self::$_defaultView->setScriptPath(APPLICATION_PATH . '/views/scripts/mail');
		}
		return self::$_defaultView;	
	}
	
	public function sendTemplate($template, $encoding = Zend_Mime::ENCODING_QUOTEDPRINTABLE)
	{
		$html = $this->_view->render($template);
		$this->setBodyHtml($html, $this->getCharset(), $encoding);
		$this->send();
	}
}