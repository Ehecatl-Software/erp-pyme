<?php
class AccountController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
    }
	
	public function getLoginForm(){
		$form = new Zend_Form();
		$form->setAction("authenticate");
		$form->setMethod("post");
		$form->setName("loginform");
		
		//Create text elements
		$userElement = new Zend_Form_Element_Text("username");
		$userElement->setLabel("Usuario: ");
		$userElement->setRequired(true);
		
		//Create password element
		$passwordElement = new Zend_Form_Element_Password("password");
		$passwordElement->setLabel("Password:");
		$passwordElement->setRequired(true);
		
		//Create the submit button
		$submitButtonElement = new Zend_Form_Element_Submit("submit");
		$submitButtonElement->setLabel("Log In");
		
		//Add Elements to form
		$form->addElement($userElement);
		$form->addElement($passwordElement);
		$form->addElement($submitButtonElement);
	return $form;
	}
	
	public function loginAction(){
			//Initialize the form for the view.
			$this->view->form = $this->getLoginForm();
	}
	
	public function authenticateAction(){
			$form = $this->getLoginForm();
			if($form->isValid($_POST)){
					
				//Initialize the variables
				$username = $form->getValue("username");
				$password = $form->getValue("password");
				$password = md5($password);
				
				//Create a db object
				require_once APPLICATION_PATH ."/models/Db/Db_Db.php";
				$db = Db_Db::conn();
			
				//Quote values
				$username = $db->quote($username);
				$password = $db->quote($password);
				//Check if the user is in the system and active
					
				$statement = "SELECT COUNT(id_user) AS total From users
						WHERE username = ".$username."
						AND password = ".$password."
						AND status = 'active'";
				$results = $db->fetchOne($statement);
					
				//If we have at least one row then the users
				if($results == 1){
			
					//Fetch the user's data
						$statement = "SELECT id_user, username FROM users
							WHERE username = ".$username."
							AND password = ".$password;
						$results = $db->fetchRow($statement);
			
				//Set the user's session
						$_SESSION['id'] = $results['id'];
						$_SESSION['username'] = $results['username'];
				
				//Forward the user to the profile page
				$this->_forward("index","index");
			
		}else{

			//Set the error message and re-display the login page.
			$this->view->message = "Usuario o password incorrecto, intente nuevamente";
			$this->view->form = $form;
			$this->render("login");
			echo $this->view->message;
		}
			}else{
				$this->view->form = $form;
				$this->render("login");
			}
	}

    public function viewUsersAction()
    {
        // action body
			require_once APPLICATION_PATH ."/models/Db/Db_Db.php";
			
			$db = Db_Db::conn(); 
			   try{ 
			      //Create the SQL statement to select the data. 
			      $statement = "SELECT *
								FROM users"; 
					      //Fetch the data 
					      $results = $db->fetchAll($statement); 
					      //Set the view variable. 
					      $this->view->users = $results; 
					   }catch(Zend_Db_Exception $e){ 
					      echo $e->getMessage(); 
				 }				 
    }
	


}



