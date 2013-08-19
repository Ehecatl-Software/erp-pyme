<?php
class Model_Existencias extends Zend_Db_Table_Abstract{
	protected $_name = 'existencias';
	
	public function findExistences($id_producto){
		return $this->fetchAll($this->select()->where('id_producto = ?', $id_producto));
	}
	
	public function updExistences($id_producto,$new_cant){		
		$data = array(
				'cantidad' => $new_cant 
		);
		
		$where = $this->getAdapter()->quoteInto('id_producto = ?', $id_producto);
		return $this->update($data, $where);
	} 
	
	public function getExistences($id_producto){
		return $this->fetchAll($this->select());
	}
}
