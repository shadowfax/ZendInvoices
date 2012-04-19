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
        
        $this->view->SectionTitle = "Change Password";
        $this->view->Form = $form;
    }
}

