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

class CoreFramework_Db_Table_Security_BannedAddresses extends Zend_Db_Table_Abstract
{
	protected $_name = 'security_banned_addresses';
	protected $_primary = 'address';
	
	/**
	 * Delete expired entries.
	 */
	protected function _garbageCollect()
	{
		$where = array();
		$where[] = $this->getAdapter()->quoteInto('expire < ?', time());
		// Expire = 0 -> Permanent ban
		$where[] = $this->getAdapter()->quoteInto('expire > ?', 0);
		
		parent::delete($where);
	}
	
	/**
	 * Check if the IP address has been banned
	 */
	public function isBanned()
	{
		// Removed expired bans
		$this->_garbageCollect();
		
		// Check database
		$select = new Zend_Db_Select($this->getAdapter());
		$select->from($this->_name, new Zend_Db_Expr("COUNT(address)"));
		$select->where('address=?', ip2long($_SERVER['REMOTE_ADDR']));
		
		$count = $this->getAdapter()->fetchOne($select);
		if($count === '1') return true;
		
		return false;
	}
	
	/**
	 * Set a ban for an address that will be effective for a given time.
	 * Defaults to 5 minutes.
	 * @param Integer $time
	 */
	public function banCurrentAddress($minutes = 5)
	{
		$data = array(
			'address'	=> ip2long($_SERVER['REMOTE_ADDR']),
			'expire'	=> (time() + ($minutes * 60))
		);
		
		parent::insert($data);
	}
}