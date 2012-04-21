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

class CoreFramework_User
{
	protected $_id;
	
	public function __construct($user_id =  0)
	{
		if ($user_id > 0) {
			$this->_id = $user_id;
		} elseif (Zend_Auth::getInstance()->hasIdentity()) {
			$userInfo = Zend_Auth::getInstance()->getStorage()->read();
			$this->_id = $userInfo->id;
		} else {
			$this->_id = 0;
		}
	}

	/**
	 * Check if the user is registered.
	 */
	public function isRegistered()
	{
		if ($this->_id == 0) return false;
		return true;
	}
	
	/**
	 * Check if the user is enabled.
	 */
	public function isActive()
	{
		if ($this->_id == 0) return false;
		
		// Check against database
		$users = new CoreFramework_Db_Table_Users();
		return $users->isActive($this->_id);
	}
	
	/**
	 * Check if the user must change the password
	 */
	public function mustChangePassword()
	{
		$users = new CoreFramework_Db_Table_Users();
		
		if ($this->_id > 0) return $users->mustChangePassword($this->_id);
		else return false;
	}
	

}