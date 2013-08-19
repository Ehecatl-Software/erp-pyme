<?php
class UserController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {	
     		if(!Zend_Auth::getInstance()->hasIdentity())
    		{
				$this->_redirect('/user/login');
    		}else
    		{
    			$identity = new Model_Acl();
				$roles = $identity->getRoles();
    		    $this->userlocation($roles);
 						
			/*	
 				$identity = Zend_Auth::getInstance()->getIdentity();
        		$this->view->identity = $identity;
        	
        		echo $identity->u_type_1;
        		echo $identity->u_type_2;
        		echo $identity->u_type_3;
        		echo $identity->u_type_4;*/
        		
    		} 
    }
    
    public function loginAction(){
    	$userForm = new Form_User(); 
    	$userForm->setAction('/user/login'); 
    	$userForm->removeElement('firstname'); 
    	$userForm->removeElement('lastname'); 
    	$userForm->removeElement('email');
		//
		
    if ($this->_request->isPost() && $userForm->isValid($_POST)) { 
        $data = $userForm->getValues(); 
        //set up the auth adapter      
        $db = Zend_Db_Table::getDefaultAdapter(); 

        $authAdapter = new Zend_Auth_Adapter_DbTable($db, 'users', 
            'username', 'password'); 
        //set the username and password 
        $authAdapter->setIdentity($data['username']); 
        $authAdapter->setCredential(md5($data['password'])); 
        
        $result = $authAdapter->authenticate(); 
        if ($result->isValid()) { 
            // store the username, first and last names of the user 
            $auth = Zend_Auth::getInstance(); 
            $storage = $auth->getStorage(); 
            $storage->write($authAdapter->getResultRowObject( 
                array('id_user','username' , 'firstname' , 'lastname', 'email','u_type_1','u_type_2','u_type_3','u_type_4','status'))); 
            return $this->_forward('index');        
        } else { 
            $this->view->loginMessage = "Disculpe, su usuario o contraseña son incorrectos"; 
        } 
    }
      $this->view->form = $userForm;
    	
    }
    
    public function logoutAction()
    {
    	$authAdapter = Zend_Auth::getInstance(); 
    	$authAdapter->clearIdentity();
    	Zend_Session::destroy();
    	/* 
    	if($_SESSION['ordsal']){unset($_SESSION['ordsal']);}
    	if($_SESSION['ordcomp']){ unset($_SESSION['ordcomp']);}*/
    	$this->_redirect("/user/login?logout=1");
    }
    
    function userlocation($roles)
    {	
    
    	if($roles['admin']==1){ $this->_redirect('/admin/index'); 
    		    	}else { if($roles['almacen']==1){ $this->_redirect('/almacen/index'); } 
 					   else { if($roles['compras']){ $this->_redirect('/compras/index'); }
 					   		  else { if($roles['ventas']==1) { $this->_redirect('/ventas/index'); } 
 					   		  		 else { $this->_redirect('/user/login'); } 
 					   		  }
 					   } 
 				}
    }
    
}