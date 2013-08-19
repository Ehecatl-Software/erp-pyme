<?php

class Model_Productos extends Zend_Db_Table_Abstract{
	protected $_name = 'productos';

	public function getProduct($id_producto){
		$where = $this->getAdapter()->quoteInto('id_producto = ?', $id_producto);
		return $this->fetchRow($where);		
	}
	
	public function getProducts()
	{
		$where = $this->getAdapter()->quoteInto('id_producto > ?',0);
		$order = "id_producto ASC";
		return $this->fetchAll($where,$order);
	}
	
	
	public function getProductFromCodigo($codigo){
		$where = $this->getAdapter()->quoteInto('codigo_interno = ?', $codigo);
		return $this->fetchRow($where);
	}
	
	public function getProductFromDescription($descrip){
		$where = $this->getAdapter()->quoteInto('descripcion = ?',$descrip);
		return $this->fetchRow($where);
	}
	
	public function getProductFromCodigoandDesc($codigo,$descrip){
		$where = $this->getAdapter()->quoteInto('codigo_interno = ? AND descripcion = ?', $codigo, $descrip);
		return $this->fetchRow($where);
	}
	
}