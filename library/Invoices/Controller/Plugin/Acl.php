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

class Invoices_Controller_Plugin_Acl extends Zend_Controller_Plugin_Abstract
{
	public function preDispatch(Zend_Controller_Request_Abstract $request)
	{
		// Must be authenticated.
		// Spanish -and other countries laws- require authentication due to privacy laws
		if (!Zend_Auth::getInstance()->hasIdentity()) {
			// If not authenticated disable the layout
			$layout = Zend_Layout::getMvcInstance();
			$layout->disableLayout();
			
			//Default module should be accesible for guests and authenticated users
			if (strcasecmp($request->getModuleName(),'default') !== 0) 
			{
				$request->setModuleName('default');
				$request->setControllerName('auth');
				$request->setActionName('login');
				$request->setDispatched(false);
			}
		}
		
		$resource = $request->getModuleName() . ':' . $request->getControllerName();
		$action = $request->getActionName();
		
		// If the user must change the password do not allow anything else
		$currentUser = Zend_Registry::get('Current_User');
		
		// Restrict user access if they must change password
		// to the password change page or logout page.
		if ($currentUser->mustChangePassword()) {
			$limited_resources = array(
				array(
					'module'		=> 'default',
					'controller'	=> 'auth',
					'action'		=> 'logout'
				),
				array(
					'module'		=> 'account',
					'controller'	=> 'password',
					'action'		=> 'index'
				)
			);
			
			$valid_resource = false;
			
			foreach($limited_resources as $t_resource) {
				if ((strcasecmp($t_resource['module'] . ':' . $t_resource['controller'], $resource) == 0) && (strcasecmp($t_resource['action'], $action) == 0)) {
					$valid_resource = true;
					break;
				}
			}
			
			if (!$valid_resource) {
				$request->setModuleName('account');
				$request->setControllerName('password');
				$request->setActionName('index');
				$request->setDispatched(false);
			}
		}
		
		// Real ACL starts here!
		
	}
}