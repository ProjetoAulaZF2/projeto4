<?php
namespace Celular\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Celular\Model\Celular;          // <-- adicione essa linha
use Celular\Form\CelularForm;       // <-- adicione essa linha

class IndexController extends AbstractActionController
{
    protected $celularTable;
    
    public function indexAction()
    {
        return new ViewModel(array(
            'celulares' => $this->getCelularTable()->fetchAll(),
        ));
    }
    
    public function getCelularTable()
    {
    	if (!$this->celularTable) {
    		$sm = $this->getServiceLocator();
    		$this->celularTable = $sm->get('Celular\Model\CelularTable');
    	}
    	return $this->celularTable;
    }
    
    public function addAction()
    {
    	$form = new CelularForm();
    
    	$request = $this->getRequest();
    	if ($request->isPost()) {
    		$celular = new Celular();
    		$form->setInputFilter($celular->getInputFilter());
    		$form->setData($request->getPost());
    	
    		if ($form->isValid()) {
    			$celular->exchangeArray($form->getData());
    			$this->getCelularTable()->salvarCelular($celular);
    
    			return $this->redirect()->toRoute('celular');
    		}
    	}
    	return array('form' => $form);
    }
    
    public function editAction()
    {
    	$id = (int) $this->params()->fromRoute('id', 0);
    	
    	if (empty($id))
    	{
    		$id = $this->getRequest()->getPost('id');
    		if (empty($id)) {
    			return $this->redirect()->toUrl('add');
    		}
    	}
    	
    	try {
    		$celular = $this->getCelularTable()->getCelular($id);
    	}
    	catch (\Exception $ex) {
    		return $this->redirect()->toRoute('celular', array( 
    				'action' => 'index'
    		));
    	}
    
    	$form  = new CelularForm();
    	$form->bind($celular);
    
    	$request = $this->getRequest();
    	if ($request->isPost()) {
    		$form->setInputFilter($celular->getInputFilter());
    		$form->setData($request->getPost());
    
    		if ($form->isValid()) {
    			$this->getCelularTable()->salvarCelular($form->getData());
    
    			return $this->redirect()->toRoute('celular');
    		}
    	}
    
    	return array(
    			'id' => $id,
    			'form' => $form,
    	);
    }
    
    public function deleteAction()
    {
    	$id = (int) $this->params()->fromRoute('id', 0);
    	if (!$id) {
    		return $this->redirect()->toRoute('celular');
    	}
    
    	$request = $this->getRequest();
    	if ($request->isPost()) {
    		$del = $request->getPost('del', 'Nao');
    
    		if ($del == 'Sim') {
    			$id = (int) $request->getPost('id');
    			$this->getCelularTable()->deletarCelular($id);
    		}
    
    		return $this->redirect()->toRoute('celular');
    	}
    
    	return array(
    			'id'    => $id,
    			'celular' => $this->getCelularTable()->getCelular($id)
    	);
    }
}
