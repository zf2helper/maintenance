<?php

namespace Maintenance\Controller;

use Zend\Mvc\Controller\AbstractActionController,
    Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController
{
    public function indexAction()
    {
        $maintenanceConfig = $this->serviceLocator->get('maintenance_config');

        if (!$this->getRequest()->isXmlHttpRequest()){
            $this->layout()->setTemplate($maintenanceConfig['layout']);
            $view = new ViewModel(array(
                 'message' => $maintenanceConfig['message'],
            ));
        } else {
            $view = new \Zend\View\Model\JsonModel(array(
                'error' => $maintenanceConfig['message']
            ));
        }

        return $view;
    }

}
