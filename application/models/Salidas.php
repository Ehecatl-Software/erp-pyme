<?php
class Model_Salidas extends Zend_Db_Table_Abstract{
	protected $_name = 'salidas';
	
	public function new_order($id_user_creador){
		$data = array(
			'id_orden_venta' => 0,
			'id_user_creador' => $id_user_creador
		);		
			
			return $this->insert($data);
	}
	
	public function update_order($id_salida, $id_orden_venta){
		$data = array(
				'id_orden_venta' => $id_orden_venta 
		);
		
		$where = $this->getAdapter()->quoteInto('id_salida = ?', $id_salida);
		return $this->update($data, $where);
	}
	
	public function delete_order($id_salida){
		$where = $this->getAdapter()->quoteInto('id_salida = ?', $id_salida);
		return $this->delete($where); //the number of rows deleted
	}
	
	public function viewAll(){
		return $this->fetchAll($this->select());
	}
}