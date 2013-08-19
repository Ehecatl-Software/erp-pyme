<?php
class Form_User extends Zend_Form{
		
		public function init(){
				// create new element test unit 
        		$id = $this->createElement('hidden', 'id_user'); 
        		// element options 
        		$id->setDecorators(array('ViewHelper')); 
        		// add the element to the form 
        		$this->addElement($id); 
        		
        		//input for firstname
				$firstnameElement = $this->createElement('text','firstname');
				$firstnameElement->setLabel("Nombre:");
				$firstnameElement->setRequired(true);
				$firstnameElement->addFilter('StripTags');
				$firstnameElement->addErrorMessage('Nombre es requerido');
				$this->addElement($firstnameElement);
		
				//input for lastname
				$lastnameElement = $this->createElement('text','lastname');
				$lastnameElement->setLabel("Apellido: ");
				$lastnameElement->setRequired(true);
				$lastnameElement->addErrorMessage('Apellido es requerido');
				$this->addElement($lastnameElement);
		
				//input for email
				$emailElement = $this->createElement('text','email');
				$emailElement->setLabel("Email:");
				$emailElement->setRequired(true);
				$emailElement->addErrorMessage('Email es requerido');
				$this->addElement($emailElement);
		
				//input for username
				$usernameElement = $this->createElement('text','username');
				$usernameElement->setLabel("Usuario:");
				$usernameElement->setRequired(true);
				$usernameElement->addErrorMessage('Nombre de Usuario es  requerido');
				$this->addElement($usernameElement);
		
				//input for password
				$passwordElement = $this->createElement('password','password');
				$passwordElement->setLabel("Password:");
				$passwordElement->setRequired(true);
				$this->addElement($passwordElement);
		
				//submit button
				$submitElement = $this->addElement('submit', 'submit',array('label'=>'ingresar'));
		
			
		}
	}

?>