<?php
class Model_User  extends Zend_Db_Table_Abstract
{ 
	protected $_name = 'users';
	public function getType($id_user)
		{
			
			return $this->fetchRow($this->select()->where('id_user =?', $id_user));
		}
		
	public function updateType($id_user, $user_type)
	{
		$u_type = array();
		for($i=1;$i<5;$i++){
			$u_type[$i] = 0;
			for($j=0;$j<sizeof($user_type);$j++){
				if($user_type[$j] == $i)
			 		$u_type[$i] = 1;
			}
		}
		
		$data = array(
				'u_type_1'	=> $u_type[1],
				'u_type_2'	=> $u_type[2],
				'u_type_3'	=> $u_type[3],
				'u_type_4'	=> $u_type[4],
		);
		
		$where = $this->getAdapter()->quoteInto('id_user = ?', $id_user);
		return $this->update($data, $where);
		
	}
	
	public function insertUser($firstname, $lastname,$username, $email, $password)
	{	
		$data = array(
						'firstname' => $firstname,
						'lastname' => $lastname,
						'username' => $username,
						'email' => $email,
						'password' => $password,
						'status' => 'active'
					);
		
		return $this->insert($data);
	}
	
	public function deleteUser($id_user){
		$where = $this->getAdapter()->quoteInto('id_user = ?', $id_user);
		return $this->delete($where); //the number of rows deleted
	}
	
	public function updateUser($id_user,$firstname, $lastname, $email, $username)
	{
		$rowUser = $this->find($id_user)->current(); 
    	if($rowUser) { 
        	// update the row values 
        	$rowUser->firstname = $firstname; 
        	$rowUser->lastname = $lastname; 
        	$rowUser->email = $email; 
        	$rowUser->username = $username; 
        	$rowUser->save(); 
        	//return the updated user 
        return $rowUser; 
    	}else{ 
        	throw new Zend_Exception("Problema al actualizar el usuario. Usuario no encontrado!"); 
    	} 		
	}
	
	public function getUserName($id_user)
	{
		$where = $this->getAdapter()->quoteInto('id_user = ?', $id_user);
		return $this->fetchRow($where);
	}
	
	public function getUserID($username){
		$where =$this->getAdapter()->quoteInto('username = ?', $username);
		return $this->fetchRow($where);
	}
}

   

?>