<?php

namespace Maintenance\Controller;

use Zend\Mvc\Controller\AbstractActionController,
    Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController
{
    public function indexAction()
    {
        $maintenanceConfig = $this->serviceLocator->get('maintenance_config');

        $this->layout()->setTemplate($maintenanceConfig['layout']);
        $view = new ViewModel(array(
             'message' => $maintenanceConfig['message'],
        ));

        return $view;
    }

}