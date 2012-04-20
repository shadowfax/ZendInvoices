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

class Default_Form_Login extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');
		
		$this->addElement(
			'text', 'username', array(
				'label' => 'Username:',
				'required' => true,
				'filters'    => array('StringTrim')
		));
		
		$this->addElement('password', 'password', array(
			'label' => 'Password:',
			'required' => true
		));
		
		$this->addElement('submit', 'submit', array(
			'ignore'   => true,
			'label'    => 'Sign In'
		));
		
		// And finally add some CSRF protection
        $this->addElement('hash', 'csrf', array(
        	'ignore' => true
        ));
	}
}