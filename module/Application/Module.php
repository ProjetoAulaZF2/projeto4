<?php
namespace Application;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
        
        $application = $e->getApplication();
        $sm = $application->getServiceManager();
        
        
        if (!$sm->get('AuthService')->hasIdentity()) {
            $e->getApplication()
            ->getEventManager()
            ->attach('route', array(
            $this,
            'verificaRota'
        ));
        }
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
    
    public function verificaRota(MvcEvent $e)
    {
        $route = $e->getRouteMatch()->getMatchedRouteName();
        
        if ( $route != "autenticar" ) {
        	$response = $e->getResponse();
        	$response -> getHeaders() -> addHeaderLine('Location', $e -> getRequest() -> getBaseUrl() . '/autenticar/');
        	$response -> setStatusCode(404);
        	$response->sendHeaders ();exit;
        }
    }
}
