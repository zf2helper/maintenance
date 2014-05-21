<?php

namespace Maintenance\Form\Validator;

class LayoutAvailable extends \Zend\Validator\AbstractValidator
{
    protected $sm;
    protected $messageTemplates = array(
        'not_found' => "Layout with name %value% is not found",
    );

    function __construct($sm)
    {
        $this->sm = $sm;
        parent::__construct();
    }

    public function isValid($value)
    {
        $this->setValue($value);

        $resolver = $this->sm->get('viewtemplatemapresolver');
        if ($resolver->resolve($value) === false) {
            $this->error('not_found');
            return false;
        }

        return true;
    }

}