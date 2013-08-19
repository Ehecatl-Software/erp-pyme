<?php
class Model_SeriesProductos extends Zend_Db_Table_Abstract {
	protected $_name = 'series_productos';
	
	public function new_serie($id_producto,$numero_serie){
		$data = array('id_producto' => $id_producto,'numero_serie' => $numero_serie);
		return $this->insert($data);
	}

	public function findSerie($id_producto, $numero_serie){
		return $this->fetchAll($this->select()->where('id_producto = ?',$id_producto)->where('numero_serie = ?', $numero_serie));
	}
	
	public function take_out($id_serie){
		$where = $this->getAdapter()->quoteInto('id_serie = ?', $id_serie);
		return $this->delete($where);  
	}
	
	public function infoSerie($numero_serie){
		return $this->fetchRow($this->select()->where('numero_serie = ?',$numero_serie));
	}
	
	public function updateSerieProd($id_serie,$almacen,$zona,$id_status,$seminuevo,$comentario){
		$data = array(
				'almacen'	=> $almacen,
				'zona'	    => $zona,
				'id_status'	=> $id_status,
				'seminuevo' => $seminuevo,
				'comentario' => $comentario,
			 );
		
		$where = $this->getAdapter()->quoteInto('id_serie = ?', $id_serie);
		return $this->update($data, $where);
	}
}