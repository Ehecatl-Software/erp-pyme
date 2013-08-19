<?php
class Model_ordenesCompras extends Zend_Db_Table_Abstract{
	
	protected $_name = 'ordenes_compras';

	public function create_order($numorden, $id_user_creador, $id_user_autoriza, $estado){
		$data = array( 
			'numorden' => $numorden,
			'id_user_creador' => $id_user_creador,
			'id_user_autoriza' => $id_user_autoriza,
			'estado' => $estado,
		);
		return $this->insert($data);
	}
	
	public function get_Info_From_Numorden($numorden){
		$where = $this->getAdapter()->quoteInto('numorden = ?', $numorden);
		return $this->fetchRow($where);
	}

	public function update_order($id_orden_compra, $monto, $estado){
		$data = array(
			'monto' => $monto,
			'estado' => $estado
		);
		$where = $this->getAdapter()->quoteInto('id_orden_compra = ?', $id_orden_compra);
		return $this->update($data, $where);
	}
	
}