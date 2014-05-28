<?php
namespace Autenticacao;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\Authentication\Storage;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Adapter\DbTable as DbTableAuthAdapter;

class Module
{
    public function onBootstrap($e)
    {
    	
        $e->getApplication()->getEventManager()->getSharedManager()->attach('Zend\Mvc\Controller\AbstractActionController', 'dispatch', function($e)
        {
        	$controller = $e->getTarget();
        	$controllerClass = get_class($controller);
        	$moduleNamespace = substr($controllerClass, 0, strpos($controllerClass, '\\'));
        	$config = $e->getApplication()->getServiceManager()->get('config');
        	if (isset($config['module_layouts'][$moduleNamespace])) {
        		$controller->layout($config['module_layouts'][$moduleNamespace]);
        	}
        }
        , 100);
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
    			'factories'=>array(
    					'Autenticacao\Model\AutenticacaoStorage' => function($sm){
    						return new \Autenticacao\Model\AutenticacaoStorage('db_projeto4');
    					},
    					 
    					'AuthService' => function($sm) {
    						$dbAdapter           = $sm->get('Zend\Db\Adapter\Adapter');
    						$dbTableAuthAdapter  = new DbTableAuthAdapter($dbAdapter,
    								'tb_usuario','login','senha', 'MD5(?)');
    						 
    						$authService = new AuthenticationService();
    						$authService->setAdapter($dbTableAuthAdapter);
    						$authService->setStorage($sm->get('Autenticacao\Model\AutenticacaoStorage'));
    
    						return $authService;
    					},
    			),
    	);
    }
    

}