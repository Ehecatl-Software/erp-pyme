<?php
class Model_Acl
{

public function isLogged(){
		$identity = Zend_Auth::getInstance();
			if(!$identity->hasIdentity()){
				return false;
			}else{
				return true;
			}
	}

public function getRoles(){
				$identity = Zend_Auth::getInstance()->getIdentity();
				$roles = array('ventas'=>0, 'almacen'=>0, 'compras'=>0, 'admin' => 0);
	
				$u_type_1 = $identity->u_type_1;
				$u_type_2 = $identity->u_type_2;
				$u_type_3 = $identity->u_type_3;
				$u_type_4 = $identity->u_type_4;
				if($u_type_1 == '1'){$roles['ventas'] = 1; }
				if($u_type_2 == '1'){$roles['almacen'] =1; }
				if($u_type_3 == '1'){$roles['compras'] =1; }
				if($u_type_4 == '1'){$roles['admin']=1; }
				
				return $roles;
	}

}
