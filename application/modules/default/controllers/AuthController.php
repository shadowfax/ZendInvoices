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

class AuthController extends Zend_Controller_Action
{
	
	public function loginAction()
	{
		$loginForm = new Default_Form_Login();
		
		if ($this->getRequest()->isPost()) {
			$formData = $this->getRequest()->getPost();
			if ($loginForm->isValid($formData)) {
				
			} else {
				$this->view->ErrorMessage = "Invalid credentials";
			}
		}
		
		$this->view->LoginForm = $loginForm;
	}
}