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

class Invoices_Db_Table_Users extends Zend_Db_Table_Abstract
{
	protected $_name = 'users';
	protected $primary = 'id';
	
	/**
	 * Create a password hash based on the username, password and password length
	 * This makes it harder to bruteforce a password in case of a database error.
	 * @param String $username
	 * @param String $password
	 */
	private function __hash_password($username, $password) 
	{
		$hash = md5(sha1($username,true) . sha1($password,true), true);
		for($i=0;$i<strlen($password);$i++) {
			$hash = md5($hash, true);
		}
		return md5($hash, false);
	}
	
	public function login($username, $password)
	{
		// Get the password hash
		$hash = $this->__hash_password($username, $password);
		
		// Auth adapter
		$authAdapter = new Zend_Auth_Adapter_DbTable(
			$this->getAdapter(),
			$this->_name,
			'username',
			'password',
			'MD5(CONCAT(salt, ?)) AND active = 1'
		);
		
		$authAdapter->setIdentity($username);
		$authAdapter->setCredential($hash);
		
		$result = $authAdapter->authenticate();
		if ($result->isValid()) {
			// regenerate ID for security shake.
			Zend_Session::regenerateId();
			
			// get all info about this user from the login table  
			// ommit only the password, we don't need that			
			$userInfo = $authAdapter->getResultRowObject(null, 'password');  
			// the default storage is a session with namespace Zend_Auth
			$authStorage = Zend_Auth::getInstance()->getStorage();  
			$authStorage->write($userInfo);  
  
			// Everything OK
			return true;
		}
		
		// default return value
		return false;
	}
	
}