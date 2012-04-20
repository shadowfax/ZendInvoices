<?php

class Account_Form_Password extends Zend_Form
{
	public function init()
	{
		$this->setName('user');
		$this->setMethod('post');
        
        $this->addElement('password', 'opassword', array(
            'filters'    => array('StringTrim'),
            'validators' => array(
                array('StringLength', false, array(3, 50))
            ),
            'required'   => true,
            'label'      => 'Current password:'
        ));
        
        $this->addElement('password', 'password', array(
            'filters'    => array('StringTrim'),
            'validators' => array(
                array('StringLength', false, array(3, 50))
            ),
            'required'   => true,
            'label'      => 'New password:'
        ));
		
        $this->addElement('password', 'confirm', array(
            'filters'    => array('StringTrim'),
            'validators' => array(
                array('StringLength', false, array(3, 50)),
                array('Identical', false, array('token' => 'password'))
            ),
            'required'   => true,
            'label'      => 'Confirm new password:'
        ));

        // CSRF
		$this->addElement('hash', 'csrf', array(
			'salt'	=> 'unique',
			'ignore' => true
        ));
        
        $this->addElement('submit', 'save', array(
            'required' => false,
            'ignore'   => true,
            'label'    => 'Save'
        ));
	}
}