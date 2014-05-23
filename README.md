Terceiro projeto das aulas de Zend Framework 2 com Nataniel Paiva
=======================

Introdução
------------

Esse terceiro projeto contempla os seguintes tópicos:

* Criar um formulário básico com o Zend\Form\Form
* CRUD da primeira tabela que criamos, no caso a tb_celular
* Validadores e filtros


Criação do formulário para adicionar registros
-----------------------------------------------

Primeiro vamos criar a nossa classe de formulário no arquivo projeto3/module/Celular/src/Celular/Form/CelularForm.php 
com o seguinte código:

	<?php
	namespace Celular\Form;

	use Zend\Form\Form;

	class CelularForm extends Form
	{
	    public function __construct($name = null)
	    {
		parent::__construct('celular');
		$this->setAttribute('method', 'post');
		$this->add(array(
		    'name' => 'id',
		    'type' => 'Hidden',
		));
		$this->add(array(
		    'name' => 'marca',
		    'type' => 'Text',
		    'options' => array(
		        'label' => 'Marca',
		    ),
		));
		$this->add(array(
		    'name' => 'modelo',
		    'type' => 'Modelo',
		    'options' => array(
		        'label' => 'Modelo',
		    ),
		));
		$this->add(array(
		    'name' => 'submit',
		    'type' => 'Submit',
		    'attributes' => array(
		        'value' => 'Salvar',
		        'id' => 'submitbutton',
		    ),
		));
	    }
	}


Confira se no seu arquivo de model que fica no caminho projeto3/module/Celular/src/Celular/Model/Celular.php 
tem as seguintes namespaces:

	use Zend\InputFilter\Factory as InputFactory;
	use Zend\InputFilter\InputFilter;
	use Zend\InputFilter\InputFilterAwareInterface;
	use Zend\InputFilter\InputFilterInterface;

Se não tiver, adicione juntamente com o seguinte código:

        public function setInputFilter(InputFilterInterface $inputFilter)
        {
          throw new \Exception("Não validado");
	}

Logo após coloque o código abaixo:

	    public function getInputFilter()
	    {
		if (!$this->inputFilter) {
		    $inputFilter = new InputFilter();
		    $factory     = new InputFactory();

		    $inputFilter->add($factory->createInput(array(
		        'name'     => 'id',
		        'required' => true,
		        'filters'  => array(
		            array('name' => 'Int'),
		        ),
		    )));

		    $inputFilter->add($factory->createInput(array(
		        'name'     => 'marca',
		        'required' => true,
		        'filters'  => array(
		            array('name' => 'StripTags'),
		            array('name' => 'StringTrim'),
		        ),
		        'validators' => array(
		            array(
		                'name'    => 'StringLength',
		                'options' => array(
		                    'encoding' => 'UTF-8',
		                    'min'      => 1,
		                    'max'      => 100,
		                ),
		            ),
		        ),
		    )));

		    $inputFilter->add($factory->createInput(array(
		        'name'     => 'modelo',
		        'required' => true,
		        'filters'  => array(
		            array('name' => 'StripTags'),
		            array('name' => 'StringTrim'),
		        ),
		        'validators' => array(
		            array(
		                'name'    => 'StringLength',
		                'options' => array(
		                    'encoding' => 'UTF-8',
		                    'min'      => 1,
		                    'max'      => 100,
		                ),
		            ),
		        ),
		    )));

		    $this->inputFilter = $inputFilter;
		}

		return $this->inputFilter;
	    }

Verifique também se sua classe possui o seguinte atributo:

	protected $inputFilter; 

Perfeito, com isso seu formulário já está meio caminho andado, agora vamos para a controller e depois para a view.
Na controller adicione o seguinte código, lembrando que o caminho da sua controller é
projeto3/module/Celular/src/Celular/Controller/IndexController.php.

	use Celular\Model\Celular;          // <-- adicione essa linha
	use Celular\Form\CelularForm;       // <-- adicione essa linha


E depois no mesmo arquivo, ou seja, na sua classe de controller coloque a sua Action add:

	public function addAction()
	    {
	    	$form = new CelularForm();
	    	$form->get('submit')->setValue('Add');
	    
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

Agora vamos criar um método de salvar o celular em nossa classe CelularTable que está no caminho
projeto3/module/Celular/src/Celular/Model/CelularTable.php vamos criar o método abaixo:

	public function salvarCelular(Celular $celular)
	    {
		$data = array(
		    'marca' => $celular->marca,
		    'modelo' => $celular->modelo,
		    'ativo' => CelularTable::ATIVO,
		);
		
		$id = (int) $celular->id;
		if ($id == 0) {
		    $this->tableGateway->insert($data);
		} else {
		    if ($this->getCelular($id)) {
		        $this->tableGateway->update($data, array(
		            'id' => $id
		        ));
		    } else {
		        throw new \Exception('Não existe registro com esse ID' . $id);
		    }
		}
	    }

Por último em seu add.phtml vamos colocar o código:

	<?php

	$title = 'Cadastrar um novo celular';
	$this->headTitle($title);
	?>
	<h1><?php echo $this->escapeHtml($title); ?></h1>
	<?php
	$form = $this->form;
	$form->setAttribute('action', $this->basePath('celular/index/add'));
	$form->prepare();

	echo $this->form()->openTag($form);
	echo $this->formHidden($form->get('id'));
	echo $this->formRow($form->get('marca'));
	echo $this->formRow($form->get('modelo'));
	echo $this->formSubmit($form->get('submit'));
	echo $this->form()->closeTag();
Pronto! Criamos nosso primeiro formulário de cadastro de celulares.
Agora vamos criar um CRUD.

CRUD completo em Zend Framework 2
------------------------------------

Como já fizemos o cadastro, o resto fica muito fácil, pois já configuramos o nossos Zend\Form\Form.
Agora vamos criar a nossa Action de editar nossos registros e como tudo no ZF2 isso é muito simples.
Vamos criar uma Action exclusivamente para realizar edição no caminho 
projeto3/module/Celular/celular/view/index/edit.phtml com o seguinte código:

	<?php
	$title = 'Editar o celular';
	$this->headTitle($title);
	?>
	<h1><?php echo $this->escapeHtml($title); ?></h1>
	<?php
	$form = $this->form;
	$form->setAttribute('action', $this->basePath('celular/index/edit'));
	$form->prepare();
	?>
	<div class="page-header">
	<?php echo $this->form()->openTag($form); ?>
	<?php echo $this->formHidden($form->get('id'));?>
	    <div class="form-group">
	    	<label for="marca" class="col-sm-2 control-label">Marca</label>
	    	<div class="col-sm-3">
	    		<?php echo $this->formRow($form->get('marca')); ?>
	    	</div>
	    </div>
	    <div class="form-group">
	    	<label for="modelo" class="col-sm-2 control-label">Modelo</label>
	    	<div class="col-sm-3">
	    		<?php echo $this->formRow($form->get('modelo')); ?>
	    	</div>
	    </div>
	    <div class="form-group">
	    	<div class="col-sm-offset-2 col-sm-4">
	    		<?php echo $this->formSubmit($form->get('submit')); ?>
	    	</div>
	    </div>
	<?php echo $this->form()->closeTag();?>
	</div>

Já que fizemos o template, agora vamos criar o nosso método de Action em nossa IndexController.php.
Adicione o seguinte método:

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


Certifique-se que o seu index.phtml que é o arquivo que exibe sua listagem tenha o seguinte código html:

	<a href="<?php echo $this->basePath('celular/index/edit/' . $celular->id) ?>"><span class="glyphicon glyphicon-pencil"></span> Editar</a>


Pronto! Agora também podemos editar nosso cadastro de celulares, então vamos para o último passo que é deletar os registros.
Também temos que criar uma template delete.phtml com o seguinte código:

	<?php
	$title = 'Excluir Celular';
	$this->headTitle($title);
	?>
	<h1><?php echo $this->escapeHtml($title); ?></h1>

	<p>Você tem certeza que vai deletar esses registros:
	    '<?php echo $this->escapeHtml($celular->marca); ?>' 
	    '<?php echo $this->escapeHtml($celular->modelo); ?>'?
	</p>
	<form action="<?php echo $this->basepath("celular/index/delete/{$this->id}"); ?>" method="post">
	<div>
	    <input type="hidden" name="id" value="<?php echo (int) $celular->id; ?>" />
	    <input type="submit" name="del" value="Sim" />
	    <input type="submit" name="del" value="Nao" />
	</div>
	</form>

E por último vamos criar nosso método deleteAction em nossa IndexController.php com o seguinte código:

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

Pronto! Finalizamos o nosso primeiro CRUD em ZF2!
Muito simples né? No próximo projeto vamos ver sobre autenticação de usuários.














