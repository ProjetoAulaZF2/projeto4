<?php
namespace Celular\Model;

use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;

class CelularTable
{

    protected $tableGateway;

    const ATIVO = 1;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll()
    {
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }

    public function getCelular($id)
    {
        $id = (int) $id;
        $rowset = $this->tableGateway->select(array(
            'id' => $id
        ));
        $row = $rowset->current();
        if (! $row) {
            throw new \Exception("Não existe linha no banco para este id $id");
        }
        return $row;
    }

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

    public function deletarCelular($id)
    {
        $this->tableGateway->delete(array(
            'id' => $id
        ));
    }
}