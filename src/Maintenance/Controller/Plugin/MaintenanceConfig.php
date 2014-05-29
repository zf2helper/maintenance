<?php

namespace Maintenance\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin,
    Zend\View\Model\ViewModel;

use Maintenance\Form\Config as ConfigForm;

class MaintenanceConfig extends AbstractPlugin
{  
    public function __invoke()
    {
        $controller = $this->getController();
        $sm = $controller->getServiceLocator();
        
        $configModel = $sm->get('Maintenance\Model\Config');
        
        $request = $controller->getRequest();
        if($request->isGet() && $controller->params()->fromQuery('drop_db', false)){
            $configModel->remove();
            return $controller->redirect()->toUrl($request->getUri()->getPath());
        }
        
        $form = new ConfigForm($sm);
        $form->setData($configModel->toForm());
        
        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $currentRouteParams = $controller->getEvent()->getRouteMatch()->getParams();
                $configModel->saveChanges(array_merge_recursive($form->getData(),
                    array('allowed_route' => array($currentRouteParams))));
            }
        }

        $view = new ViewModel(array(
            'form' => $form,
        ));
        $view->setTemplate('maintenance/config');
        return $view;
    }
}
