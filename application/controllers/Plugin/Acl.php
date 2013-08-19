<?php
class ERP_Controller_Plugin_Acl extends Zend_Controller_Plugin_Abstract
{
	const ROLE_ALMACEN = 'almacen';
	const ROLE_VENTAS = 'ventas';
	const ROLE_VENTAS_M = 'ventas_m';
	const ROLE_COMPRAS = 'compras';
	const ROLE_TESORERIA = 'tesoreria';
	const ROLE_ADMIN = 'admin';
	
	public function preDispatch(Zend_Controller_Request_Abstract $request)
	{
		$acl = new Zend_Acl();
		$acl->addRole(new Zend_Acl_Role(self::ROLE_ALMACEN));
		$acl->addRole(new Zend_Acl_Role(self::ROLE_VENTAS));
		$acl->addRole(new Zend_Acl_Role(self::ROLE_VENTAS_M),self::VENTAS);
		$acl->addRole(new Zend_Acl_Role(self::ROLE_COMPRAS));
		$acl->addRole(new Zend_Acl_Role(self::ROLE_TESORERIA));
		$acl->addRole(new Zend_Acl_Role(self::ROLE_ADMIN));
		
		$acl->addResource(new Zend_Acl_Resource('ventas'));
		$acl->addResource(new Zend_Acl_Resource('ventas_m'),'ventas');
		$acl->addResource(new Zend_Acl_Resource('compras'));
		$acl->addResource(new Zend_Acl_Resource('almacen'));
		$acl->adResource(new Zend_Acl_Resource('admin'));
		
		$acl->allow('almacen','almacen');
		$acl->allow('ventas','ventas');
		$acl->allow('ventas_m','ventas');
		$acl->allow('ventas_m','ventas_m');
		$acl->allow('tesoreria','tesoreria');
		$acl->allow('tesoreria','tesoreria');
		$acl->allow('admin',null);
		
		/*
		$auth = Zend_Auth::getInstance();
			if($auth->hasIdentity()){
				$identity = $auth->getIdentity();
				$u_type_4 = $identity->u_type_4;
				if($u_type_4 == 1){$role = 'admin';}
				
				$controller = $request->controller; 
				$action = $request->action; 
		
				if (!$acl->isAllowed($role, $controller, $action)) { 
					$request->setControllerName('user'); 
        			$request->setActionName('index'); 
				}
			}else{
				$this->_redirect('/user/login');
			}*/
			
	
	}
	
}