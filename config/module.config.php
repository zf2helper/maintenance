<?php

return array(
    'controllers' => array(
        'invokables' => array(
            'Maintenance\Controller\Index' => 'Maintenance\Controller\IndexController',
        ),
    ),
    'controller_plugins' => array(
        'invokables' => array(
            'MaintenanceManagment' => 'Maintenance\Controller\Plugin\MaintenanceConfig',
        )
    ),
    'view_manager' => array(
        'template_map' => array(
            'layout/maintenance' => __DIR__ . '/../view/layout/maintenance.phtml',
            'maintenance/config' => __DIR__ . '/../view/maintenance/config/config.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
    'maintenance' => array(
        'enable' => false,
        'http_code' => 503,
        'message' => 'Service temporary unavailable',
        'layout' => 'layout/maintenance',
        'forward_route' => array(
            'controller' => 'Maintenance\Controller\Index',
            'action' => 'index',
        ),
        'allowed_route' => array(
        ),
        'storage' => array(
            'type' => 'json', // 'json', 'sqlite3'
            'path' => '',
            'filename' => 'maintenance.db',
        ),
    ),
);