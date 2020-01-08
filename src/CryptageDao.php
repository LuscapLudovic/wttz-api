<?php


namespace SilexApi;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception\InvalidArgumentException;
use mysql_xdevapi\Exception;

class CryptageDao
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
        $sql = "SELECT * FROM CRYPTAGE";
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
        $sql = "SELECT id, text, team_id FROM CRYPTAGE WHERE id=?";
        $row = $this->getDb()->fetchAssoc($sql, array($id));

        if($row){
            return $this->buildDomainObject($row);
        } else {
            throw new \Exception("pas de user avec cette id : " .$id);
        }
    }
}
