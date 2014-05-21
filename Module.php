<?php

namespace Maintenance;

use Zend\Mvc\MvcEvent,
    Zend\ServiceManager\ServiceManager;
use Maintenance\Listener\Maintenance as MaintenanceListener,
    Maintenance\Event\Maintenance as MaintenanceEvent,
    Maintenance\Model\Config as MaintenanceConfigModel;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $application = $e->getApplication();
        $eventManager = $application->getEventManager();
        $maintenanceListener = new MaintenanceListener();
        $maintenanceListener->attach($eventManager);
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }
    
    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'Maintenance\Event\Maintenance' => function(ServiceManager $sm) {
                    $maintenanceConfig = $sm->get('maintenance_config');
                    return new MaintenanceEvent($maintenanceConfig);
                },
                'Maintenance\Model\Config' => function(ServiceManager $sm) {
                    $appConfig = $sm->get('config');
                    return new MaintenanceConfigModel($appConfig['maintenance']);
                },   
                'maintenance_config' => function(ServiceManager $sm){
                    $maintenanceConfigModel = $sm->get('Maintenance\Model\Config');
                    return $maintenanceConfigModel->getConfig();
                }
            ),
        );
    }

}