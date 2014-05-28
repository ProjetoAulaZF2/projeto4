<?php
namespace Usuario\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Usuario
{

    public $id;

    public $nome;

    public $email;

    public $login;

    public $senha;

    public $ativo;

    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->id = (isset($data['id'])) ? $data['id'] : null;
        $this->nome = (isset($data['nome'])) ? $data['nome'] : null;
        $this->email = (isset($data['email'])) ? $data['email'] : null;
        $this->login = (isset($data['login'])) ? $data['login'] : null;
        $this->senha = (isset($data['senha'])) ? $data['senha'] : null;
        $this->ativo = (isset($data['ativo'])) ? $data['ativo'] : null;
    }

    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Não validado");
    }

    public function getInputFilter()
    {
        if (! $this->inputFilter) {
            $inputFilter = new InputFilter();
            $factory = new InputFactory();
            
            $inputFilter->add($factory->createInput(array(
                'name' => 'id',
                'required' => false,
                'filters' => array(
                    array(
                        'name' => 'Int'
                    )
                )
            )));
            
            $inputFilter->add($factory->createInput(array(
                'name' => 'nome',
                'required' => true,
                'filters' => array(
                    array(
                        'name' => 'StripTags'
                    ),
                    array(
                        'name' => 'StringTrim'
                    )
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 1,
                            'max' => 100
                        )
                    )
                )
            )));
            
            $this->inputFilter = $inputFilter;
        }
        
        return $this->inputFilter;
    }

    public function getArrayCopy()
    {
       return array(
            'id'    => $this->id,
            'nome'  => $this->nome,
            'email' => $this->email,
            'login' => $this->login,
            'ativo' => $this->ativo
        );
    }
}
