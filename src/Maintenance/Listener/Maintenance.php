<?php

namespace Maintenance\Listener;

use Zend\EventManager\EventManagerInterface,
    Zend\EventManager\ListenerAggregateInterface,
    Zend\Mvc\MvcEvent;

class Maintenance implements ListenerAggregateInterface
{
    protected $listeners = array();

    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_DISPATCH, array($this, 'mvcPreDispatch'), 900);
    }

    public function detach(EventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $listener) {
            if ($events->detach($listener)) {
                unset($this->listeners[$index]);
            }
        }
    }

    /**
     * MVC preDispatch Event
     *
     * @param \Zend\Mvc\MvcEvent $event
     * @return mixed
     */
    public function mvcPreDispatch(MvcEvent $event)
    {
        $sm = $event->getTarget()->getServiceManager();
        $maintenanceEvent = $sm->get('Maintenance\Event\Maintenance');
        return $maintenanceEvent->preDispatch($event);
    }
}