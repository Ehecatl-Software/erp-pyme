<?php
class Form_Products extends Zend_Dojo_Form
{
	public $_selectOptions;
    public function init()
    {
        $this->_selectOptions=array(
                '1' => 'red',
                '2' => 'blue',
                '3' => 'gray'
            );
            
        $this->setMethod('post');
        $this->setAttribs(array(
                'name' => 'masterform'
            ));
        
$this->setDecorators(array(

            'FormElements',

            array('TabContainer', array(

                'id' => 'tabContainer',

                'style' => 'width: 600px; height: 500px;',

                'dijitParams' => array(

                    'tabPosition' => 'top'

                ),

            )),

            'DijitForm',
        ));
             
        $toggleForm= new Zend_Dojo_Form_SubForm();
        $toggleForm->setAttribs(array(
                    'name' => 'toggletab',
                    'legend' => 'Toggle Elements',
                ));
        $toggleForm->addElement(
                'NumberSpinner',
                'ns',
                array(
                    'value' => '7',
                    'label' => 'NumberSpinner',
                    'smallDelta' => 5,
                    'largeDelta' => 25,
                    'defaultTimeout' => 1000,
                    'timeoutChangeRate' => 100,
                    'min' => 9,
                    'max' => 1550,
                    'places' => 0,
                    'maxlength' => 20,
                )
            );
        $toggleForm->addElement(
            'Button',
            'dijitButton',
            array(
                'label' => 'Button',
            )
        );
        $toggleForm->addElement(
            'CheckBox',
            'checkbox',
            array(
                'label' => 'CheckBox',
                'checkedValue' => 'foo',
                'uncheckedValue' => 'bar',
                'checked' => true,
            )
        );
        $selectForm= new Zend_Dojo_Form_SubForm();
        $selectForm->setAttribs(array(
                    'name' => 'selecttab',
                    'legend' => 'Select Elements',
                ));
        $selectForm->addElement(
            'FilteringSelect',
            'filterselect',
            array(
                'label' => 'FilteringSelect(select)',
                'storeId' => 'productStore',
				'storeType' => 'dojo.data.ItemFileReadStore',
				'storeParams' => array('url' => 'getproducts'),
				'dijitParams' => array('searchAttr' => 'descripcion'),
            )
        );
        
        $this->addSubForm($selectForm,'selectForm');
    }
	/*
	public function init(){
		$this->setMethod('post');
		$form = new Zend_Dojo_Form_SubForm();
		$form->setAttribs(array(
						'name' => 'products',	
				));		
		
		$form->addElement(
				'FilteringSelect',
				'filteringselectremote',
				array(
					'label' => 'Producto: ',
					'storeId' => 'productStore',
					'storeType' => 'dojo.data.ItemFileReadStore',
					'storeParams' => array('url' => '/compras/getproducts'),
					'dijitParams' => array('searchAttr' => 'descripcion'),
				)
		);
		$this->addSubForm($form);
	}*/
}