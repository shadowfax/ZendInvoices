<?php


class Invoices_User
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
		$users = new Invoices_Db_Table_Users();
		return $users->isActive($this->_id);
	}
	
	/**
	 * Check if the user must change the password
	 */
	public function mustChangePassword()
	{
		$users = new Invoices_Db_Table_Users();
		
		if ($this->_id > 0) return $users->mustChangePassword($this->_id);
		else return false;
	}
	

}