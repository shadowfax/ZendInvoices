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
		$request = $this->getRequest();
		
		if ($request->isPost()) {
			// Initialize the users table
			$users = new Invoices_Db_Table_Users();
			$bad_logins = new Invoices_Db_Table_Security_BadLogins();
			
			$formData = $this->getRequest()->getPost();
			if ($loginForm->isValid($formData)) {
				$username = $loginForm->getValue('username');
				if ($users->login($username, $loginForm->getValue('password'))) {
					// Must change password
					$userInfo = Zend_Auth::getInstance()->getStorage()->read();
					if ($userInfo->must_change_pass) {
						$this->_helper->redirector('index', 'password', 'account');
					} else {
						$this->_helper->redirector('index', 'index', 'default');
					}
				} else {
					// Log it!
					$userInfo = $users->findUserByName($username);
					$bad_logins->register($userInfo['id']);
					
					if ($bad_logins->getBadAttemptsForUser($userInfo['id']) > 4) {
						// Block user account
						if ($users->deactivateAccount($userInfo['id'])) {
							// Send email to reactivate account
							$email_address = $users->getEmailAddress($userInfo['id']);
							/*
							// ToDo: Send email with reactivation link
							if (!empty($email_address)) {
								//$transport = new Zend_Mail_Transport_Smtp('mail.example.com');
								//Zend_Mail::setDefaultTransport($transport);
								$mail = new Zend_Mail();
								$mail->setBodyText('This is the text of the mail.');
								$mail->setFrom('somebody@example.com', 'Some Sender');
								$mail->addTo($email_address, $_SERVER['SERVER_NAME']);
								$mail->setSubject('TestSubject');
								$mail->send();
							}
							*/
						}
					}
					
					// Shall we deactivate the account due to security risks
	
					$this->view->ErrorMessage = "Invalid credentials";
				}
			} else {
				// Log it!
				$bad_logins->register(0);
					
				$this->view->ErrorMessage = "Invalid credentials";
			}
			
			// Shall we ban the IP address?
			if ($bad_logins->getBadAttemptsForCurrentAddress() >= 20) {
				$banned_addresses = new Invoices_Db_Table_Security_BannedAddresses();
				$banned_addresses->banCurrentAddress();
				
				$request->setModuleName('default');
				$request->setControllerName('error');
				$request->setActionName('banned');
				$request->setDispatched(false);
			}
		}
		
		
		
		// show the view!
		$this->view->LoginForm = $loginForm;
	}
	
	public function logoutAction()
	{
		$auth = Zend_Auth::getInstance()->clearIdentity();
		Zend_Session::regenerateId();
		
		$this->_helper->redirector('login', 'auth', 'default');
	}
}