<?php

return array(
    'router' => array(
        'routes' => array(
           
            'celular' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/celular',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Celular\Controller',
                        'controller'    => 'Index',
                        'action'        => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/[:controller[/:action]][/:id]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id'         => '[0-9]+',
                            ),
                            'defaults' => array(
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
    'service_manager' => array(
    		'abstract_factories' => array(
    				'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
    				'Zend\Log\LoggerAbstractServiceFactory',
    		),
    		'aliases' => array(
    				'translator' => 'MvcTranslator',
    		),
    ),
    'controllers' => array(
        'invokables' => array(
            'Celular\Controller\Index' => 'Celular\Controller\IndexController',
            'Celular\Controller\Aula' => 'Celular\Controller\AulaController'
        ),
    ),
   'view_manager' => array(
        'template_path_stack' => array(
            'Celular' => __DIR__ . '/../view',
        ),
    ),
);
