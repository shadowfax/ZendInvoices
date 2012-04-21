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

class CoreFramework_Log
{
	/**
	 * Logger object
	 * 
	 * @var Zend_Log
	 */
	protected $_logger;
	
	/**
	 * Shared object for Singleton
	 * 
	 * @var CoreFramework_Log
	 */
	private static $_instance;
	
	protected function __construct()
	{
		$this->_logger = Zend_Registry::get('log');
	}
	
	/**
	 * Return the logger singleton object
	 * 
	 * @return CoreFramework_Log
	 */
	public static function getInstance()
	{
		if (is_null(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	/**
	 * Returns the logging object
	 * 
	 * @return Zend_Log
	 */
	public function getLog()
	{
		return $this->_logger;
	}
	
	/**
	 * Log an information message
	 * 
	 * @param string $message
	 */
	public static function info($message)
	{
		self::getInstance()->getLog()->info($message);
	}
}