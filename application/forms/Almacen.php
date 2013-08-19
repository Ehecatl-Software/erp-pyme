<?php
class Form_Almacen extends Zend_Form{
		
		public function form_Series_Prod($cod_prod,$num_series){

				// create hidden element 
				$id = $this->createElement('hidden','cod_prod'); 
        		$id->setValue($cod_prod);
				$this->addElement($id); 
        		
        		$num=$num_series;

        		for ($i=0;$i<$num;$i++){
        			$serieElement = $this->createElement('text','serie'.$i);
        			$serieElement->setLabel('Serie'.$i.':');
        			$serieElement->setRequired(true);
        			$serieElement->addFilter('StripTags');
        			$serieElement->addErrorMessage('Serie es requerido');
        			$this->addElement($serieElement); 
        		}
        				
				//submit button
				$submitElement = $this->addElement('submit', 'submit',array('label'=>'actualizar'));
		
			
		}
	}
?>
