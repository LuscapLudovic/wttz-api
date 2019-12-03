<?php


namespace SilexApi;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception\InvalidArgumentException;
use mysql_xdevapi\Exception;

class UserDao
{
    private $db;

    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    protected function getDb()
    {
        return $this->db;
    }

    public function findAll()
    {
        $sql = "SELECT id, username FROM USER";
        $result = $this->getDb()->fetchAll($sql);
        $entities = array();
        foreach ($result as $row){
            $id = $row['id'];
            $entities[$id] = $this->buildDomainObject($row);
        }
        return $entities;
    }

    public function findById($id)
    {
        $sql = "SELECT id, username FROM USER WHERE id=?";
        $row = $this->getDb()->fetchAssoc($sql, array($id));

        if($row){
            return $this->buildDomainObject($row);
        } else {
            throw new \Exception("pas de user avec cette id : " .$id);
        }
    }

    public function connexion($username, $password)
    {
        $sql = "SELECT username FROM USER WHERE username=? AND password=?";
        $row = $this->getDb()->fetchAssoc($sql, array($username, $password));

        if($row){
            return true;
        } else {
            throw new \Exception("nom de commpte ou mot de passe incorrect");
        }
    }

    public function delete($id)
    {
        try {
            $this->getDb()->delete('user', array('id' => $id));
        } catch (InvalidArgumentException $e) {
            echo $e;
        }
    }

    protected function buildDomainObject($row)
    {
        $user = new User();
        $user->setId($row['id']);
        $user->setUsername($row['username']);

        return $user;
    }
}
