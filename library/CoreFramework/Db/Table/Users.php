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

class CoreFramework_Db_Table_Users extends Zend_Db_Table_Abstract
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
	
	/**
	 * Find a user given the user name
	 * 
	 * @param String $username
	 */
	public function findUserByName($username)
	{
		$select = new Zend_Db_Select($this->getAdapter());
		$select->from($this->_name);
		$select->where('username=?', $username);
		
		return $this->getAdapter()->fetchRow($select);
	}
	
	/**
	 * Process a user login.
	 * 
	 * @param String $username
	 * @param String $password
	 */
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
			$userInfo = $authAdapter->getResultRowObject(array('id', 'username'));  
			// the default storage is a session with namespace Zend_Auth
			$authStorage = Zend_Auth::getInstance()->getStorage();  
			$authStorage->write($userInfo);  
  
			// Update Last Login
			$updateData = array(
				'last_login'	=> date("Y-m-d H:i:s")
			);
			$where = $this->getAdapter()->quoteInto('id = ?', $userInfo->id);
			parent::update($updateData, $where);
			
			// Everything OK
			return true;
		}
		
		// default return value
		return false;
	}
	
	/**
	 * Change the current user password.
	 * 
	 * @param String $oldpassword
	 * @param String $password
	 */
	public function changePassword($oldpassword, $password)
	{
		if (Zend_Auth::getInstance()->hasIdentity()) {
			// Get user information
			$userInfo = Zend_Auth::getInstance()->getStorage()->read();
			
			// Generate the password hash
			$hash = $this->__hash_password($userInfo->username, $password);
			
			// Generate a new seed
			list($usec, $sec) = explode(' ', microtime());
			$seed = (float) $sec + ((float) $usec * 100003);
			mt_srand($seed);
			
			$salt = md5(uniqid(mt_rand(),true));
			
			$update = array(
				'salt'		=> $salt,
				'password'	=> md5($salt . $hash),
				'must_change_pass'	=> 0,
				'last_pass_change'	=> date("Y-m-d H:i:s")
			);
			
			$where = array();
			$where[] = $this->getAdapter()->quoteInto('active = ?', 1);
			$where[] = $this->getAdapter()->quoteInto('id = ?', $userInfo->id);
			$where[] = $this->getAdapter()->quoteInto('password = MD5(CONCAT(salt,?))', $this->__hash_password($userInfo->username, $oldpassword));
			
			return parent::update($update, $where);
		}
		
		// default return value
		return false;
	}
	
	/**
	 * Check if a user is active
	 * 
	 * @param Integer $user_id
	 */
	public function isActive($user_id)
	{
		$select = new Zend_Db_Select($this->getAdapter());
		$select->from($this->_name, 'active');
		$select->where('id=?', $user_id);
		
		$row = $this->getAdapter->fetchOne($select);
		
		if ($row === 1) return true;
		return false;
	}
	
	/**
	 * Check if a user user must change passwords
	 */
	public function mustChangePassword($user_id)
	{
		$select = new Zend_Db_Select($this->getAdapter());
		$select->from($this->_name, array('must_change_pass', 'last_pass_change'));
		$select->where('id=?', $user_id);
		
		$row = $this->getAdapter()->fetchRow($select);
		
		if ($row['must_change_pass'] === '1') return true;

		// Although it is not explicitly forced lets check
		// the last time the user updated his password and 
		// force if it is older than a year
		$days = floor((time() - strtotime($row['last_pass_change'])) / 86400);
		if ($days > 365) return true;
		
		// Default return value
		return false;
	}
	
	/**
	 * Get the email address of a given user
	 * @param Integer $user_id
	 */
	public function getEmailAddress($user_id)
	{
		$select = new Zend_Db_Select($this->getAdapter());
		$select->from($this->_name, array('email'));
		$select->where('id=?', $user_id);
		
		return $this->getAdapter()->fetchOne($select);
	}
	
	/**
	 * Deactivate a user account given its user identifier
	 * @param Integer $user_id
	 */
	public function deactivateAccount($user_id)
	{
		$data = array(
			'active' => 0
		);
		
		$where = $this->getAdapter()->quoteInto('id=?', $user_id);
		
		return parent::update($data, $where);
	}
	
	public function activateAccount($user_id)
	{
		$data = array(
			'active' => 1
		);
		
		$where = $this->getAdapter()->quoteInto('id=?', $user_id);
		
		return parent::update($data, $where);
	}
}