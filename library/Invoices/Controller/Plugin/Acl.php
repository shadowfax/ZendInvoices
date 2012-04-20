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
	}
}