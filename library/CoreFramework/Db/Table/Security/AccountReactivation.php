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

class CoreFramework_Db_Table_Security_AccountReactivation extends Zend_Db_Table_Abstract
{
	protected $_name = 'security_account_reactivation';
	
	public function createToken($user_id)
	{
		// create a random token
		list($usec, $sec) = explode(' ', microtime());
		$seed = (float) $sec + ((float) $usec * 100003);
		
		// create a random token
		$token = md5(uniqid(mt_rand(), true), false) . sha1(uniqid(mt_rand(), true), false);
				
		// We can now save the data
		$data = array(
			'token'			=> $token,
			'user_id'		=> $user_id,
			'creation_time'	=> time()
		);
		
		// Insert
		parent::insert($data);
		
		// Return the token
		return $token;
	}
	
	/**
	 * Get the user identifier based on the token
	 * @param string $token
	 */
	public function getUserIdentifier($token)
	{
		$select = new Zend_Db_Select($this->getAdapter());
		$select->from($this->_name, array('id' => 'user_id'));
		$select->where('token=?', $token);
		
		$result = $this->getAdapter()->fetchOne($select);
		if (empty($result)) $result = '0';
		
		return $result;
	}
	
	public function removeToken($token)
	{
		$where = $where = $this->getAdapter()->quoteInto('token=?', $token);
		parent::delete($where);
	}
}