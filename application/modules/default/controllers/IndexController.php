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

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    	
    	// This file is a bit special...
    	// If not logged in take the user to the authentication controller
    	if (!Zend_Auth::getInstance()->hasIdentity()) {
    		$this->_helper->redirector('login', 'auth', 'default');
    	}
    }

    public function indexAction()
    {
        // action body
    }


}

