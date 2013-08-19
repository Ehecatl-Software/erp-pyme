<?php
class Model_productosOrdenesCompras extends Zend_Db_Table_Abstract {
	protected $_name = 'productos_orden_compras';
	
	
	public function add_Product_To_Order($id_producto,$id_orden_compra,$cantidad){
			
				$data = array(
								'id_producto' => $id_producto,
								'id_orden_compra' => $id_orden_compra,
								'cantidad' => $cantidad);
				
				$this->insert($data);
	}

}