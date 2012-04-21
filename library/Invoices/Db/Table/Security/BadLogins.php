<?php

class Invoices_Db_Table_Security_BadLogins extends Zend_Db_Table_Abstract
{
	protected $_name = 'security_bad_logins';
	protected $_primary = array('user_id', 'time');
	
	private function _garbageCollect()
	{
		$where = $this->getAdapter()->quoteInto('time < ?', time() - 300);
		parent::delete($where);
	}
	
	public function register($user_id)
	{
		// delete old entries
		$this->_garbageCollect();
		
		// Save to database
		$data = array(
			'user_id'	=> $user_id,
			'time'		=> time(),
			'address'		=> ip2long($_SERVER['REMOTE_ADDR'])
		);
		
		parent::insert($data);
	}
	
	public function getBadAttemptsForUser($user_id)
	{
		// delete old entries
		$this->_garbageCollect();
		
		// Non existent user... fast exit.
		if ($user_id <= 0) return 0;
		
		// Get results from database
		$select = new Zend_Db_Select($this->getAdapter());
		$select->from($this->_name, new Zend_Db_Expr('COUNT(time)'));
		$select->where('user_id=?', $user_id);
			
		$attempts = $this->getAdapter()->fetchOne($select);
		return (int)$attempts;
	}
	
	/**
	 * Get the number of attempts an IP address has tried to authenticate
	 */
	public function getBadAttemptsForCurrentAddress()
	{
		// delete old entries
		$this->_garbageCollect();
		
		// Get results from database
		$select = new Zend_Db_Select($this->getAdapter());
		$select->from($this->_name, new Zend_Db_Expr('COUNT(time)'));
		$select->where('address=?', ip2long($_SERVER['REMOTE_ADDR']));
		
		$attempts = $this->getAdapter()->fetchOne($select);
		return (int)$attempts;
	}
}