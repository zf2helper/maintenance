<?php
namespace Maintenance\Form;

use Zend\Form\Form,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory;

use Maintenance\Form\Validator\LayoutAvailable as LayoutValidator;

class Config extends Form
{
    protected $routesNames = array();
    protected $sm = null;
    public $inputFilter = null;

    public function __construct($sm)
    {
        $routeNames = array();
        foreach($sm->get('router')->getRoutes() as $routeName => $routeParams){
            $routeNames[] = $routeName;
        }
        $this->sm = $sm;
        $this->routesNames = $routeNames;
        parent::__construct('maintenance_config');
        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'MaintenanceForm');
        $this->addElements();
    }

    public function addElements()
    {
        $this->add(array(
            'name' => 'enable',
            'type' => 'Checkbox',
            'options' => array(
                'label' => 'Enable Maintenance Status',
                'use_hidden_element' => true,
                'checked_value' => true,
                'unchecked_value' => false
            ),
        ));
        $this->add(array(
            'name' => 'http_code',
            'type' => 'Number',
            'options' => array(
                'label' => 'HTTP Responce code',
            ),
        ));
        $this->add(array(
            'name' => 'message',
            'type' => 'Text',
            'options' => array(
                'label' => 'Http Responce message',
            ),
        ));
        $this->add(array(
            'name' => 'layout',
            'type' => 'Text',
            'options' => array(
                'label' => 'Maintenance page layout',
            ),
        ));
        // Checkboxes for available routes
        $routeList = array();
        foreach($this->routesNames as $routeName){
            $routeList[$routeName] = $routeName;
        }
        $this->add(array(
            'name' => 'allowed_route',
            'type' => 'select',
            'attributes' => array(
                'multiple' => 'multiple',
            ),
            'options' => array(
                'label' => 'List of allowed route',
                'value_options' => $routeList,
            )
        ));
        //Submit button
        $this->add(array(
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Save Config',
                'id' => 'submitButton',
                'class' => 'btn btn-primary'
            ),
        ));
        
        $this->add(array(
            'name' => 'clear',
            'type' => 'Button',
            'options' => array(
                'label' => 'Reset Config',
            ),
            'attributes' => array(
                'value' => 'Reset Config',
                'id' => 'dropButton',
                'onClick' => 'removeConfig()',
                'class' => 'btn btn-danger'
            ),
        ));
    }

    public function getInputFilter()
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();
            $factory = new InputFactory();
            
            $inputFilter->add($factory->createInput(array(
                    'name' => 'enable',
                    'required' => false,
                    'validators' => array(
                        array(
                            'name' => 'InArray',
                            'options' => array(
                                'haystack' => array('0', '1'),
                            )
                        ),
                    ),
            )));
            
            $inputFilter->add($factory->createInput(array(
                    'name' => 'http_code',
                    'required' => false,
                    'validators' => array(
                        array(
                            'name' => 'InArray',
                            'options' => array(
                                'haystack' => array('503', '418'),
                            )
                        ),
                    ),
            )));
            
            $inputFilter->add($factory->createInput(array(
                    'name' => 'message',
                    'required' => false,
                    'filters' => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'StringLength',
                            'options' => array(
                                'encoding' => 'UTF-8',
                                'min' => 5,
                            ),
                        ),
                    ),
            )));
            
            $layoutValidator = new LayoutValidator($this->sm);
            $inputFilter->add($factory->createInput(array(
                    'name' => 'layout',
                    'required' => false,
                    'filters' => array(
                        array('name' => 'StripTags'),
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array($layoutValidator),
            )));
            
            $inputFilter->add($factory->createInput(array(
                    'name' => 'allowed_route',
                    'required' => false,
//                    'validators' => array(
//                        array(
//                            'name' => 'InArray',
//                            'options' => array(
//                                'haystack' => $this->routesNames,
//                                'recursive' => true,
//                            )
//                        ),
//                    ),
            )));

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }

}
