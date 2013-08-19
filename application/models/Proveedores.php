<?php
class Model_Proveedores extends Zend_Db_Table_Abstract{
	protected $_name = 'proveedores';
	
	public function getAll(){
		return $this->fetchAll($this->select());
	}
}
