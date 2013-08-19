<?php
class Model_SalidasProductos extends Zend_Db_Table_Abstract {
	protected $_name = 'salidas_productos';
	
	public function new_order($id_salida,$id_producto,$num_serie){
		$data = array('id_salida' => $id_salida, 'id_producto' => $id_producto,'num_serie' => $num_serie);
		
		return $this->insert($data);
	}
	
	public function update_order($id_salida, $id_producto, $num_serie){
		$data = array('num_serie' => $num_serie);
		
		$where = $this->getAdapter()->quoteInto('id_producto = ? AND id_salida = ?', $id_producto, $id_salida);
		return $this->update($data, $where);
	}
	
	public function delete_order($id_salida,$id_producto,$num_serie){
		$where = $this->getAdapter()->quoteInto('id_salida = ? AND id_producto = ? AND num_serie = ?', $id_salida, $id_producto, $num_serie);
		return $this->delete($where); //the number of rows deleted
	}
	
	public function getSalidaProductos($id_salida){
		$where = $this->getAdapter()->quoteInto('id_salida = ?',$id_salida);
		return $this->fetchAll($where);
	}
	
}