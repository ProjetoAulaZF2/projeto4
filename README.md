Quarto projeto das aulas de Zend Framework 2 com Nataniel Paiva
=======================

Introdução
------------

Esse quarto projeto contempla os seguintes tópicos:

* Criar uma autenticação utilizando a tb_usuario
* Configurar os módulos para que só permita a navegação de usuário autenticado.



Tabela de autenticação
-----------------------------------------------

Vamos começar criando uma tabela para realizarmos nossa autenticação.
Vamos criar a tb_usuario.
O banco para o nosso projeto será o sequinte script:

	CREATE SCHEMA IF NOT EXISTS `db_projeto4` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ;

	CREATE TABLE IF NOT EXISTS `db_projeto4`.`tb_celular` (
	  `id` INT(11) NOT NULL AUTO_INCREMENT,
	  `marca` VARCHAR(100) NOT NULL,
	  `modelo` VARCHAR(100) NOT NULL,
	  `ativo` TINYINT(4) NULL DEFAULT NULL,
	  PRIMARY KEY (`id`))
	ENGINE = InnoDB
	DEFAULT CHARACTER SET = utf8
	COLLATE = utf8_general_ci;

	CREATE TABLE IF NOT EXISTS `db_projeto4`.`tb_usuario` (
	  `id` INT(11) NOT NULL AUTO_INCREMENT,
	  `nome` VARCHAR(100) NOT NULL,
	  `email` VARCHAR(100) NOT NULL,
	  `login` VARCHAR(20) NOT NULL,
	  `senha` VARCHAR(32) NOT NULL,
	  `ativo` TINYINT(4) NOT NULL DEFAULT 1,
	  PRIMARY KEY (`id`))
	ENGINE = InnoDB
	DEFAULT CHARACTER SET = utf8
	COLLATE = utf8_general_ci;


	INSERT INTO `db_projeto4`.`tb_celular` (`marca`, `modelo`, `ativo`) VALUES ('Samsung', 'Galaxy 5', '1');
	INSERT INTO `db_projeto4`.`tb_celular` (`id`, `marca`, `modelo`, `ativo`) VALUES ('', 'Motorola', 'Moto G', '1');
	INSERT INTO `db_projeto4`.`tb_celular` (`id`, `marca`, `modelo`, `ativo`) VALUES ('', 'Nokia', 'Lumia', '1');

	INSERT INTO `db_projeto4`.`tb_usuario` (`nome`, `email`, `login`, `senha`) VALUES ('Nataniel Paiva', 'nataniel.paiva@gmail.com', 'nataniel.paiva', md5('123'));


Nesse projeto irá conter mais um CRUD de usuários, porém não irei mostrar esse exemplo aqui.
Vamos nos focar em criar a autenticação.

Primeiro vamos criar o módulo Autenticacao e em seu arquivo Module.php coloque o seguinte código:

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


Depois vamos criar as rotas em nosso arquivo module.config.php com o seguinte código:

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

Depois teremos mais configurações no aquivo Module.php do módulo Application, mas vamos primeiro criar as classes necessárias no
módulo de Autenticacao.
Primeiro vamos criar a nossa model Autenticacao no seguinte caminho projeto4/module/Autenticacao/src/Autenticacao/Model/AutenticacaoStorage.php
com o seguinte código:

	<?php
	namespace Autenticacao\Model;
	 
	use Zend\Authentication\Storage;
	 
	class AutenticacaoStorage extends Storage\Session
	{
	    public function setRememberMe($rememberMe = 0, $time = 1209600)
	    {
		 if ($rememberMe == 1) {
		     $this->session->getManager()->rememberMe($time);
		 }
	    }
	     
	    public function forgetMe()
	    {
		$this->session->getManager()->forgetMe();
	    } 
	}

Depois vamos para a nossa controller AuthController.php, acredito que não precisa eu dizer o caminho, certo?
Segue o código da classe:

	<?php
	namespace Autenticacao\Controller;


	use Zend\Mvc\Controller\AbstractActionController;
	use Zend\Form\Annotation\AnnotationBuilder;
	use Zend\View\Model\ViewModel;
	use Application\Controller;

	class AuthController extends AbstractActionController
	{

	    protected $storage;
	    protected $authservice;
	    protected $usuarioTable;

	    public function getAuthService()
	    {
		if (! $this->authservice) {
		    $this->authservice = $this->getServiceLocator()
		                              ->get('AuthService');
		}

		return $this->authservice;
	    }

	    public function getSessionStorage()
	    {
		if (! $this->storage) {
		    $this->storage = $this->getServiceLocator()
		                          ->get('Autenticacao\Model\AutenticacaoStorage');
		}

		return $this->storage;
	    }

	    public function loginAction()
	    {

		if ($this->getAuthService()->hasIdentity()){
		   // return $this->redirect()->toRoute('success');
		}
	    }

	    public function autenticarAction()
	    {
		$redirect = 'autenticar';
		$request = $this->getRequest();

		if ($request->isPost()){
		        //Verifica autenticacao
		        $this->getAuthService()->getAdapter()
		                               ->setIdentity($request->getPost('login'))
		                               ->setCredential($request->getPost('senha'));

		        $result = $this->getAuthService()->authenticate();
		        foreach($result->getMessages() as $message)
		        {
		            $this->flashmessenger()->addMessage($message);
		        }
		        if ($result->isValid()) {
		            $redirect = 'home';
		            if ($request->getPost('rememberme') == 1 ) {
		                $this->getSessionStorage()
		                     ->setRememberMe(1);
		                $this->getAuthService()->setStorage($this->getSessionStorage());
		            }

		            $usuarioLogado  = $this->getUsuarioTable()->getUsuarioIdentidade($request->getPost('autenticar'));
		            $this->getAuthService()->setStorage($this->getSessionStorage());
		            $this->getAuthService()->getStorage()->write($usuarioLogado);
		        }

		}

		return $this->redirect()->toRoute($redirect);
	    }

	    public function logoutAction()
	    {
		$this->getSessionStorage()->forgetMe();
		$this->getAuthService()->clearIdentity();

		$this->flashmessenger()->addMessage("Você acabou de sair do sistema");
		return $this->redirect()->toRoute('autenticar');
	    }

	    public function getUsuarioTable()
	    {
	    	if (!$this->usuarioTable)
	    	{
	    		$sm = $this->getServiceLocator();
	    		$this->usuarioTable = $sm->get('Usuario\Model\UsuarioTable');
	    	}
	    	return $this->usuarioTable;
	    }
	}
Ainda no módulo de Autenticacao, vamos criar o nosso layout para nossa tela de login no seguinte arquivo
projeto4/module/Autenticacao/src/Autenticacao/view/layout/login.phtml com o seguinte código:

	<?php echo $this->doctype(); ?>

	<html lang="en">
	    <head>
		<meta charset="utf-8">
		<?php echo $this->headTitle($this->translate('Login'))->setSeparator(' - ')->setAutoEscape(false) ?>

		<?php echo $this->headMeta()
		    ->appendName('viewport', 'width=device-width, initial-scale=1.0')
		    ->appendHttpEquiv('X-UA-Compatible', 'IE=edge')
		?>

		<!-- Le styles -->
		<?php echo $this->headLink(array('rel' => 'shortcut icon', 'type' => 'image/vnd.microsoft.icon', 'href' => $this->basePath() . '/img/favicon.ico'))
		                ->prependStylesheet($this->basePath() . '/css/style.css')
		                ->prependStylesheet($this->basePath() . '/css/bootstrap-theme.min.css')
		                ->prependStylesheet($this->basePath() . '/css/bootstrap.min.css') ?>

		<!-- Scripts -->
		<?php echo $this->headScript()
		    ->prependFile($this->basePath() . '/js/bootstrap.min.js')
		    ->prependFile($this->basePath() . '/js/jquery.min.js')
		    ->prependFile($this->basePath() . '/js/respond.min.js', 'text/javascript', array('conditional' => 'lt IE 9',))
		    ->prependFile($this->basePath() . '/js/html5shiv.js',   'text/javascript', array('conditional' => 'lt IE 9',))
		; ?>

	    </head>
	    <body>
		<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
		    <div class="container">
		        <div class="navbar-header">
		            <img src="<?php echo $this->basePath('img/zf2-logo.png') ?>" alt="Zend Framework 2"/>&nbsp;<?php echo $this->translate('Nataniel Paiva') ?></a>
		        </div>
		    </div>
		</nav>
		<div class="container">
		    <?php echo $this->content; ?>
		    <hr>
		    <footer>
		        <p>&copy; 2005 - <?php echo date('Y') ?> by Zend Technologies Ltd. <?php echo $this->translate('All rights reserved.') ?></p>
		    </footer>
		</div> <!-- /container -->
		<?php echo $this->inlineScript() ?>
	    </body>
	</html>

Pronto!Agora está quase pronta nossa autenticação, faltando apenas criar uma configuração em nosso módulo Application, para que somente o usuário que estiver logado
consiga navegar nos menus do sistema.
No arquivo projeto4/module/Application/Module.php coloque o seguinte código:

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

Perfeito! Agora temos um projeto com autenticação via banco de dados mysql, é lógico que poderíamos utilizar de várias outras formas de autenticação,
por arquivo, LDAP ou etc...
Mas como é só o ponta pé inicial, espero que esse projeto ajude em nossas aulas presenciais.
Agora em nosso próximo projeto, ou seja, o projeto 5 irei implementa além da autenticação o ACL, continue seguindo os projetos que você irá muito bem em nossas
aulas presenciais de Zend Framework 2.










