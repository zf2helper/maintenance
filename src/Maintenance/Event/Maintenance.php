<?php

/**
 * @namespace
 */

namespace Maintenance\Event;

/**
 * @uses Zend\Mvc\MvcEvent
 */
use Zend\Mvc\MvcEvent as MvcEvent,
    Zend\Mvc\Router\RouteMatch;

/**
 * Authentication Event Handler Class
 *
 * This Event Handles Maintenance
 *
 */
class Maintenance
{
    protected $enable = false;
    protected $config = null;

    public function __construct($config) {
        if($config['enable']){
            $this->enable = true;
            $this->config = $config;
        }
    }

    /**
     * preDispatch Event Handler
     *
     * @param \Zend\Mvc\MvcEvent $event
     * @throws \Exception
     */
    public function preDispatch(MvcEvent $event) 
    {
        //Skip work, if no config 
        if($this->enable === false){
            return;
        }
        
        //Get and check route params;
        $routeParams = $event->getRouteMatch()->getParams();
        $routeName = $event->getRouteMatch()->getMatchedRouteName();
        if(count($this->config['allowed_route']) > 0){
            foreach($this->config['allowed_route'] as $allowedRoute){
                if(is_array($allowedRoute)
                    && $routeParams['controller'] == $allowedRoute['controller']
                    && $routeParams['action'] == $allowedRoute['action']){
                    return;
                } elseif(is_string($allowedRoute)
                        && $routeName == $allowedRoute){
                    return;
                }
            }
        }
        
        $event->getResponse()->setStatusCode($this->config['http_code']);
        $event->setRouteMatch(new RouteMatch($this->config['forward_route']));
    }

}
