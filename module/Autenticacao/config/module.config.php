<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Autenticacao\Controller\Auth' => 'Autenticacao\Controller\AuthController',
            'Autenticacao\Controller\Deny' => 'Autenticacao\Controller\DenyController',
        ),
    ),
    'module_layouts' => array(
    		'Autenticacao' => 'layout/login',
    ),
		'router' => array(
				'routes' => array(
						'autenticar' => array(
								'type'    => 'segment',
								'options' => array(
										'route'    => '/autenticar[/][:action][/:id]',
										'constraints' => array(
												'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
												'id'     => '[0-9]+',
										),
										'defaults' => array(
												'controller' => 'Autenticacao\Controller\Auth',
												'action'     => 'login',
										),
								),
						),
				    
				    'sair' => array(
				    		'type'    => 'segment',
				    		'options' => array(
				    				'route'    => '/sair[/][:action][/:id]',
				    				'constraints' => array(
				    						'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
				    						'id'     => '[0-9]+',
				    				),
				    				'defaults' => array(
				    						'controller' => 'Autenticacao\Controller\Auth',
				    						'action'     => 'logout',
				    				),
				    		),
				    ),

				),
		),
    'view_manager' => array(
        'template_path_stack' => array(
            'Autenticacao' => __DIR__ . '/../view',
        ),
    ),
);