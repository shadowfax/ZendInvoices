<?php

class Account_PasswordController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

	public function indexAction()
    {
        // action body
        $form = new Account_Form_Password();
        
        if ($this->getRequest()->isPost()) {
        	$formData = $this->getRequest()->getPost();
        	if ($form->isValid($formData)) {
        		$password = $form->getValue('password');
        		$oldpassword = $form->getValue('opassword');
        		
        		if (strcasecmp($password, $form->getValue('opassword')) !== 0) {
        			$users = new Invoices_Db_Table_Users();
        			if($users->changePassword($oldpassword, $password)) {
        				$this->_helper->redirector('index', 'index', 'default');
        			} else {
        				$this->view->ErrorMessage = $this->view->translate('Invalid password.');
        			}
        		} else {
        			$this->view->ErrorMessage = $this->view->translate('New password shall be different than the old password.');
        		}
        	}
        }
        
        $this->view->SectionTitle = "Change Password";
        $this->view->Form = $form;
    }
}

