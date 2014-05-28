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