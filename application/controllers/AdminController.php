<?php

class AdminController extends Zend_Controller_Action
{
	
	public function init(){
		  
		$identity = Zend_Auth::getInstance();
			if(!$identity->hasIdentity()){
				$this->_redirect('/user/login');
			}else{
				//$identity = Zend_Auth::getInstance()->getIdentity();
				$identity = new Model_Acl();
				$roles = $identity->getRoles();
				
				if($roles['admin'] != 1){
					$this->_redirect('/user/index');
				}
				
				$this->view->roles = $identity->getRoles();
				$this->view->username = $identity->username;
				$this->view->fecha = Model_Info::fecha();
			
			}
			
	}
	
	public function indexAction(){
				$this->_forward('ver-usuarios');	
    }
	
	public function verUsuariosAction()
    {
    	
    		
			require_once APPLICATION_PATH ."/models/Db/Db_Db.php";
			
			
			$db = Db_Db::conn(); 
			   try{  
			      $statement = "SELECT *
								FROM users"; 
					      $results = $db->fetchAll($statement); 
					      $this->view->users = $results; 
					   }catch(Zend_Db_Exception $e){ 
					      echo $e->getMessage(); 
				 }
				 //dojo test
				Zend_Dojo::enableView($this->view);				 
    }

	public function rolUsuarioAction()
	{
		$router = new Zend_Controller_Router_Rewrite();
		$route = new Zend_Controller_Router_Route(
				'rol-usuario/:id_user',
						array(
						'controller' => 'admin',
						'action' => 'rol-usuario'
					),
				array(
					//alpha, numbers and _-
					'id_user' => '[a-zA-Z-_0-9]+'
						)
				);
		
		
		$this->view->setrolform = $this->getRolForm();	
	}
	
	public function getRolForm()
	{
		$id_user = $this->_request->getParam('id_user');
		
		$form = new Zend_Form();
		$form->setAction('/admin/actualizarol');
		$form->setMethod('post');
		$form->setName("rolform");
		//$user_type = $this->getUserType($id_user);
		
		$info_user = new Model_User();
		$user_type = $info_user->getType($id_user);
		$this->view->ut = $user_type;
		
		//Multicheckbox
		$userElement = new Zend_Form_Element_MultiCheckbox("user_type");
		//$userElement->setLabel("Rol de Usuario: ");
		//$userElement->setRequired(true);
		$userElement->addMultiOption(1,'Ventas');
		$userElement->addMultiOption(2,'Almacen');
		$userElement->addMultiOption(3,'Compras');
		$userElement->addMultiOption(4,'Admin');
		
		//hidden input
		$iduserElement = new Zend_Form_Element_Hidden("id_user");
		$iduserElement->setValue($id_user);
		
		$multiVals = array();
		for($i=1;$i<5;$i++){
			$a = 'u_type_' . $i;
			if($user_type[$a] == 1)
			$multiVals[] = $i;
		}
		$userElement->setValue($multiVals);
		
		//submit bottom
		$submitButtonElement = new Zend_Form_Element_Submit("submit");
		$submitButtonElement->setLabel("Actualizar");
		
		$form->addElement($userElement);
		$form->addElement($iduserElement);
		$form->addElement($submitButtonElement);
		return $form;
	}
	
	public function actualizarolAction(){
		$id_user = $this->_request->getParam('id_user');
		$user_type = $this->_request->getParam('user_type');
		
		$info_user = new Model_User();
		$info_response = $info_user->updateType($id_user,$user_type);
		if($info_response == 1){
			$str = 'admin/rol-usuario/id_user/' . $id_user;
			$this->_redirect($str);
		}
	}
	
	public function crearUsuarioAction(){
		
		$this->view->userForm = $this->getUserForm();
	}
	
	public function getUserForm(){
		$form = new Zend_Form();
		$form->setAction('/admin/insertausuario');
		$form->setMethod('post');
		$form->setName("userform");
		
		//input for firstname
		$firstnameElement = new Zend_Form_Element_Text("firstname_user");
		$firstnameElement->setLabel("Nombre:");
		$firstnameElement->setRequired(true);
		
		//input for lastname
		$lastnameElement = new Zend_Form_Element_Text("lastname_user");
		$lastnameElement->setLabel("Apellido: ");
		$lastnameElement->setRequired(true);
		
		//input for email
		$emailElement = new Zend_Form_Element_Text("email_user");
		$emailElement->setLabel("Email:");
		$emailElement->setRequired(true);
		
		//input for username
		$usernameElement = new Zend_Form_Element_Text("username_user");
		$usernameElement->setLabel("Usuario:");
		$usernameElement->setRequired(true);
		
		//input for password
		$passwordElement = new Zend_Form_Element_Password("password_user");
		$passwordElement->setLabel("Password:");
		$passwordElement->setRequired(true);
		
		//submit button
		$submitElement = new Zend_Form_Element_Submit("submit");
		$submitElement->setLabel("crear");
		
		$form->addElement($firstnameElement);
		$form->addElement($lastnameElement);
		$form->addElement($usernameElement);
		$form->addElement($emailElement);
		$form->addElement($usernameElement);
		$form->addElement($passwordElement);
		$form->addElement($submitElement);
		
		return $form;
		
	}
	
	public function insertausuarioAction(){
		$form = $this->getUserForm();
			if($form->isValid($_POST)){
					
					$firstname = $form->getValue("firstname_user");
					$lastname = $form->getValue("lastname_user");
					$username = $form->getValue("username_user");
					$email = $form->getValue("email_user");
					$password = $form->getValue("password_user");
					$password = md5($password);
					
					$info_user = new Model_User();
					$info_response = $info_user->insertUser($firstname,$lastname,$username,$email,$password);
					echo $info_response;
					if($info_response){
						$this->view->message = "Usuario creado exit�samente";
						$this->render("crear-usuario");
					}else{
						$this->view->message = "No fue posible crear el usuario, intente nuevamente";
						$this->render("crear-usuario");
					}
			}else{
				$this->view->userForm = $form;
				$this->render("crear-usuario");
			}			
		$this->_helper->viewRenderer->setNoRender();
	}
	
	public function actualizausuarioAction(){
		$router = new Zend_Controller_Router_Rewrite();
		$route = new Zend_Controller_Router_Route(
				'actualizausuario/:id_user',
						array(
						'controller' => 'admin',
						'action' => 'actualizausuario'
					),
				array(
					//alpha, numbers and _-
					'id_user' => '[0-9]+'
						)
				);
		$form = new Form_User();
		$form->setAction('/admin/actualizausuario');
		$form->removeElement('password'); 
		$userModel = new Model_User();
			if ($this->_request->isPost()){
				if ($form->isValid($_POST)) { 
				
				$info_user = $userModel->updateUser($form->getValue('id_user'), 
                 						$form->getValue('firstname'), 
                 						$form->getValue('lastname'), 
                						$form->getValue('email'),
                 						$form->getValue('username') 
            							);
				
				}
			}else{
				 $id = $this->_request->getParam('id_user');
				 $currentUser = $userModel->find($id)->current(); 
				 $form->populate($currentUser->toArray());
			}
		$this->view->userForm = $form;
	}
	
	public function eliminausuarioAction(){
		$router = new Zend_Controller_Router_Rewrite();
		$route = new Zend_Controller_Router_Route(
				'eliminausuario/:id_user',
						array(
						'controller' => 'admin',
						'action' => 'eliminausuario'
					),
				array(
					//alpha, numbers and _-
					'id_user' => '[0-9]+'
						)
				);
		$id_user = $this->_request->getParam('id_user');
		$modelUser = new Model_User();
		$response_data = $modelUser->deleteUser($id_user);
		if($response_data > 0){
			$this->view->message_user = "usuario con id: "
										.$id_user
										."eliminado correctamente";
			
		}else{
			$this->view->message_user = "Problema al eliminar el usuario, 
			intente nuevamente";
		}
	}
	
	/*
	public function getUserType($id_user)
	{
		require_once APPLICATION_PATH . "/models/Db/Db_Db.php";
		$db = Db_Db::conn();
		
		try{
			$statement = "SELECT u_type_1,u_type_2,u_type_3,u_type_4 FROM users WHERE id_user=$id_user";
			$result = $db->fetchRow($statement);

		}catch(Zend_Db_Exception $e){ echo $u->getMessage(); }
		return $result;
	}*/
	
}