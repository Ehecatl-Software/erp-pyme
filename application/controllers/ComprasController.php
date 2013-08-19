<?php

class ComprasController extends Zend_Controller_Action
{

    public function init() {
		$identity = new Model_Acl();
		if(!$identity->isLogged())
			$this->_redirect('/user/login');
		$roles = $identity->getRoles();
		//check if role is admin, you can request for ventas, almacen, compras

		if(!($roles['admin'] == 1 || $roles['compras'] == 1))
			$this->_redirect('/user/index');        

		$identity = Zend_Auth::getInstance()->getIdentity();
		$username = $identity->username;
		$dw=date(D);//this day of the week, numeric
		$d=date(j);//this day
		$m=date(m);//this month
		$y=date(o);//this year
		
		switch($dw){
			case 'Mon':
				$dia = 'Lunes';
				break;
			case 'Tue':
				$dia = 'Martes';
				break;
			case 'Wed':
				$dia = 'Miércoles';
				break;
			case 'Thu':
				$dia = 'Jueves';
				break;
			case 'Fri':
				$dia = 'Viernes';
				break;
			case 'Sat':
				$dia = 'Sábado';
				break;
			case 'Sun':
				$dia = 'Domingo';
				break;
			default: break;
		}
		
		switch ($m){
			case 1:
				$mes = 'Enero';
				break;
			case 2:
				$mes = 'Febrero';
				break;
			case 3:
				$mes = 'Marzo';
				break;
			case 4:
				$mes = 'Abril';
				break;
			case 5:
				$mes = 'Mayo';
				break;
			case 6:
				$mes = 'Junio';
				break;
			case 7:
				$mes = 'Julio';
				break;
			case 8:
				$mes = 'Agosto';
				break;
			case 9:
				$mes = 'Septiembre';
				break;
			case 10:
				$mes = 'Octubre';
				break;
			case 11:
				$mes = 'Noviembre';
				break;
			case 12:
				$mes = 'Diciembre';
				break;
			default: break;
		}
		
		$fecha = $dia." ".$d." de ".$mes." de ".$y;

		$this->view->roles = $roles;
		$this->view->username = $username;
		$this->view->fecha = $fecha;
	}

    public function indexAction()
    {
        // Menu principal
    }

	public function menuAction(){
		// funcion de menu principal
	}
	
	public function agregaproveedorAction(){
		$form = $this->getAddProvForm();
		if($form->isValid($_POST)){
			$nombre = $form->getValue("nombre");
			$direcc = $form->getValue("domicilio");
			$rfc = $form->getValue("rfc");
			$contac = $form->getValue("contacto");
			$email = $form->getValue("email");
			$tel1 = $form->getValue("telefono1");
			$tel2 = $form->getValue("telefono2");
			if($tel2 == ""){$tel2 = NULL; }
			$ciudad  = $form->getValue("ciudad");
			$pais = $form->getValue("pais");
			$web = $form->getValue("web");
			$distrib = $form->getValue("distribucion");

			$provArray=array(
				"contacto" => $contac,
				"nombre" => $nombre,
				"rfc" => $rfc,
				"distr_exclusiva" => $distrib,
				"domicilio" => $direcc,
				"email" => $email,
				"tel1" => $tel1,
				"tel2" => $tel2,
				"pag_web" => $web,
				"ciudad" => $ciudad, 
				"pais" => $pais,
			); 
					
			//Create a db object
			require_once APPLICATION_PATH ."/models/Db/Db_Db.php";
			$db = Db_Db::conn();
			
			$insProv = $db->insert("proveedores",$provArray);			
						
			if(!$insProv){
				$this->view->message = "Problemas con la Base de Datos, Intente más tarde";
			} else {
				$this->view->message = "Inserción Exitosa";
			}

			$this->view->proveedor = $insProv;

		} else {
			$this->view->message = "Hay errores en el formulario";
		 	$this->view->form = $form;
		}
	}

	public function getAddProvForm(){
		$form = new Zend_Form();
		$form->setAction("agregaproveedor");
		$form->setMethod("post");
		$form->setName("addprovform");
		
		//Create text elements
		$nombElement = new Zend_Form_Element_Text("nombre");
		$nombElement->setLabel("Nombre: ");
		$nombElement->setRequired(true);
		$dirElement = new Zend_Form_Element_Text("domicilio");
		$dirElement->setLabel("Domicilio: ");
		$dirElement->setRequired(true);
		$rfcElement = new Zend_Form_Element_Text("rfc");
		$rfcElement->setLabel("R.F.C.: ");
		$contElement = new Zend_Form_Element_Text("contacto");
		$contElement->setLabel("Contacto: ");
		$contElement->setRequired(true);
		$emailElement = new Zend_Form_Element_Text("email");
		$emailElement->setLabel("Correo: ");
		$emailElement->setRequired(true);
		$tel1Element = new Zend_Form_Element_Text("telefono1");
		$tel1Element->setLabel("Teléfono 1: ");
		$tel1Element->setRequired(true);
		$tel2Element = new Zend_Form_Element_Text("telefono2");
		$tel2Element->setLabel("Teléfono 2: ");
		$ciudadElement = new Zend_Form_Element_Text('ciudad');
		$ciudadElement->setLabel('Ciudad: ');
		$ciudadElement->setRequired(true);
		$countries = array('multiOptions' => array("MX" => "México", "US" => "United States", "UK" => "United Kingdom"));
		$paisElement = new Zend_Form_Element_Select('pais',$countries);
		$paisElement->setLabel("País: ");
		$paisElement->setRequired(true);
		$webElement = new Zend_Form_Element_Text("web");
		$webElement->setLabel("Web Site: ");
		$webElement->setRequired(true);

		//Create Text Validators
		$nombElement->addValidator( new Zend_Validate_StringLength(5,50) );
		$dirElement->addValidator( new Zend_Validate_StringLength(5,128) );
		$rfcElement->addValidator( new Zend_Validate_StringLength(12,15) );
		$contElement->addValidator( new Zend_Validate_StringLength(1,50) );
		$emailElement->addValidator(new Zend_Validate_EmailAddress() );
		$tel1Element->addValidator( new Zend_Validate_Digits() );
		$tel2Element->addValidator( new Zend_Validate_Digits() );
		$ciudadElement->addValidator(new Zend_Validate_StringLength(3,50) );
		$paisElement->addValidator( new Zend_Validate_StringLength(1,50) );
		$webElement->addValidator( new Zend_Validate_StringLength(7,50) );
		
		//Create Error Messages
		$nombElement->addErrorMessage('Nombre requerido, Longitud: Max=50, Min=5');
		$dirElement->addErrorMessage('Domicilio requerido, Longitud: Max=50, Min=5');
		$rfcElement->addErrorMessage('RFC requerido, Longitud: Max=15, Min=13');
		$contElement->addErrorMessage('Contato requerido, Longitud: Max=50, Min=1');
		$emailElement->addErrorMessage('Correo requerido, Formato: ejemplo@ejemplo.com');
		$tel1Element->addErrorMessage('Telefono requerido, Solo Digitos');
		$tel2Element->addErrorMessage('Solo Digitos');
		$ciudadElement->addErrorMessage('Ciudad requerida, Longitud: Max=50, Min=1');
		$paisElement->addErrorMessage('Pais requerido, Longitud: Max=50, Min=1');		
		$webElement->addErrorMessage('Sitio Web requerido, Longitud: Max=50, Min=1');
		
		//Create All Filters for HTML TAGS
		$nombElement->addFilter( new Zend_Filter_StripTags() );
		$dirElement->addFilter( new Zend_Filter_StripTags() );
		$rfcElement->addFilter( new Zend_Filter_StripTags() );
		$contElement->addFilter( new Zend_Filter_StripTags() );
		$emailElement->addFilter( new Zend_Filter_StripTags() );
		$tel1Element->addFilter( new Zend_Filter_StripTags() );
		$tel2Element->addFilter( new Zend_Filter_StripTags() );
		$ciudadElement->addFilter( new Zend_Filter_StripTags() );
		$paisElement->addFilter( new Zend_Filter_StripTags() );
		$webElement->addFilter( new Zend_Filter_StripTags() );
		
		//Create textarea elements
		$distElement = new Zend_Form_Element_Text("distribucion");
		$distElement->setLabel("Distribución Exclusiva: ");
		$distElement->addValidator( new Zend_Validate_StringLength(1,256) );
		$distElement->addFilter( new Zend_Filter_StripTags() );
		$distElement->addErrorMessage('Distribucion requerido, Longitud: Max=256, Min=1');
		

		//Create the submit button
		$submitButtonElement = new Zend_Form_Element_Submit("submit_addprov");
		$submitButtonElement->setLabel("Agregar");
		
		//Add Elements to form
		$form->addElement($nombElement);
		$form->addElement($dirElement);
		$form->addElement($contElement);
		$form->addElement($emailElement);
		$form->addElement($rfcElement);
		$form->addElement($tel1Element);
		$form->addElement($tel2Element);
		$form->addElement($ciudadElement);
		$form->addElement($paisElement);
		$form->addElement($webElement);
		$form->addElement($distElement);
		$form->addElement($submitButtonElement);
		
		return $form;				
	}
	
	public function altaproveedorAction(){
		$this->view->form = $this->getAddProvForm();
	}
	
	public function resultadosproveedorAction(){
		$form = $this->getSearchProvForm();
		if($form->isValid($_POST)){
				
			//Initialize the variables
			$id = $form->getValue("id_bus");
			$nomb = $form->getValue("nombre_bus");
			
			//Create a db object
			require_once APPLICATION_PATH ."/models/Db/Db_Db.php";
			$db = Db_Db::conn();
			
			if(isset($nomb)){
				$query = "SELECT * FROM proveedores WHERE id_proveedor = $id and nombre LIKE '%$nomb%' ";				
			} else {
				$query = "SELECT * FROM proveedores WHERE id_proveedor = $id";
			}
			
			$message = "Búsqueda = Número: ".$id;
			if($nomb) { $message.= " - Nombre: ".$nomb; }
			
			$this->view->message = $message; 
			
			$res = $db->fetchAll($query);
			$this->view->results = $res;
		} else {
			$this->_forward( 'busquedasproveedor', 'Compras', null, null );
		}
	}

	public function getSearchProvForm(){		
		$form = new Zend_Form();
		$form->setAction("resultadosproveedor");
		$form->setMethod("post");
		$form->setName("addprodform");
		
		//Create text elements
		$idElement = new Zend_Form_Element_Text("id_bus");
		$idElement->setLabel("Núm. Proveedor: ");
		$idElement->setRequired(true);
		$nombElement = new Zend_Form_Element_Text("nombre_bus");
		$nombElement->setLabel("Nombre: ");
		$nombElement->setRequired(false);
		$submitButtonElement = new Zend_Form_Element_Submit("submit_bus");
		$submitButtonElement->setLabel("Buscar");	

		//Create Validators
		$idElement->addValidator( new Zend_Validate_Digits() );
		$nombElement->addValidator( new Zend_Validate_StringLength(0,256) );

		//Create Filters
		$idElement->addFilter( new Zend_Filter_StripTags() );
		$nombElement->addFilter( new Zend_Filter_StripTags() );		

		$form->addElement($idElement);
		$form->addElement($nombElement);
		$form->addElement($submitButtonElement);	
		return $form;	
	}
	
    public function busquedasproveedorAction()
    {
		$this->view->form = $this->getSearchProvForm();
    }

    public function visualizaproveedorAction()
    {
        // action body
		require_once APPLICATION_PATH ."/models/Db/Db_Db.php";
		$db = Db_Db::conn();

		$query = "SELECT * FROM proveedores";
		$objs = $db->fetchAll($query);
		$this->view->results = $objs;
    }

	public function verproductosprovAction(){
		require_once APPLICATION_PATH ."/models/Db/Db_Db.php";
		$db = Db_Db::conn();
		
		$id_proveedor=$_GET['id_prov'];
		$query = "SELECT * FROM proveedores WHERE id_proveedor = $id_proveedor";
		$results = $db->fetchAll($query);
		$this->view->proveedor = $results;

		$query = "SELECT * FROM existencias";
		$result = $db->fetchAll($query);
		$i=0;
		foreach($result as $existente){
			$id_producto=$existente['id_producto'];
			$query = "SELECT * FROM productos WHERE id_producto=$id_producto and id_proveedor=$id_proveedor";
			$results = $db->fetchAll($query);
			
			foreach($results as $producto){
				$producto['existencia']=$existente['cantidad'];
				$objArray[$i] = $producto;				
			}
			$i++;
		}
		$this->view->existences = $objArray;		
	}

	public function actualizaproveedorAction(){
		$form = $this->getAddProvForm();
		if($form->isValid($_POST)){
			$nombre = $form->getValue("nombre");
			$direcc = $form->getValue("domicilio");
			$rfc = $form->getValue("rfc");
			$contac = $form->getValue("contacto");
			$correo = $form->getValue("mail");
			$moneda = $form->getValue("moneda");
			$tel = $form->getValue("telefono");
			$pais = $form->getValue("pais");
			$credit = $form->getValue("credito");
			$web = $form->getValue("web");
			$distrib = $form->getValue("distribucion");
			$icoterm = $form->getValue("icoterm");
			
			$provArray=array(
				"contacto" => $contac,
				"nombre" => $nombre,
				"rfc" => $rfc,
				"moneda" => $moneda,
				"distr_exclusiva" => $distrib,
				"icoterm" => $icoterm,
				"domicilio" => $direcc,
				"mail" => $correo,
				"tel" => $tel,
				"pag_web" => $web,
				"pais" => $pais, 
				"credito" => $credit
			); 
					
			$id = $_GET['id_prov'];
			$cond[] = "id_proveedor=$id";

			//Create a db object
			require_once APPLICATION_PATH ."/models/Db/Db_Db.php";
			$db = Db_Db::conn();
						
			$modProv = $db->update("proveedores",$provArray,$cond);			
						
			if($modProv==1){
				$this->view->message = "Modificación Exitosa";
			} else {
				$this->view->message = "Problemas con la Base de Datos, Intente más tarde";
			}
			$this->view->proveedor = $modProv;
		} else {
		 	$this->view->form = $form;
		}		
	}

	public function modificaproveedorAction(){
		$id = $_GET['id_prov'];
		$form = $this->getAddProvForm();
		$form->setAction("actualizaproveedor?id_prov=".$id);

		require_once APPLICATION_PATH ."/models/Db/Db_Db.php";
		$db = Db_Db::conn();

		$query = "SELECT * FROM proveedores WHERE id_proveedor=$id";
		$results = $db->fetchAll($query);
		
		foreach($results as $result){
			$form->contacto->setValue($result['nombre']);
			$form->nombre->setValue($result['nombre']);
			$form->domicilio->setValue($result['domicilio']);
			$form->rfc->setValue($result['rfc']);
			$form->contacto->setValue($result['contacto']);
			$form->email->setValue($result['email']);
			$form->telefono1->setValue($result['tel1']);
			$form->telefono2->setValue($result['tel2']);
			$form->ciudad->setValue($result['ciudad']);
			$form->pais->setValue($result['pais']);
			$form->web->setValue($result['pag_web']);
			$form->distribucion->setValue($result['distr_exclusiva']);
		}
		
		$this->view->form = $form;
	}
	
	public function creaOrdenForm(){
		$form = new Zend_Form();
		$form->setAction("nueva-orden-compra");
		$form->setMethod("post");
		$form->setName("addprodform");
		$submitButtonElement = new Zend_Form_Element_Submit("submit");
		$submitButtonElement->setLabel("Crear Orden");	
		$form->addElement($submitButtonElement);
		
		return $form;
	}
	
	public function agregaProductoOrdenForm(){
		$form = new Zend_Form();
		Zend_Dojo::enableForm($form);
		$form->setAction("nueva-orden-compra");
		$form->setMethod("post");
		$form->setName("addordenprod");
		
		//Create text elements
		$codIntElement = new Zend_Form_Element_Text("codigo_int");
		$codIntElement->setLabel("Codigo Interno: ");
		$codIntElement->setRequired(false);
		$descElement = new Zend_Form_Element_Text("descrip");
		$descElement->setLabel("Descripcion: ");
		$descElement->setRequired(false);
		
		$submitButtonElement = new Zend_Form_Element_Submit("submit");
		$submitButtonElement->setLabel("Añadir");
		
		/*select element*/
		$selectElement = new Zend_Dojo_Form_Element_FilteringSelect('product');
		$selectElement->setOptions(array(
										'dojoType' => 'dijit.form.FilteringSelect',
										'label' => 'Producto:',
										'autocomplete' => true,
										"jsId"=> "productInput",
      									'storeId'   => 'productStore',
      									'storeType' => 'dojo.data.ItemFileReadStore',
      									'storeParams' => array( 'url' => 'getproducts'),
      									'dijitParams' => array('searchAttr' => 'descripcion')
								));
		 /*select element */		

		//Create Validators
		$codIntElement->addValidator( new Zend_Validate_StringLength(1,20) );
		$descElement->addValidator( new Zend_Validate_StringLength(0,256) );

		//Create Filters
		$codIntElement->addFilter( new Zend_Filter_StripTags() );
		$descElement->addFilter( new Zend_Filter_StripTags() );		

		$form->addElement($codIntElement);
		$form->addElement($descElement);
		
		$form->addElement($selectElement);
		/*
		$form->addElement('FilteringSelect',
                'filteringSelect',
                array(
                    'label' => 'FilteringSelect (select)',
                    'autocomplete' => true,
                	'dojoType'       => array('dijit.form.FilteringSelect'),
                    'store'   => 'productStore2',
      				'storeType' => 'dojo.data.ItemFileReadStore',
      				'storeParams' => array('url' => "getproducts"),
      				'dijitParams' => array('searchAttr' => 'descripcion'),
                	
                   ));
               
                	'storeId'   => 'productStore',
      				'storeType' => 'dojo.data.ItemFileReadStore',
      				'storeParams' => array('url' => "getproducts"),
      				'dijitParams' => array('searchAttr' => 'descripcion'),*/
                
        		  	
		$form->addElement($submitButtonElement);
			
		return $form;	
				
	}
	
	public function ordenesCompraAction(){
		$this->view->message = "Deshabilitado";
	}
	
	public function getCompraOrdenForm($odcomp){
		$form = new Zend_Form();
		$form->setAction("nueva-orden-compra");
		$form->setMethod("post");
		$form->setName("formordencompra");
		//items imput
		$itemsElement = new Zend_Form_Element_Hidden('items');
		$itemsElement->setValue($ordcomp->items);
		$itemsElement->addDecorators(array('viewHelper',array('HtmlTag',
						  array('tag' => 'dd', 'class' => 'noDisplay' ))));	
		$form->addElement($itemsElement);
		
		$submitButtonElement = new Zend_Form_Element_Submit("submit");
		$submitButtonElement->setAttrib('style','margin-bottom:10px;margin-top:-20px;margin-left:0px;position:absolute;');
		$submitButtonElement->setLabel("Generar Orden");	
		$cancelButtonElement = new Zend_Form_Element_Submit("cancel");
		$cancelButtonElement->setAttrib('style','margin-bottom:10px;margin-top:-20px;margin-left:750px;');
		$cancelButtonElement->setLabel("Cancelar Orden");	
		
		$form->addElement($cancelButtonElement);
		$form->addElement($submitButtonElement);
		
		return $form;
	}
	
	public function nuevaOrdenCompraAction(){
		Zend_Dojo::enableView($this->view);
		//Zend_Dojo_View_Helper_Dojo::setUseProgrammatic();
		if(!isset($ordcomp)){
			$ordcomp = new Zend_Session_Namespace('ordcomp');
			$n=0;
		}
		
		if($this->_getParam('submit')== 'Crear Orden'){
			// adding num order to ordcomp session
			$date = date("d.m.y.hms");
			$numorden = 'OC.'.$date;
			$ordcomp->num = $numorden;

			// getting the id_user
			$identity = Zend_Auth::getInstance()->getIdentity();
			$id_user = $identity->id_user;
			
			//creating order to database
			$norden = new Model_ordenesCompras();
			$norden->create_order($numorden,$id_user,1,1);
		}
		
		if(!isset($ordcomp->num)){
			$this->view->form = $this->creaOrdenForm();
					
		} else {
				$this->view->form = NULL;			
				$this->view->productSelect = 1;	
		    	
				
				if($this->_getParam('addProduct') =='Agregar'){			
					$id_producto = $this->_getParam('id_producto');
				
					//getting info Product
					$data_model = new Model_Productos();
					$info_product = $data_model->getProduct($id_producto);
				
					//getting productcode
					$productCode = $info_product['codigo_interno'];
					
					$items = $ordcomp->items;
					$items ++;
					for($i=0;$i<$items;$i++){
						//Searching for a product already selected for basket
						//print_r($ordcomp->item);
						if($ordcomp->item[$i][codigo_interno] == $productCode){
							$ordcomp->item[$i][cant] ++;
							$already = 1;
						}
					}
				
					if($already != 1){
						//adding new item with productcode
						$n = $ordcomp->items;		
						$ordcomp->item[$n][codigo_interno] = $productCode;
						
						//adding id_producto
						$ordcomp->item[$n][id_producto] = $id_producto;
						
						//adding description to product
						$desc_prod = $info_product['descripcion'];
						$ordcomp->item[$n][descripcion] = $desc_prod;
				
						//adding quantity
						$ordcomp->item[$n][cant] = 1;			
						$ordcomp->items ++;
					}
				}elseif($this->_getParam('cancel')== 'Cancelar Orden'){
						unset($ordcomp->items);
						unset($ordcomp->item);
						unset($ordcomp->num);
						$this->view->messageuser = "Orden Cancelada Exitósamente";
					}elseif($this->_getParam('submit') == 'Generar Orden'){
							
							$data_ordenCompras = new Model_ordenesCompras();
							$data_Info_Orden = $data_ordenCompras->get_Info_From_Numorden($ordcomp->num); 
							
							$data_productosOrdenesCompras = new Model_productosOrdenesCompras();
							for($i=0;$i<$ordcomp->items;$i++){
								//echo "orden de compras:" . $ordcomp->item[$i][id_producto] ."orden de compra: " . $data_Info_Orden['id_orden_compra'] . "items: " . $ordcomp->item[$i][cant];
								$data_productosOrdenesCompras->add_Product_To_Order($ordcomp->item[$i][id_producto], $data_Info_Orden['id_orden_compra'], $ordcomp->item[$i][cant]);
							}	
							unset($ordcomp->items);
							unset($ordcomp->item);
							unset($ordcomp->num);
							$this->view->messageuser = "orden de compra generada exitósamente";
					}else{
						$already = 0;
						$ordcomp->items = 0;
		 			}
		 	//print_r($ordcomp->num);
			$this->view->ordcomp = $ordcomp;
			$this->view->form1 = $this->getCompraOrdenForm($ordcomp);
		}
			
	}
	
	public function getproductsAction()
	{
		$data = new Model_Productos();
		$result = $data->getProducts();
		$dojoData = new Zend_Dojo_Data('id_producto',$result,'descripcion');
		echo $dojoData->toJson();
    	$this->_helper->viewRenderer->setNoRender();
	}
	
}