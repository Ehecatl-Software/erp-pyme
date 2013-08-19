<?php
//new 4 ene hola
class AlmacenController extends Zend_Controller_Action
{
	
    public function init() {
		$identity = new Model_Acl();
		if(!$identity->isLogged())
			$this->_redirect('/user/login');
		$roles = $identity->getRoles();
		
		
		if(!($roles['admin'] == 1 || $roles['almacen'] == 1))
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
        // action body
    }
    
	public function agregarproductoAction()
	{
		$form = $this->getAddProdForm();
		if($form->isValid($_POST)){
				
			//Initialize the variables
			$cod_int = $form->getValue("cod_int");
			$cod_prov = $form->getValue("cod_prov");
			$modelo = $form->getValue("modelo");
			$marca = $form->getValue("marca");
			$tipo = $form->getValue("tipo");
			if($tipo == ""){$tipo = NULL;}
			$pieza = $form->getValue("pieza");
			$descrip = $form->getValue("desc");
			$proced = $form->getValue("proc");
			
			$currency = $form->getValue('moneda');
			$vig = $form->getValue('vig');
			$minexist = $form->getValue('minexist');
			
			$cant = 0;
			//$form->getValue("cant");
			$cost = $form->getValue("cost");
			//$etiq = $form->getValue("etiq");
			$fecha_actual=date("Y/m/d");

			//Create product array
			$prodArray=array(
				"id_user" => 1,
				"id_proveedor" => $cod_prov,
				"codigo_interno" => $cod_int,
				"descripcion" => $descrip,
				"marca" => $marca,
				"modelo" => $modelo,
				"pieza" => $pieza,
				"procedencia" => $proced,
				"fecha" => $fecha_actual,
				"tipo" => $tipo,
				"vigencia" => $vig,
				"minexist" => $minexist,
			); 
					
			//Create a db object
			require_once APPLICATION_PATH ."/models/Db/Db_Db.php";
			$db = Db_Db::conn();
			
			$insProd = $db->insert("productos",$prodArray);	
			$id_product = $db->lastInsertId();		
						
			
			$cantArray = array(
				'id_orden_ingreso' => 2,
				'id_producto' => $id_product,
				'id_user' => 1,
				'cantidad' => 0,
				'cantidad_apartada' => 0,
			);
			try{
				$insExist = $db->insert('existencias',$cantArray);
			} catch(Zend_Db_Exception $error){
				$message = $error->getMessage();
				echo $message;
			}

			$query = "SELECT * FROM porcentajes_costos WHERE id_porcentaje_costos=1";
			$porcentaje = $db->fetchAll($query);				
			foreach($porcentaje as $porcent){
				$precio=$cost;
				$precio*=$porcent['importacion'];
				$precio*=$porcent['gastos_operacion'];
				$precio*=$porcent['tarifa_proteccion'];
				$precio*=$porcent['utilidad'];
			};

			$costArray = array(
				'id_producto' => $id_product,
				'id_currency'	 => $currency,
				'id_proveedor' => $cod_prov,
				'id_porcentaje_costos' => 1,
				'precio_unitario' => $precio,
				'costo_producto' => $cost,
				'fecha' => $fecha_actual
			);
			try{
				$insCosts = $db->insert('costos_productos',$costArray);
			}catch(Zend_Db_Exception $error){
				$message = $error->getMessage();
				echo $message;
			}
            $this->view->message = "Producto Agregado Exitosamente";
			$this->_forward( 'altaproducto', 'Almacen', null, null );
		} else {
		 	$this->view->form = $form;
		}
	}

	public function getAddProdForm()
	{
		$form = new Zend_Form();
		$form->setAction("agregarproducto");
		$form->setMethod("post");
		$form->setName("addprodform");
		
		//Create text elements
		$codIntElement = new Zend_Form_Element_Text("cod_int");
		$codIntElement->setLabel("Codigo Interno: ");
		$codIntElement->setRequired(true);
		
		$modelProveedores = new Model_Proveedores();
		
		$results = $modelProveedores->getAll();
		
		foreach($results as $result){
			$option[$result['id_proveedor']] = $result['nombre'];
		}
		$options = array("multiOptions" => $option);

		$codProvElement = new Zend_Form_Element_Select('cod_prov', $options);
		$codProvElement->setLabel("Clave Proveedor: ");
		$codProvElement->setRequired(true);
		$modElement = new Zend_Form_Element_Text("modelo");
		$modElement->setLabel("Modelo: ");
		$modElement->setRequired(true);
		$marcaElement = new Zend_Form_Element_Text("marca");
		$marcaElement->setLabel("Marca: ");
		$marcaElement->setRequired(true);
		$tipoElement = new Zend_Form_Element_Text("tipo");
		$tipoElement->setLabel("Tipo: ");
		$piezElement = new Zend_Form_Element_Text("pieza");
		$piezElement->setLabel("Pieza: ");
		$piezElement->setRequired(true);
		$currency = array("multiOptions" => array("1" => "MX", "2" => "USD"));
		$monedaElement = new Zend_Form_Element_Select('moneda', $currency);
		$monedaElement->setLabel("Moneda: ");
		$monedaElement->setRequired(true);
		$costElement = new Zend_Form_Element_Text("cost");
		$costElement->setLabel("Costo Proveedor: $");
		$costElement->setRequired(true);

		//Create Text Validators
		$codIntElement->addValidator( new Zend_Validate_StringLength(6,20) );
		$codProvElement->addValidator( new Zend_Validate_Digits() );
		$modElement->addValidator( new Zend_Validate_StringLength(4,30) );
		$marcaElement->addValidator( new Zend_Validate_Alnum() );
		$marcaElement->addValidator( new Zend_Validate_StringLength(4,30) );
		$tipoElement->addValidator( new Zend_Validate_Alnum() );
		$tipoElement->addValidator( new Zend_Validate_StringLength(1,15) );
		$piezElement->addValidator( new Zend_Validate_Alnum() );
		$piezElement->addValidator( new Zend_Validate_StringLength(1,15) );
		$valid_cost = new Zend_Validate_Float();
		$valid_cost->setLocale('es_MX');
		$costElement->addValidator( $valid_cost );
		
		
		$codIntElement->addErrorMessage('Código Interno Requerido, Longitud: Max=20, Min=6');
		$codProvElement->addErrorMessage('Proveedor Requerido, Sólo Digitos');
		$modElement->addErrorMessage('Modelo requerido, Longitud: Max=30, Min=4');
		$marcaElement->addErrorMessage('Marca requerido, Longitud: Max=40, Min=5');
		$tipoElement->addErrorMessage('Tipo requerido, Longitud: Max=15, Min=1');
		$piezElement->addErrorMessage('Pieza requerido, Longitud: Max=15, Min=1');
		$costElement->addErrorMessage('Costo requerido, Solo Digitos');
		
		//Create All Filters for HTML TAGS
		$codIntElement->addFilter( new Zend_Filter_StripTags() );
		$codProvElement->addFilter( new Zend_Filter_StripTags() );
		$modElement->addFilter( new Zend_Filter_StripTags() );
		$marcaElement->addFilter( new Zend_Filter_StripTags() );
		$tipoElement->addFilter( new Zend_Filter_StripTags() );
		$piezElement->addFilter( new Zend_Filter_StripTags() );
		$costElement->addFilter( new Zend_Filter_StripTags() );
		
		//Create textarea elements
		$descElement = new Zend_Form_Element_Textarea("desc");
		$descElement->setLabel("Descripcion: ");
		$descElement->setRequired(true);
		//$etiqElement = new Zend_Form_Element_Textarea("etiq");
		//$etiqElement->setLabel("Etiquetado: ");
		//$etiqElement->setRequired(true);
		//$ubicElement = new Zend_Form_Element_Text("ubic");
		//$ubicElement->setLabel("Ubicacion: ");
		//$ubicElement->setRequired(true);
		$procElement = new Zend_Form_Element_Text("proc");
		$procElement->setLabel("Procedencia: ");
		$procElement->setRequired(true);
		
		//Vigencia Element
		$vigElement = new Zend_Form_Element_Text('vig');
		$vigElement->setLabel('Vigencia (Meses): ');
		$vigElement->addValidator(new Zend_Validate_Digits());
		$vigElement->setRequired(true);
		
		//Cantidad Element
		$minExist = array("multiOptions" => array('2'=>'2', '3'=>'3', '4'=>'4', '5'=>'5', '6'=>'6', '7'=>'7'));
	    $existElement = new Zend_Form_Element_Select('minexist',$minExist);
		$existElement->setLabel("Existencias (Mínimo): ");
		$existElement->setRequired(true);
		$existElement->addValidator( new Zend_Validate_Digits() );
				
		//Create TextArea Validators
		$descElement->addValidator( new Zend_Validate_StringLength(1,256) );
		//$etiqElement->addValidator( new Zend_Validate_StringLength(1,256) );
		//$ubicElement->addValidator( new Zend_Validate_StringLength(1,256) );		
		$procElement->addValidator( new Zend_Validate_StringLength(1,256) );		

		$descElement->addErrorMessage('Descripción requerido, Longitud: Max=256, Min=1');
		//$etiqElement->addErrorMessage('Etiquetado requerido, Longitud: Max=256, Min=1');		
		//$ubicElement->addErrorMessage('Ubicación requerido, Longitud: Max=256, Min=1');
		$procElement->addErrorMessage('Procedencia requerido, Longitud: Max=256, Min=1');
		
		// Create All Filters for HTML TAGS
		$descElement->addFilter( new Zend_Filter_StripTags() );
		//$etiqElement->addFilter( new Zend_Filter_StripTags() );
		//$ubicElement->addFilter( new Zend_Filter_StripTags() );
		$procElement->addFilter( new Zend_Filter_StripTags() );

		//Create the submit button
		$submitButtonElement = new Zend_Form_Element_Submit("submit");
		$submitButtonElement->setLabel("Agregar");
		
		//Add Elements to form
		$form->addElement($codIntElement)
		       ->addElement($codProvElement)
			   ->addElement($modElement)
			   ->addElement($marcaElement)
			   ->addElement($tipoElement)
			   ->addElement($piezElement)
			   ->addElement($monedaElement)
			   ->addElement($costElement)
			   ->addElement($descElement)
			   ->addElement($procElement)
			   ->addElement($vigElement)
			   ->addElement($existElement)
			   ->addElement($submitButtonElement);
		
		return $form;				
	}

    public function altaproductoAction()
    {
		$this->view->form = $this->getAddProdForm();
    }


	public function resultadosproductoAction(){
		$form = $this->getSearchProdForm();
		if($_POST['cod_int']||$_POST['desc_bus']){
			//Initialize the variables
			$cod_int = $_POST["cod_int"];
			$descrip = $_POST["desc_bus"];
								
			//Create a db object
			require_once APPLICATION_PATH ."/models/Db/Db_Db.php";
			$db = Db_Db::conn();
			
			if($_POST['cod_int']&&$_POST['desc_bus']){
				$query = "SELECT * FROM productos WHERE codigo_interno LIKE '%$cod_int%' and descripcion LIKE '%$descrip%' ";				
			} else {
				if($_POST['cod_int']){
					$query = "SELECT * FROM productos WHERE codigo_interno LIKE '%$cod_int%'";	
				}
				if($_POST['desc_bus']){ 
					$query = "SELECT * FROM productos WHERE descripcion LIKE '%$descrip%'";
				}
			}
			
			$res = $db->fetchAll($query);
			$this->view->results = $res;
			
		} else {
			$this->view->message = "Realiza una búsqueda válida";
			$this->_forward( 'busquedasproducto', 'Almacen', null, null );
		}	
	}
	
	public function getSearchProdForm()
	{
		$form = new Zend_Form();
		$form->setAction("resultadosproducto");
		$form->setMethod("post");
		$form->setName("addprodform");
		
		//Create text elements
		$codIntElement = new Zend_Form_Element_Text("cod_int");
		$codIntElement->setLabel("Codigo Interno: ");
		$codIntElement->setRequired(false);
		$descElement = new Zend_Form_Element_Text("desc_bus");
		$descElement->setLabel("Descripcion: ");
		$descElement->setRequired(false);
		$submitButtonElement = new Zend_Form_Element_Submit("submit");
		$submitButtonElement->setLabel("Enviar");	

		//Create Validators
		$codIntElement->addValidator( new Zend_Validate_StringLength(1,20) );
		$descElement->addValidator( new Zend_Validate_StringLength(0,256) );

		//Create Filters
		$codIntElement->addFilter( new Zend_Filter_StripTags() );
		$descElement->addFilter( new Zend_Filter_StripTags() );		

		$form->addElement($codIntElement);
		$form->addElement($descElement);
		$form->addElement($submitButtonElement);	
		return $form;	
	}
	
    public function busquedasproductoAction()
    {
        // action body
		$this->view->form = $this->getSearchProdForm();
    }

    public function visualizaproductoAction()
    {
        // action body
    	require_once APPLICATION_PATH ."/models/Db/Db_Db.php";
		$db = Db_Db::conn();
    	
		if(isset($_POST['accion'])){
			if($_POST['submit']=='Ejecutar'){
				if(isset($_POST['id_producto'])){
					$action=$_POST['accion'];
					$recordsToWork=$_POST['id_producto'];
					switch ($action){
						case 'delete':
							foreach($recordsToWork as $record){
								try{
									
									$query = "SELECT * FROM existencias WHERE id_producto=$record";
									$results = $db->fetchAll($query);
									foreach($results as $recordFounded){
										$existencia=$recordFounded['cantidad'];
									}
									if($existencia==0){
										$whereCond="id_producto=$record";
										$deleteExistences = $db->delete('existencias',$whereCond);
										$deleteCosts = $db->delete('costos_productos',$whereCond);
										$deletedRecord = $db->delete('productos',$whereCond);
										if($deletedRecord==1){
											$message='Eliminados Correctamente';
										} else {
											$message='Hubo Fallos al Borrar';
										}
									} else {
										$message = "Este producto aún tiene existencias, no es posible eliminarlo.";
									}
								} catch (Zend_Db_Exception $error){
									$message = $error->getMessage();
								}
							}
							break;
						default:
							break;
					}
					$this->view->message = $message;
				}
			}
		}
		
		$query = "SELECT * FROM existencias";
		$result = $db->fetchAll($query);
		$i=0;
		foreach($result as $existente){
			$id_producto=$existente['id_producto'];
			$query = "SELECT * FROM productos WHERE id_producto=$id_producto";
			$results = $db->fetchAll($query);
			
			foreach($results as $producto){
				$producto['existencia']=$existente['cantidad'];
				$objArray[$i] = $producto;				
			}
			$i++;
		}
		$this->view->existences = $objArray;
    }

	public function searchExistForm(){
		$form = new Zend_Form();
		$form->setAction("existencias");
		$form->setMethod("post");
		$form->setName("addprodform");
		
		//Create text elements
		$codIntElement = new Zend_Form_Element_Text("cod_int");
		$codIntElement->setLabel("Codigo Interno: ");
		$codIntElement->setRequired(true);
		$submitButtonElement = new Zend_Form_Element_Submit("submit_exist");
		$submitButtonElement->setLabel("Buscar");	

		$form->addElement($codIntElement);
		$form->addElement($submitButtonElement);	

		return $form;
	}

	public function existenciasAction(){
			
			require_once APPLICATION_PATH ."/models/Db/Db_Db.php";
			$db = Db_Db::conn();
				
			if($_POST['submit_exist'] == 'Buscar'){
				$formSearch = $this->searchExistForm();
				if($formSearch->isValid($_POST)):	
					$codigo = $_POST['cod_int'];
					$query = "SELECT * 
								FROM productos p
								JOIN existencias e ON p.id_producto = e.id_producto
								WHERE codigo_interno LIKE  '%$codigo%'";
				else:
					$this->view->message = "Ingresa Código Válido";
					$query = "SELECT * FROM existencias";
				endif;
			} else {
				$query = "SELECT * FROM existencias";			
			}		
		
			$this->view->form = $this->searchExistForm();
		
			$result = $db->fetchAll($query);
			$i=0;
			foreach($result as $existente){
				$id_producto=$existente['id_producto'];
				$query = "SELECT * FROM productos WHERE id_producto=$id_producto";
				$results = $db->fetchAll($query);
			
				foreach($results as $producto){
					$producto['existencia']=$existente['cantidad'];
					$objArray[$i] = $producto;				
				}
				$i++;
			}
			$this->view->existences = $objArray;
			if(!$objArray){
				$this->view->message = "No hay datos";
			}		
	}
	
	public function existenciasNoDispAction(){
		require_once APPLICATION_PATH ."/models/Db/Db_Db.php";
		$db = Db_Db::conn();
		$query = "SELECT * 
						FROM series_productos sp
						JOIN productos p ON sp.id_producto = p.id_producto
						WHERE id_status !=1;";
		$this->view->existences = $db->fetchAll($query);
	
	}
	
	public function menuAction(){
		// action body
	}
	
	public function agregaexistAction(){
		/*if(isset($_GET['cod_int'])){		
			$form = $this->seriesProdForm();
		} else {
			$form = $this->seriesProdForm();
		}*/
			
		$form = $this->seriesProdForm();
		
		if($form->isValid($_POST)){
			//Initialize the variables
			$cantidad = $form->getValue("canti");
			
			require_once APPLICATION_PATH ."/models/Db/Db_Db.php";
			$db = Db_Db::conn();
			
			if(isset($_GET['cod_int'])){
				$codigo = $_GET['cod_int'];
			} else {
				$codigo = $form->getValue("cod_int");
			}
			
			$query = "SELECT * FROM productos WHERE codigo_interno='$codigo'";
			$results = $db->fetchAll($query);
			
			foreach($results as $result){
				$id_producto = $result['id_producto'];
			}
			
			$query = "SELECT * FROM existencias WHERE id_producto=$id_producto";
			$results2 = $db->fetchAll($query);
			
			foreach($results2 as $result){
				$dbcantidad=$result['cantidad'];
			}
			
			$newCant = $cantidad+$dbcantidad;

			//agregar los números de serie
			for($i=0;$i<$cantidad;$i++){ 
				$serieName = 'serie'.$i;
				$serieToAdd = $_POST[$serieName];
				$almacenName = 'almacen'.$i;
				$almacen_num = $this->_request->getParam($almacenName);
				$zonaName = 'zona'.$i;
				$zona_ref = $this->_request->getParam($zonaName);
				$serieArray = array(
					'id_producto' => $id_producto,
					'numero_serie' => $serieToAdd,
					'almacen' => $almacen_num,
					'zona' => $zona_ref,
				);

				$insSerie = $db->insert('series_productos',$serieArray);
			}
			
			$condicion[] = "id_producto=$id_producto";
			$updates = array("cantidad" =>	$newCant );
			$updExis = $db->update("existencias",$updates,$condicion);
			
			$this->view->obj=$updExis;
		} else {
			$this->view->message = 'Deben llenados los campos obligatorios';
			$this->view->form = $form;
		}
	}
	
	public function getExistIndividualForm(){
		$form = new Zend_Form();
		//$form->setAction("agregaexist?cod_int=".$_GET['cod_int']);
		$form->setAction("existenciaind?cod_int=".$_GET['cod_int']);
		$form->setMethod("post");
		$form->setName("addexistencesI");

		$cantElement = new Zend_Form_Element_Text("canti");
		$cantElement->setLabel("Cantidad: ");
		$cantElement->setRequired(true);

		$submitButtonElement = new Zend_Form_Element_Submit("submit");
		$submitButtonElement->setAttrib('style','margin-bottom:10px;margin-top:-28px;margin-left:250px;position:absolute;');
		$submitButtonElement->setLabel("Integrar");	
		
		$form->addElement($cantElement);
		$form->addElement($submitButtonElement);
		return $form;
	}
	
	public function existenciaindAction(){
		$submit = $this->_request->getParam('submit');
		if($submit == 'Integrar'){
			$formSeriesProd = $this->seriesProdForm();
			$this->view->formSeriesProd = $formSeriesProd;
		}else{
			$this->view->form = $this->getExistIndividualForm();
			//$this->view->form = $this->getExistDirectaForm();
		}		
		
	}
	
	public function getExistDirectaForm(){
		$form = new Zend_Form();
		//$form->setAction("agregaexist");
		$form->setAction("existenciadir");
		$form->setMethod("get");
		$form->setName("addexistencesII");

		$codIntElement = new Zend_Form_Element_Text("cod_int");
		$codIntElement->setLabel("Codigo Interno: ");
		$codIntElement->setRequired(true);
		$cantElement = new Zend_Form_Element_Text("canti");
		$cantElement->setLabel("Cantidad: ");
		$cantElement->setRequired(true);

		$submitButtonElement = new Zend_Form_Element_Submit("submit");
		$submitButtonElement->setAttrib('style','margin-bottom:10px;margin-top:-28px;margin-left:250px;position:absolute;');
		$submitButtonElement->setLabel("Integrar");	
		
		$form->addElement($codIntElement);
		$form->addElement($cantElement);
		$form->addElement($submitButtonElement);
		return $form;
	}
	
	public function seriesProdForm(){
		$cod_prod = $this->_request->getParam('cod_int');
		$num_series = $this->_request->getParam('canti');
		if($num_series){	
			$form = new Zend_Form();
			$form->setAction('/almacen/agregaexist');
			$form->setMethod('post');
			$form->setName("agregaexist");
			
			$idElement = new Zend_Form_Element_Hidden('cod_int');
			$idElement->setValue($cod_prod);
			//adding class to element
			$idElement->addDecorators(array('viewHelper',array('HtmlTag',
								  array('tag' => 'dd', 'class' => 'noDisplay' ))));
			$cantElement = new Zend_Form_Element_Hidden('canti');
			$cantElement->setValue($num_series);
			$cantElement->addDecorators(array('viewHelper',array('HtmlTag',array('tag' => 'dd', 'class' => 'noDisplay' ))));
				
			for($i=0;$i<$num_series;$i++)
			{	//serieelement
				$serieElement = new Zend_Form_Element_Text('serie'.$i);			
				$serieElement->setLabel('Serie: ');
				$serieElement->setRequired(true);
				$nameElement = 'serie'.$i.'-element';
				$serieElement->setAttrib('style','position:absolute;margin-left: 50px;margin-top: -20px;width: 254px;');
				$serieElement->addDecorators(array(array('HtmlTag',
								  array('tag' => 'dd', 'id' => $nameElement, 'style' => 'height: 10px' ))));
			    $serieElement->addErrorMessage('No. de Serie es requerido');
				$form->addElement($serieElement);
				
				//almacen element
				$almacenElement = new Zend_Form_Element_Text('almacen'.$i);			
				$almacenElement->setLabel('Almacen: ');
				$almacenElement->addValidator(new Zend_Validate_Digits());
				$almacenElement->setRequired(true);
				$almacenElement->class ="prodSeriesInputAlmacen";
				$anameElement = 'almacen'.$i.'-element';
				$almacenElement->addDecorators(array(
								array('HtmlTag',array('tag' => 'dd', 'class' => 'prodSerieNoAlmacen-input' )),
								array('Label', array('tag' => 'dt', 'class' =>'prodSerieNoAlmacen-label')),
								  ));
			    $almacenElement->addErrorMessage('*');
			    $form->addElement($almacenElement);
			   
			   //zona element
			    $zonas = array("multiOptions" =>array("A"=>"A","B"=>"B"));
			    $zonaElement = new Zend_Form_Element_Select('zona'.$i,$zonas);		
				$zonaElement->setLabel('Zona: ');
				$zonaElement->setRequired(true);
				$znameElement = 'zona'.$i.'-element';
				$zonaElement->addDecorators(
								array(array('HtmlTag',array('tag' => 'dd', 'class' => 'prodSerieNoZona-input' )),
								array('Label', array('tag' => 'dt', 'class' =>'prodSerieNoZona-label')),
								  ));
			    $zonaElement->addErrorMessage('*');
			    $form->addElement($zonaElement);
				
			}
			
			$form->addElement($idElement);
			$form->addElement($cantElement);
		
			$submitElement = new Zend_Form_Element_Submit('submit');
			$submitElement->setLabel('Ingresar');
			$submitElement->setAttrib('style','margin-bottom:10px;margin-top:-18px;margin-left:215px;position:absolute;');
			$form->addElement($submitElement);
			return $form;
		} else {
			return NULL;
		}
		
	}

	public function existenciadirAction(){
		$submit = $this->_request->getParam('submit');
		if($submit == 'Integrar'){
			if(($_GET['cod_int']!="")&&($_GET['canti']!="")){
				$formSeriesProd = $this->seriesProdForm();
				$this->view->formSeriesProd = $formSeriesProd;
			} else {
				$this->view->form = $this->getExistDirectaForm();				
			}
		}else{
			$this->view->form = $this->getExistDirectaForm();
		}		
	}	
	
	public function verSeriesAction(){
		$id_producto = $this->_request->getParam('id_producto');
		
		require_once APPLICATION_PATH ."/models/Db/Db_Db.php";
		$db = Db_Db::conn();
		
		$query = "SELECT * FROM productos WHERE id_producto=$id_producto";
		$results = $db->fetchAll($query);
		
		foreach($results as $result){
			$query="SELECT * FROM series_productos WHERE id_producto=$id_producto";
			$series = $db->fetchAll($query);
			$i=0;
			if($series){
				foreach($series as $serie){
					$producto['id_proucto'] = $result['id_producto'];
					$producto['codigo_interno'] = $result['codigo_interno'];
					$producto['descripcion'] = $result['descripcion'];
					$producto['numero_serie'] = $serie['numero_serie'];
					$producto['almacen'] = $serie['almacen'];
					$producto['zona'] = $serie['zona'];
					
					//gettng the serie product status
					$id_status = $serie['id_status'];
					$data_statusProd = new Model_StatusProductos();
					$name_status = $data_statusProd->getNameFromId($id_status);
					$producto['status'] = $name_status['nombre_status'];
					
					$producto['comentario'] = $serie['comentario'];
					
					$objArray[$i]=$producto;
					$i++;
				}
				$this->view->info_producto = $results;
				$this->view->series = $objArray;
			} else {
				$this->view->message = "No hay existencias de este producto";
			}
		}
	}
	
	public function getSerieForm(){
		$form = new Zend_Form();
		$form->setAction("add-salida-productos");
		$form->setMethod("post");
		$form->setName("ordensalida");

		$serieElement = new Zend_Form_Element_Text("serie");
		$serieElement->setLabel("Serie: ");
		$serieElement->setRequired(true);

		$submitButtonElement = new Zend_Form_Element_Submit("submit");
		$submitButtonElement->setAttrib('style','margin-bottom:10px;margin-top:0px;margin-left:167px;position:absolute;');
		$submitButtonElement->setLabel("Agregar");	
		
		$form->addElement($serieElement);
		$form->addElement($submitButtonElement);
		return $form;
	}
	
	public function getOrdenSalidaForm($salidas){
		$form = new Zend_Form();
		$form->setAction("add-salida-productos");
		$form->setMethod("post");
		$form->setName("formsalida");
		$items=$salidas['items'];
		$itemsElement = new Zend_Form_Element_Hidden('items');
		$itemsElement->setValue($items);
		$itemsElement->addDecorators(array('viewHelper',array('HtmlTag',
						  array('tag' => 'dd', 'class' => 'noDisplay' ))));
		$form->addElement($itemsElement);
		if($salidas){
			for($ind=1;$ind<=$items;$ind++){
				$idProdElement = new Zend_Form_Element_Hidden('id_producto'.$ind);
				$idProdElement->addDecorators(array('viewHelper',array('HtmlTag',
								  array('tag' => 'dd', 'class' => 'noDisplay' ))));
				$codigo_internoElement = new Zend_Form_Element_Hidden('codigo_interno'.$ind);
				$codigo_internoElement->addDecorators(array('viewHelper',array('HtmlTag',
								  array('tag' => 'dd', 'class' => 'noDisplay' ))));
				$cantElement = new Zend_Form_Element_Hidden('cantidad'.$ind);
				$cantElement->addDecorators(array('viewHelper',array('HtmlTag',
								  array('tag' => 'dd', 'class' => 'noDisplay' ))));

				$idProdElement->setValue($salidas[$ind]['id_producto']); 
				$codigo_internoElement->setValue($salidas[$ind]['codigo_interno']);
				$cantElement->setValue($salidas[$ind]['cantidad']); 

				$form->addElement($idProdElement);
				$form->addElement($codigo_internoElement);
				$form->addElement($cantElement);
				
				$cantidad = $salidas[$ind]['cantidad'];
				for($x=1;$x<=$cantidad;$x++){
					$elementName = 'series'.$ind.'_'.$x;
					$seriesElement = new Zend_Form_Element_Hidden($elementName);
					$seriesElement->setValue($salidas[$ind]['serie'][$x]);
					$seriesElement->addDecorators(array('viewHelper',array('HtmlTag',
								array('tag' => 'dd', 'class' => 'noDisplay' ))));
					$form->addElement($seriesElement);
				}
			}
		}
				
		$submitButtonElement = new Zend_Form_Element_Submit("submit");
		$submitButtonElement->setAttrib('style','margin-bottom:10px;margin-top:-10px;margin-left:10px;position:absolute;');
		$submitButtonElement->setLabel("Generar Orden");	
		$cancelButtonElement = new Zend_Form_Element_Submit("cancel");
		$cancelButtonElement->setAttrib('style','margin-bottom:10px;margin-top:-10px;margin-left:750px;');
		$cancelButtonElement->setLabel("Cancelar Orden");	
		
		$form->addElement($cancelButtonElement);
		$form->addElement($submitButtonElement);
		
		return $form;
	}
	
	public function addSalidaProductosAction(){
		
		if(!isset($ordsal))
			$ordsal = new Zend_Session_Namespace('ordsal');
			
		if(isset($ordsal->items)){
			$hiddenForm = $this->getOrdenSalidaForm($_SESSION['ordsal']);					
			$this->view->form2 = $hiddenForm; 
			$this->view->productos = $_SESSION['ordsal'];
		}

		if($_POST['submit']=='Agregar'){
			$serie = $this->_request->getParam('serie');

			require_once APPLICATION_PATH ."/models/Db/Db_Db.php";
			$db = Db_Db::conn();
			
			$query = "SELECT * FROM series_productos WHERE numero_serie='$serie'";
			$results = $db->fetchAll($query);
			
			if($results){
	
				foreach($results as $result){ $id_producto = $result['id_producto']; $id_status = $result['id_status']; }
				if($id_status != 1):
					$this->view->message  = 'Número de Serie: ' . $serie . ' del Producto no tiene de estatus de Venta';
				else:
			
				$query = "SELECT * FROM productos WHERE id_producto=$id_producto";
				$results = $db->fetchAll($query);
				
				foreach($results as $result){ $codigo = $result['codigo_interno']; $desc = $result['descripcion']; }
			
				$query = "SELECT * FROM costos_productos WHERE id_producto=$id_producto";
				$results = $db->fetchAll($query);
				
				foreach ($results as $result){ $precio_unitario = $result['precio_unitario']; }
							
				$items = $_SESSION['ordsal']['items'];
				
				$founded=false;
				
				for($y=1;$y<=$items;$y++){
					if($codigo==$_SESSION['ordsal'][$y]['codigo_interno']){
						$cantidad=$_SESSION['ordsal'][$y]['cantidad'];
						$cantidad++;
						$_SESSION['ordsal'][$y]['cantidad']=$cantidad;
						$_SESSION['ordsal'][$y]['serie'][$cantidad] = $serie;
						$founded=true;	
					}
				}
				
				if(!$founded){
					$ordsal->items++;
					$items = $_SESSION['ordsal']['items'];
					$_SESSION['ordsal'][$items]['id_producto'] = $id_producto;
					$_SESSION['ordsal'][$items]['descripcion'] = $desc;
					$_SESSION['ordsal'][$items]['codigo_interno'] = $codigo;
					$_SESSION['ordsal'][$items]['cantidad'] = 1;
					$cantidad = $_SESSION['ordsal'][$items]['cantidad'];
					$_SESSION['ordsal'][$items]['serie'][$cantidad] = $serie;
					$_SESSION['ordsal'][$items]['unitario'] = $precio_unitario;
					$_SESSION['ordsal'][$items]['costo'] = $cantidad*$precio_unitario;
				}
				
				$hiddenForm = $this->getOrdenSalidaForm($_SESSION['ordsal']);
								
				$this->view->form2 = $hiddenForm; 
				$this->view->productos = $_SESSION['ordsal'];
				
				endif;
			} else {
				$this->view->message = 'Inserte un número de serie válido por favor';
			}
		}
		
		if($_POST['submit']=='Generar Orden'){
			$items = $this->_request->getParam('items');
			$id_user_creador = 1;
			$modelSalida = new Model_Salidas();
			$id_salida = $modelSalida->new_order($id_user_creador);
			
			for($ind=1;$ind<=$items;$ind++){
				$modelExistencia = new Model_Existencias();
				$id_producto = $this->_request->getParam('id_producto'.$ind);
				$cantReq = $this->_request->getParam('cantidad'.$ind);
			
				try{
					$existencias=$modelExistencia->findExistences($id_producto);
				} catch(Zend_Db_Exception $error){
					echo $error->getMessage();
				}
				
				foreach($existencias as $existencia){
					$new_cant = $existencia['cantidad'] - $cantReq;
				}
				
				try{
					$updCant = $modelExistencia->updExistences($id_producto, $new_cant);	
				} catch(Zend_Db_Exception $error){
					echo $error->getMessage();
				}
				
				for($x=1;$x<=$cantReq;$x++){
					$nameElement = 'series'.$ind.'_'.$x;
					$num_serie = $this->_request->getParam($nameElement);
					$productoSal = new Model_SalidasProductos();
					$numero_serie=$num_serie;

					try{
						$request = $productoSal->new_order($id_salida, $id_producto, $num_serie);
					} catch (Zend_Db_Exception $error){
						echo $error->getMessage();
					}
										
					$modelSeries = new Model_SeriesProductos();
					
					try{
						$series = $modelSeries->findSerie($id_producto, $numero_serie);	
					} catch (Zend_Db_Exception $error){
						echo $error->getMessage();
					}
					
					if($series){
						foreach($series as $serie){
							$id_serie = $serie['id_serie'];
						}
						echo 'id_serie=',$id_serie;
					} else {
						echo "No hay resultados";
					}
					
					try{
						$delSerie = $modelSeries->take_out($id_serie,$id_producto,$numero_serie);
					} catch (Zend_Db_Exception $error){
						$error->getMessage();
					}
				}
					
			}
			
			for($y=1;$y<$ordsal->items;$y++)
				unset($ordsal->$y);
			unset($ordsal->items);
		}

		if($_POST['cancel'] == 'Cancelar Orden'){
			for($y=1;$y<$ordsal->items;$y++)
				unset($ordsal->$y);
			unset($ordsal->items);
			$this->view->form2 = NULL;			
		}
		
		if($_GET['eliminar']){
			if($_SESSION['ordsal']['items'] == $_GET['eliminar']){
				if($_SESSION['ordsal']['items'] == 1){
					$y=1;
					unset($ordsal->items);
					$this->view->productos = NULL;			
				} else {
					$y=$_GET['eliminar'];
					$ordsal->items--;					
				}
				unset($ordsal->$y);
			} else {
				//Ejecutar corrimiento en la sesión
				for($y=$_GET['eliminar'];$y<=(($ordsal->items)-1);$y++){
					$_SESSION['ordsal'][$y] = $_SESSION['ordsal'][$y+1];
				}
				$unsetItem = $ordsal->items;
				unset($ordsal->$unsetItem);
				$ordsal->items--;
			}
			$hiddenForm = $this->getOrdenSalidaForm($_SESSION['ordsal']);					
			$this->view->form2 = $hiddenForm; 
			$this->_redirect("/almacen/add-salida-productos");
		}
		
		$this->view->form = $this->getSerieForm();
	}
	
	public function delOrdenSalidaAction(){
		$id_orden_salida = $this->_request->getParam('id_orden');
		
		if($id_orden_salida != ""){
			$modelOrden = new Model_Salidas();
			$request = $modelOrden->delete_order($id_orden_salida);
			if($request){
				$this->view->message = 'Borrado exitosamente';
			} else {
				$this->view->message = 'No fue posible borrar el registro';
			}
		}
	}
	
	public function updOrdenSalidaAction(){
		$id_orden_venta = $this->_request->getParam('id_orden_venta');
		$id_orden_salida = $this->_request->getParam('id_orden_salida');
		if($id_orden_venta != 0){
			$modelOrden = new Model_Salidas();
			$request = $modelOrden->update_order($id_orden_salida, $id_orden_venta);
			if($request){
				$this->view->message = 'Actualizado exitosamente';
			} else {
				$this->view->message = 'No fue posible actualizar el registro';
			}
		}
	}
	
	public function listaOrdenSalidaAction(){
		$salidasModel = new Model_Salidas();
		$results = $salidasModel->viewAll();
		
		$i=0;
		foreach($results as $result){
			$id_user = $result['id_user_creador'];
			$usersModel = new Model_User();
			$users = $usersModel->getUserName($id_user);
			$userName = $users['username'];
			
			$objeto['id_salida'] = $result['id_salida'];
			$objeto['id_orden_venta'] = $result['id_orden_venta'];
			$objeto['id_user_creador'] = $result['id_user_creador'];
			$objeto['fecha'] = $result['fecha'];
			$objeto['username'] = $userName;
			$objArray[$i] = $objeto;
			$i++;
		}
		
		$this->view->salidas = $objArray;
	}
	
	public function productosOrdenSalidaAction(){
		$id_salida = $this->_request->getParam('id_salida');
		$salidasProductosModel = new Model_SalidasProductos();
		$productosModel = new Model_Productos();
		
		$productosSalida = $salidasProductosModel->getSalidaProductos($id_salida);
		$i=0;
		foreach($productosSalida as $productoSalida){
			$id_producto = $productoSalida['id_producto'];
			if(isset($objArray)){
				
				$founded = false;
				$x = 0;
				foreach ($objArray as $array){
					if($array['id_producto']==$id_producto){
						$founded = true;
						$indice = $x;
					}
					$x++;
				}	
				
				if($founded){
					$cant = $objArray[$indice]['cantidad']; 
					$cant++;
					$objArray[$indice]['cantidad'] = $cant;
					$objArray[$indice]['series'].= ",".$productoSalida['num_serie'];
				} else {
					$object['cantidad'] = 1;
					$object['id_producto'] = $producto['id_producto'];
					$object['codigo'] = $producto['codigo_interno'];
					$object['descripcion'] = $producto['descripcion'];
					$object['series'] = $productoSalida['num_serie'];
					$objArray[$i] = $object;
				}
			} else {
				$producto = $productosModel->getProduct($id_producto);
				$object['cantidad'] = 1;
				$object['id_producto'] = $producto['id_producto'];
				$object['codigo'] = $producto['codigo_interno'];
				$object['descripcion'] = $producto['descripcion'];
				$object['series'] = $productoSalida['num_serie'];
				$objArray[$i] = $object;
			}
			$i++;
		}
		$this->view->lista = $objArray;
		$this->view->id_orden = $this->_request->getParam('id_salida');
	}
	
	public function serieProductoForm(){
		$form = new Zend_Form();
		$form->setAction("estatus-productos");
		$form->setMethod("post");
		$form->setName("serieproducto");

		$serieElement = new Zend_Form_Element_Text("serieprod");
		$serieElement->setLabel("Serie: ");
		$serieElement->addErrorMessage("");
		//$serieElement->addValidator(new Zend_Validate_Alnum());
		$serieElement->setRequired(true);

		$submitButtonElement = new Zend_Form_Element_Submit("submit");
		$submitButtonElement->setLabel("Ver");	
		$submitButtonElement->setAttrib('style','margin-left:167px;position:relative;');
		
		$form->addElement($serieElement);
		$form->addElement($submitButtonElement);
		return $form;
	
	}
	
	public function changeStatusSerieProdForm($result_data){
		/*
		$numero_serie = $this->_request->getParam("serieprod");
		$data_serie = new Model_SeriesProductos();
		$result_data = $data_serie->infoSerie($numero_serie);*/
					
		$form = new Zend_Form();
		$form->setAction("estatus-productos");
		$form->setMethod("post");
		$form->setName("statusSerieProd");
		
		$numeroSerieProdElement = new Zend_Form_Element_Hidden('numero_serie');
		$numeroSerieVal = $result_data['numero_serie'];
		$numeroSerieProdElement->setValue($numeroSerieVal);
		$numeroSerieProdElement->addDecorators(array('viewHelper',array('HtmlTag',
								  array('tag' => 'dd', 'class' => 'noDisplay' ))));
		$numeroSerieProdElement->removeDecorator('label')->removeDecorator('HtmlTag');
		
		$idSerieProdElement = new Zend_Form_Element_Hidden('id_serie');
		$idSerieVal = $result_data['id_serie'];
		$idSerieProdElement->setValue($idSerieVal);
		$idSerieProdElement->addDecorators(array('viewHelper',array('HtmlTag',
								  array('tag' => 'dd', 'class' => 'noDisplay' ))));
		$idSerieProdElement->removeDecorator('label')->removeDecorator('HtmlTag');
		
		
		$almacenElement = new Zend_Form_Element_Text("almacen");
		$almacenElement->addValidator(new Zend_Validate_Digits());
		$almacenVal = $result_data['almacen'];
		$almacenElement->setValue($almacenVal);
		$almacenElement->setLabel("Almacen: ");
		$almacenElement->setAttrib("style","width:26px");
		$almacenElement->setRequired(true);
		
		$zonas = array("multiOptions" =>array("A"=>"A","B"=>"B"));
		$zonaElement = new Zend_Form_Element_Select('zona',$zonas);
		$zonaVal = $result_data['zona'];
		$zonaElement->setValue($zonaVal);
		$zonaElement->setLabel('Zona: ');
		$zonaElement->setRequired(true);
		
		$status = array("multiOptions" =>array("1" =>"VENTA", 
												"2" => "PRESTAMO CONGRESO",
											    "3" =>"MUESTRA", 
											    "4" =>"DEVOLUCION POR DEFECTO",
												"5" => "REPARACION",
												"6" => "MERMAS",
												"7" => "BLOQUEADO"));
		$statusElement = new Zend_Form_Element_Select('id_status',$status);
		$statusVal = $result_data['id_status'];
		$statusElement->setValue($statusVal);
		$statusElement->setLabel("Status: ");
		$statusElement->setRequired(true);
		
		$seminuevoElement = new Zend_Form_Element_Checkbox('seminuevo');
		$seminuevoElement->setLabel("Seminuevo: ");
		$seminuevoVal = $result_data['seminuevo'];
		if($seminuevoVal == 1){$seminuevoElement->setChecked(1); }else{ $seminuevoElement->setChecked(0); }
		
		$comentarioElement = new Zend_Form_Element_Textarea('comentario');
		$comentarioElement->setLAbel("Comentario: ");
		$comentarioElement->addFilter(new Zend_Filter_HtmlEntities());
		$comentarioElement->addFilter(new Zend_Filter_StripTags());
		$comentarioVal = $result_data['comentario'];
		if($comentarioVal != NULL){ $comentarioElement->setValue($comentarioVal); }
		$comentarioElement->setAttrib('style','width:220px;height:140px');
		
		$submitButtonElement = new Zend_Form_Element_Submit("submit");
		$submitButtonElement->setLabel("Actualizar");	
		$submitButtonElement->setAttrib('style','margin-left:167px;position:relative;');
		
		$form->addElement($idSerieProdElement);
		$form->addElement($numeroSerieProdElement);
		$form->addElement($almacenElement);
		$form->addElement($zonaElement);
		$form->addElement($statusElement);
		$form->addElement($seminuevoElement);
		$form->addElement($comentarioElement);
		$form->addElement($submitButtonElement);
		return $form;
	}
	
	public function estatusProductosAction(){
		$form1 = $this->serieProductoForm();
		
		if($this->_request->getParam('submit')){
			
			if($this->_request->getParam('submit') == "Ver")
			{
				if($form1->isValid($_POST)){
					$numero_serie = $this->_request->getParam("serieprod");
					$data_serie = new Model_SeriesProductos();
					$result_data = $data_serie->infoSerie($numero_serie);
					if($result_data){
					    //$this->view->info_serie = $result_data;
						$this->view->form2 = $this->changeStatusSerieProdForm($result_data);
					}else{
						$this->view->messageUser = "Número de Serie no existe";
						$this->view->form1 = $form1;
					}
				}else{
					$this->view->messageUser = "Número de Serie no válido";
					$this->view->form1 = $form1;
				}
			}elseif($this->_request->getParam('submit') == "Actualizar"){
				$id_serie = $this->_request->getParam('id_serie');
				if(isset($id_serie)){
					$numero_serie = $this->_request->getParam("numero_serie");
					$data_serie = new Model_SeriesProductos();
					$result_data = $data_serie->infoSerie($numero_serie);
					$form2 = $this->changeStatusSerieProdForm($result_data);
					$this->view->form2 = $form2;
					if($form2->isValid($_POST)){
						
						$almacen = $this->_request->getParam('almacen');
						$zona = $this->_request->getParam('zona');
						$id_status = $this->_request->getParam('id_status');
						$seminuevo = $this->_request->getParam('seminuevo');
						$comentario = $this->_request->getParam('comentario');
						$data_serie = new Model_SeriesProductos();
						$result_data = $data_serie->updateSerieProd($id_serie,$almacen,$zona,$id_status,$seminuevo,$comentario);
						if($result_data){ $this->view->messageUser = "Actualización exitósa"; }else{$this->view->messageUser = "Error al actualizar"; }
					}else{ 
							$this->view->messageUser = "Ingrese Información Válida"; 
							
					}
				}else{ $this->view->messageUser = "Ingrese Información válida"; }
			}
		
		}else{
			//show the serieProdForm
			$this->view->form1 = $form1;
		}		
	}
		
}
