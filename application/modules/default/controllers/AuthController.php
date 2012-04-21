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
			$users = new CoreFramework_Db_Table_Users();
			$bad_logins = new CoreFramework_Db_Table_Security_BadLogins();
			
			$formData = $this->getRequest()->getPost();
			if ($loginForm->isValid($formData)) {
				$username = $loginForm->getValue('username');
				if ($users->login($username, $loginForm->getValue('password'))) {
					// Log
					CoreFramework_Log::info("login: success for '" . $username ."' from " . $_SERVER['REMOTE_ADDR']);
					// Must change password
					$userInfo = Zend_Auth::getInstance()->getStorage()->read();
					if ($userInfo->must_change_pass) {
						$this->_helper->redirector('index', 'password', 'account');
					} else {
						$this->_helper->redirector('index', 'index', 'default');
					}
				} else {
					CoreFramework_Log::info("login: failed for '" . $username ."' from " . $_SERVER['REMOTE_ADDR']);
					// Log it!
					$userInfo = $users->findUserByName($username);
					$bad_logins->register($userInfo['id']);
					
					// Shall we deactivate the account due to security risks
					if ($bad_logins->getBadAttemptsForUser($userInfo['id']) > 4) {
						// Block user account
						if ($users->deactivateAccount($userInfo['id'])) {
							// Generate a reactivation entry
							$reactivation = new CoreFramework_Db_Table_Security_AccountReactivation();
							
							// Send email to reactivate account
							$email_address = $users->getEmailAddress($userInfo['id']);
							if (!empty($email_address)) {
								$mail = new CoreFramework_Mail();
								$mail->setTemplateVariable('Token', $reactivation->createToken($userInfo['id']));
								$mail->setSubject('Your account has been deactivated');
								$mail->addTo($email_address,$email_address);
								$mail->sendTemplate('deactivated.phtml');
							}
						}
					}
	
					$this->view->ErrorMessage = "Invalid credentials";
				}
			} else {
				// Log it!
				$bad_logins->register(0);
					
				$this->view->ErrorMessage = "Invalid credentials";
			}
			
			// Shall we ban the IP address?
			if ($bad_logins->getBadAttemptsForCurrentAddress() >= 20) {
				$banned_addresses = new CoreFramework_Db_Table_Security_BannedAddresses();
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
	
	/**
	 * Account reactivation
	 * This is an auto reactivation so the user may enable the account through a link
	 */
	public function reactivateAction()
	{
		$request = $this->getRequest();
		$token = $request->getParam('account', NULL);
		if (is_null($token)) {
			$request->setModuleName('default');
			$request->setControllerName('error');
			$request->setActionName('error404');
			$request->setDispatched(false);
		} elseif(!preg_match('/^[a-f0-9]{72}$/', $token)) {
			$request->setModuleName('default');
			$request->setControllerName('error');
			$request->setActionName('error404');
			$request->setDispatched(false);
		}
		
		// Everything looks fine...
		// Reactivate the account.
		$reactivation = new CoreFramework_Db_Table_Security_AccountReactivation();
		$userId = $reactivation->getUserIdentifier($token);
		
		if ($userId <= 0) {
			$request->setModuleName('default');
			$request->setControllerName('error');
			$request->setActionName('error404');
			$request->setDispatched(false);
		}
		
		$users = new CoreFramework_Db_Table_Users();
		if ($users->activateAccount($userId)) {
			$reactivation->removeToken($token);
			// Remove bad logins for this user aswell
			$bad_logins = new CoreFramework_Db_Table_Security_BadLogins();
			$bad_logins->resetUser($userId);
		}
		
		// Go to login page
		$request->setModuleName('default');
		$request->setControllerName('auth');
		$request->setActionName('login');
		$request->setDispatched(false);
	}
}